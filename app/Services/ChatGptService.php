<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use RuntimeException;

class ChatGptService
{
    protected string $openaiKey;
    protected string $model;

    public function __construct()
    {
        $this->openaiKey = config('services.openai.key') ?: env('OPENAI_API_KEY', '');
        $this->model = config('services.openai.model', 'gpt-5-mini'); // configurable
    }

    /**
     * Extrae texto de un PDF. Requiere 'pdftotext' en el sistema o Smalot\PdfParser instalado.
     */
    public function extractTextFromPdf(string $path): string
    {
        if (!file_exists($path)) {
            throw new RuntimeException("PDF not found: {$path}");
        }

        // prefer system pdftotext if available
        $which = trim(shell_exec('which pdftotext 2>/dev/null'));
        if ($which) {
            $tmp = tempnam(sys_get_temp_dir(), 'pdftxt_');
            // -layout para mantener disposición lo más fiel posible
            $cmd = escapeshellcmd($which) . ' -layout ' . escapeshellarg($path) . ' ' . escapeshellarg($tmp);
            @exec($cmd);
            $text = @file_get_contents($tmp) ?: '';
            @unlink($tmp);
            if ($text !== '') return $text;
        }

        // fallback to Smalot\PdfParser if available
        if (class_exists(\Smalot\PdfParser\Parser::class)) {
            $parser = new \Smalot\PdfParser\Parser();
            $pdf = $parser->parseFile($path);
            return (string) $pdf->getText();
        }

        throw new RuntimeException('No PDF text extractor available (install pdftotext or smalot/pdfparser).');
    }

    /**
     * Construye prompt para extraer los campos solicitados desde un bloque de texto y llama a OpenAI.
     * Retorna array: ['raw' => string, 'json' => array|null]
     */
    public function extractOmegaDataFromText(string $pageText, int $pageNumber = null): array
    {
        $pageHint = $pageNumber ? " (page: {$pageNumber})" : '';
        $prompt = <<<PROMPT
Eres un parser que extrae valores concretos de informes de laboratorio.
Del siguiente texto{$pageHint} extrae:
- "Equilibrio Omega3" en formato EXACTO "NN:NN" (dos números, dos dígitos separados por dos puntos). Key: equilibrio_omega3
- "Índice de omega" extraer porcentaje (ej. "12.5%") y una breve descripción asociada. Keys: indice_omega_percent, indice_omega_description

Devuelve únicamente un JSON válido con estas keys. Si algún valor no se encuentra, devuelve null para esa key.
Formato de salida (ejemplo):
{
  "equilibrio_omega3": "12:34",
  "indice_omega_percent": "12.5%",
  "indice_omega_description": "Descripción corta..."
}

Texto a analizar:
---
{$pageText}
---
IMPORTANTE: responde SOLO con JSON válido, sin comentarios ni texto adicional.
PROMPT;

        return $this->callOpenAiAndParseJson($prompt);
    }

    /**
     * Extrae texto del PDF y ejecuta la extracción sobre el texto completo.
     */
    public function extractOmegaDataFromPdf(string $pdfPath, int $pageNumber = null): array
    {
        $text = $this->extractTextFromPdf($pdfPath);
        // si se desea limitar por página, no implementado exhaustivamente aquí; enviamos todo y pedimos page hint
        return $this->extractOmegaDataFromText($text, $pageNumber);
    }

    /**
     * Llama a OpenAI Chat Completions y trata de decodificar JSON de la respuesta.
     */
    protected function callOpenAiAndParseJson(string $prompt): array
    {
        if (empty($this->openaiKey)) {
            throw new RuntimeException('OpenAI API key not configured (services.openai.key or OPENAI_API_KEY).');
        }

        $payload = [
            'model' => $this->model,
            'messages' => [
                ['role' => 'system', 'content' => 'Eres un asistente que responde estrictamente en JSON cuando se solicita.'],
                ['role' => 'user', 'content' => $prompt],
            ],
            'temperature' => 0.0,
            'max_tokens' => 800,
        ];

        try {
            $resp = Http::withToken($this->openaiKey)
                ->timeout(30)
                ->post('https://api.openai.com/v1/chat/completions', $payload);

            if (!$resp->successful()) {
                Log::error('OpenAI error', ['status' => $resp->status(), 'body' => $resp->body()]);
                throw new RuntimeException('OpenAI request failed: ' . $resp->status());
            }

            $body = $resp->json();
            $content = $body['choices'][0]['message']['content'] ?? ($body['choices'][0]['text'] ?? null);
            $content = trim((string) $content);

            // intentar extraer JSON: buscar primer { ... } válido
            $firstBrace = strpos($content, '{');
            $lastBrace = strrpos($content, '}');
            $json = null;
            if ($firstBrace !== false && $lastBrace !== false && $lastBrace > $firstBrace) {
                $candidate = substr($content, $firstBrace, $lastBrace - $firstBrace + 1);
                $decoded = json_decode($candidate, true);
                if (json_last_error() === JSON_ERROR_NONE) {
                    $json = $decoded;
                } else {
                    Log::warning('OpenAI returned invalid JSON', ['candidate' => $candidate]);
                }
            } else {
                Log::warning('OpenAI response contains no JSON', ['content' => $content]);
            }

            return [
                'raw' => $content,
                'json' => $json,
                'meta' => $body,
            ];
        } catch (\Throwable $e) {
            Log::error('Error calling OpenAI', ['exception' => $e->getMessage()]);
            throw $e;
        }
    }
}

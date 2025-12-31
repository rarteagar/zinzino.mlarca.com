<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Test;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Smalot\PdfParser\Parser;

class TestController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // Tests entered by the user (their own tests)
        $myTests = Test::where('client_id', $user->id)->latest()->get();

        // Tests of clients managed by the user
        $clientIds = $user->clients()->pluck('id');
        $clientTests = Test::whereIn('client_id', $clientIds)->latest()->get();

        return view('tests.index', compact('myTests', 'clientTests'));
    }

    public function create()
    {
        $user = Auth::user();
        $clients = $user->clients()->get();
        $hasSelf = $clients->where('is_self', true)->isNotEmpty();
        return view('tests.create', compact('clients', 'hasSelf'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();

        // Validación base del request (client_id puede ser 0 para indicar "nuevo cliente")
        $base = $request->validate([
            'client_id' => 'required|integer|min:0',
            'subject_age' => 'required|integer|min:0|max:150',
            'subject_height_cm' => 'required|integer|min:0|max:300',
            'subject_weight_kg' => 'required|numeric|min:0|max:500',
            'health_challenges' => 'nullable|string',
            'attachment' => 'required|file|mimes:pdf|max:10240', // max 10MB
        ]);

        // Si client_id == 0 -> crear cliente con campos provistos en el mismo POST
        $clientData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:50',
            'identifier' => 'nullable|string|max:100',
            'birthdate' => 'nullable|date',
            'height_cm' => 'nullable|integer|min:0|max:300',
            'weight_kg' => 'nullable|numeric|min:0|max:500',
            'is_self' => 'sometimes|boolean'
        ]);

        $clientData['user_id'] = $user->id;
        $client = Client::create($clientData);
        $clientId = $client->id;

        // reemplazo: obtener texto del PDF desde el UploadedFile (no asumir base64)
        $textPdf = null;
        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            try {
                $parser = new Parser();
                $pdf = $parser->parseFile($file->getRealPath());
                $textPdf = (string) $pdf->getText();
            } catch (\Throwable $e) {
                \Log::error('PDF parse error: ' . $e->getMessage());
                $textPdf = '';
            }
        } else {
            $textPdf = '';
        }

        $test = new Test();
        $test->user_id = Auth::id();
        $test->client_id = $clientId;
        $test->subject_age = $base['subject_age'];
        $test->subject_height_cm = $base['subject_height_cm'];
        $test->subject_weight_kg = $base['subject_weight_kg'];
        $test->health_challenges = $base['health_challenges'] ?? null;
        $test->pdf_text = $textPdf;
        $test->status = 'Registrado';
        $test->save();

        // Store PDF as "test_{id}.pdf" on the "public" disk under "tests/"
        if ($request->hasFile('attachment')) {
            $path = $test->storePdf($request->file('attachment'));
        }
        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['success' => true, 'test' => $test, 'client' => $client ?? null], 201);
        }
        return redirect()->route('tests.index')->with('success', 'Prueba creada correctamente.');
    }


    public function update(Request $request)
    {
        $user = Auth::user();

        // Validación base del request (client_id puede ser 0 para indicar "nuevo cliente")
        $base = $request->validate([
            'client_id' => 'required|integer|min:0',
            'subject_age' => 'required|integer|min:0|max:150',
            'subject_height_cm' => 'required|integer|min:0|max:300',
            'subject_weight_kg' => 'required|numeric|min:0|max:500',
            'health_challenges' => 'nullable|string',
            'attachment' => 'required|file|mimes:pdf|max:10240', // max 10MB
        ]);

        // Si client_id == 0 -> crear cliente con campos provistos en el mismo POST
        if ($base['client_id'] == 0) {
            $clientData = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|max:255',
                'phone' => 'nullable|string|max:50',
                'identifier' => 'nullable|string|max:100',
                'birthdate' => 'nullable|date',
                'height_cm' => 'nullable|integer|min:0|max:300',
                'weight_kg' => 'nullable|numeric|min:0|max:500',
                'is_self' => 'sometimes|boolean'
            ]);

            $clientData['user_id'] = $user->id;
            $client = Client::create($clientData);
            $clientId = $client->id;
        } else {
            // client_id > 0 -> usar cliente existente, verificar pertenencia al usuario
            $clientId = $base['client_id'];
            if (!$user->clients()->where('id', $clientId)->exists()) {
                if ($request->wantsJson() || $request->ajax()) {
                    return response()->json(['success' => false, 'message' => 'Cliente no autorizado'], 403);
                }
                return redirect()->back()->withErrors('Cliente no autorizado')->withInput();
            }
            $client = Client::find($clientId);
        }

        // reemplazo: obtener texto del PDF desde el UploadedFile (no asumir base64)
        $textPdf = null;
        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            try {
                $parser = new Parser();
                $pdf = $parser->parseFile($file->getRealPath());
                $textPdf = (string) $pdf->getText();
            } catch (\Throwable $e) {
                \Log::error('PDF parse error: ' . $e->getMessage());
                $textPdf = '';
            }
        } else {
            $textPdf = '';
        }

        $test = new Test();
        $test->user_id = Auth::id();
        $test->client_id = $clientId;
        $test->subject_age = $base['subject_age'];
        $test->subject_height_cm = $base['subject_height_cm'];
        $test->subject_weight_kg = $base['subject_weight_kg'];
        $test->health_challenges = $base['health_challenges'] ?? null;
        $test->pdf_text = $textPdf;
        $test->status = 'Registrado';
        $test->save();

        // Store PDF as "test_{id}.pdf" on the "public" disk under "tests/"
        if ($request->hasFile('attachment')) {
            $path = $test->storePdf($request->file('attachment'));
        }
        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['success' => true, 'test' => $test, 'client' => $client ?? null], 201);
        }
        return redirect()->route('tests.index')->with('success', 'Prueba creada correctamente.');
    }

    public function show(Test $test)
    {
        if (Gate::denies('view', $test)) {
            abort(403);
        }
        return view('tests.show', compact('test'));
    }
}

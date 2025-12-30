@extends('layouts.app')

@section('content')
    <div class="container mx-auto px-4 py-8">
        <h1 class="text-2xl font-bold mb-4">Detalle de Prueba</h1>

        <div class="p-4 bg-white dark:bg-gray-800 rounded-md shadow-sm">
            <div class="mb-2"><strong>Tipo:</strong> {{ $test->type ?? '—' }}</div>
            <div class="mb-2"><strong>Fecha:</strong> {{ $test->sample_date?->format('Y-m-d') }}</div>
            <div class="mb-2"><strong>Score:</strong> {{ $test->score ?? '—' }}</div>

            <div class="mt-4">
                <strong>Datos:</strong>
                <pre class="mt-2 bg-gray-100 dark:bg-gray-900 p-3 rounded text-sm overflow-auto">{{ json_encode($test->data ?? [], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
            </div>
        </div>

        <div class="mt-4">
            <a href="{{ route('tests.index') }}" class="text-muted">Volver</a>
        </div>
    </div>
@endsection

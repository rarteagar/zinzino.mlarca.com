@extends('layouts.app')

@section('content')
    <div class="container mx-auto px-4 py-8">
        <h1 class="text-2xl font-bold mb-4">Mis Pruebas</h1>

        @can('create tests')
            <a href="{{ route('tests.create') }}" class="inline-block px-4 py-2 bg-primary text-white rounded-md mb-4">Registrar
                prueba</a>
        @endcan

        <h2 class="text-xl font-semibold mt-6">Pruebas propias</h2>
        @if ($myTests->isEmpty())
            <p class="text-sm text-muted">No tienes pruebas registradas.</p>
        @else
            <ul class="space-y-3 mt-3">
                @foreach ($myTests as $t)
                    <li class="p-3 bg-white dark:bg-gray-800 rounded-md shadow-sm">
                        <div class="flex items-center justify-between">
                            <div>
                                <div class="font-medium">{{ $t->type ?? 'Prueba' }} — {{ $t->sample_date?->format('Y-m-d') }}
                                </div>
                                <div class="text-sm text-muted">Score: {{ $t->score ?? '—' }}</div>
                            </div>
                            <a href="{{ route('tests.show', $t) }}" class="text-sm text-primary">Ver</a>
                        </div>
                    </li>
                @endforeach
            </ul>
        @endif

        <h2 class="text-xl font-semibold mt-8">Pruebas de mis clientes</h2>
        @if ($clientTests->isEmpty())
            <p class="text-sm text-muted">No hay pruebas de clientes registradas.</p>
        @else
            <ul class="space-y-3 mt-3">
                @foreach ($clientTests as $t)
                    <li class="p-3 bg-white dark:bg-gray-800 rounded-md shadow-sm">
                        <div class="flex items-center justify-between">
                            <div>
                                <div class="font-medium">{{ $t->type ?? 'Prueba' }} —
                                    {{ $t->sample_date?->format('Y-m-d') }}</div>
                                <div class="text-sm text-muted">Cliente: {{ $t->client?->name ?? '—' }}</div>
                            </div>
                            <a href="{{ route('tests.show', $t) }}" class="text-sm text-primary">Ver</a>
                        </div>
                    </li>
                @endforeach
            </ul>
        @endif
    </div>
@endsection

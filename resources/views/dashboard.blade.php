<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <h3 class="text-2xl font-semibold mb-4">Bienvenido a ZinoKit</h3>
                    <p class="text-sm text-muted mb-6">Aquí verás tus pruebas, recomendaciones y documentos. Si aún no
                        tienes resultados, realiza la prueba o sube tus datos.</p>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="p-4 bg-gray-50 dark:bg-gray-900 rounded-md">
                            <h4 class="font-medium">Balance Test</h4>
                            @can('create tests')
                                <div class="mt-3">
                                    <a href="{{ route('tests.create') }}"
                                        class="inline-block px-3 py-2 bg-primary text-white rounded-md text-sm">Registrar
                                        una prueba</a>
                                </div>
                            @endcan
                        </div>

                        <div class="p-4 bg-gray-50 dark:bg-gray-900 rounded-md">
                            <h4 class="font-medium">Recomendaciones</h4>
                            <p class="text-sm text-muted">Contenido personalizado aparecerá aquí tras análisis de
                                resultados.</p>
                        </div>

                        <div class="p-4 bg-gray-50 dark:bg-gray-900 rounded-md">
                            <h4 class="font-medium">Documentos</h4>
                            <p class="text-sm text-muted">Descarga informes e imágenes relacionadas con tus pruebas.</p>
                            <div class="mt-3">
                                <a href="/docs"
                                    class="inline-block px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-md text-sm">Ver
                                    documentos</a>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>

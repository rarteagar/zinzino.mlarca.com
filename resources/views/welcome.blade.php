<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>ZinoKit — {{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />

    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif
</head>

<body class="bg-background dark:bg-background-dark text-gray-900 dark:text-gray-100 min-h-screen">
    <div class="max-w-4xl mx-auto px-6 py-12">
        <header class="flex items-center justify-between">
            <a href="/" class="flex items-center gap-3">
                <span class="font-semibold text-xl"></span>
            </a>

            <div class="flex items-center gap-3">
                @if (Route::has('login'))
                    @auth
                        <a href="{{ url('/dashboard') }}" class="px-4 py-2 rounded-md bg-primary text-white">Dashboard</a>
                    @else
                        <a href="{{ route('register') }}" class="px-4 py-2 rounded-md bg-primary text-white">Regístrate</a>
                        <a href="{{ route('login') }}"
                            class="px-4 py-2 rounded-md border border-gray-300 dark:border-gray-700">Iniciar sesión</a>
                    @endauth
                @endif
            </div>
        </header>

        <main class="mt-10 grid lg:grid-cols-2 gap-10 items-center">
            <section>
                <h1 class="text-4xl font-bold text-primary mb-4">ZinoKit</h1>
                <p class="text-lg text-muted mb-6">Regístrate para acceder a tu panel personal. Aquí interpretarás
                    resultados de pruebas de sangre, consulta sobre productos entre otros.</p>

                <div class="flex items-center gap-3">
                    @guest
                        <a href="{{ route('register') }}"
                            class="inline-block px-6 py-3 bg-primary text-white rounded-md text-sm">Regístrate</a>
                        <a href="{{ route('login') }}"
                            class="inline-block px-6 py-3 border border-gray-300 dark:border-gray-700 rounded-md text-sm">Iniciar
                            sesión</a>
                    @else
                        <a href="{{ url('/dashboard') }}"
                            class="inline-block px-6 py-3 bg-primary text-white rounded-md text-sm">Ir al dashboard</a>
                    @endguest
                </div>

                <p class="mt-6 text-sm text-muted">El dashboard estará disponible tras el registro. Por ahora la sección
                    es informativa.</p>
            </section>

            <aside class="bg-white dark:bg-gray-900 rounded-lg p-6 shadow-md">
                <h3 class="font-semibold mb-3">¿Qué ofrece ZinoKit?</h3>
                <ul class="text-sm space-y-2">
                    <li>• Interpretación de pruebas de sangre (Balance Test)</li>
                    <li>• Recomendaciones personalizadas de producto</li>
                    <li>• Acceso a documentos e imágenes</li>
                    <li>• Gestión de tu historial y progreso</li>
                </ul>
                <p class="mt-4 text-xs text-muted">Privacidad y seguridad de datos respetadas. Solo usuarios registrados
                    pueden ver resultados personales.</p>
            </aside>
        </main>
    </div>
</body>

</html>

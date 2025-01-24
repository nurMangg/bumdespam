<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        <style>
            body {
                transition: background-image 0.5s ease-in-out;
            }
        </style>
    </head>
    <body class="font-sans text-gray-900 antialiased bg-cover bg-center bg-no-repeat">
        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0">
            

            <div class="w-full sm:max-w-md mt-6 px-6 py-4 bg-white bg-opacity-50 shadow-md overflow-hidden sm:rounded-lg">
                <div class="flex items-center justify-center mb-4">
                    <a href="/">
                        <x-application-logo class="fill-current text-gray-500" />
                    </a>
                </div>
                {{ $slot }}
            </div>
        </div>

        <!-- Random Background Script -->
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                // Daftar gambar background
                const backgrounds = [
                    "{{ asset('images/873.jpg') }}",
                    "{{ asset('images/9126.jpg') }}",
                    "{{ asset('images/24143.jpg') }}",
                    "{{ asset('images/46361.jpg') }}"
                ];

                // Pilih gambar secara acak
                const randomBackground = backgrounds[Math.floor(Math.random() * backgrounds.length)];

                // Terapkan gambar ke body
                document.body.style.backgroundImage = `url('${randomBackground}')`;
            });
        </script>
    </body>
</html>

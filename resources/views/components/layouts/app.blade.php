<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>KasirQ | {{ Auth::user()->role === 'admin' ? 'Admin' : 'Kasir' }}</title>

    <link rel="icon" href="{{ asset('images/favicon-kasirq-light.svg') }}">

    @vite('resources/css/app.css')
    @livewireStyles

    <style>
    @media print {
        body * {
            visibility: hidden;
        }
        #receipt-content, #receipt-content * {
            visibility: visible;
        }
        #receipt-content {
            position: absolute;
            left: 0;
            top: 0;
            width: 100%;
            max-width: none;
            margin: 0;
            padding: 20px;
            box-shadow: none;
            border: none;
            background: white;
        }
        .print-hidden {
            display: none;
        }
    }
    </style>
</head>
<body class="bg-gray-50 antialiased">
    <div class="flex h-screen">
        <!-- Sidebar -->
        <x-sidebar />

        <!-- Main Content -->
        <div class="flex-1 flex flex-col overflow-hidden">
            <!-- Header -->
            <header class="bg-white p-8 flex justify-between items-center">
                <div class="text-2xl font-bold text-gray-800">
                    {{ $title ?? 'Dashboard' }}
                </div>

                <!-- User Info & Logout -->
                <div class="flex items-center space-x-4">
                    <div class="text-right">
                        <p class="font-medium text-gray-800">{{ Auth::user()->name }}</p>
                        <p class="text-sm text-gray-500 capitalize">{{ Auth::user()->role }}</p>
                    </div>

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button 
                            type="submit" 
                            class="p-2 bg-black rounded-full hover:bg-gray-900 text-white hover:text-gray-400 transition duration-150"
                            title="Logout"
                        >
                            <!-- Logout SVG Icon -->
                            <x-icon name="heroicon-s-arrow-left-on-rectangle" class="w-6 h-6" />
                        </button>
                    </form>
                </div>
            </header>

            <!-- Main Content Area -->
            <main class="flex-1 overflow-x-hidden overflow-y-auto p-6 bg-white">
                {{ $slot }}
                @livewireScripts
            </main>
        </div>
    </div>
</body>
</html>
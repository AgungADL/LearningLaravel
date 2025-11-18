<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk - KasirQ</title>
    
    <link rel="icon" href="{{ asset('images/favicon-kasirq-light.svg') }}">

    @vite('resources/css/app.css')
</head>
<body class="bg-white flex items-center justify-center min-h-screen">
    <div class="w-full max-w-md p-8 bg-white rounded-xl shadow-sm border border-gray-200">
        <!-- Logo -->
        <div class="h-10 mb-8">
            <img 
            src="{{ asset('images/logo-kasirq-dark.svg') }}" 
            alt="Logo KasirQ" 
            class="mx-auto w-60 h-auto relative -top-20"
        >
        </div>
        
        <p class="text-sm text-center mt-4 mb-8 text-gray-500">Silakan masuk ke akun Anda</p>

        <!-- Status Messages -->
        @if (session('status'))
            <div class="mb-4 text-sm text-green-600">{{ session('status') }}</div>
        @endif
        @if ($errors->any())
            <div class="mb-4 p-3 bg-red-50 border border-red-200 text-red-600 text-sm rounded">
                Terdapat kesalahan dalam pengisian.
            </div>
        @endif

        <!-- Form -->
        <form method="POST" action="{{ route('login') }}">
            @csrf

            <div class="mb-5">
                <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                <input 
                    id="email" 
                    type="email" 
                    name="email" 
                    value="{{ old('email') }}" 
                    required 
                    autofocus 
                    class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-1 focus:ring-black focus:border-black transition"
                >
            </div>

            <div class="mb-6">
                <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Kata Sandi</label>
                <input 
                    id="password" 
                    type="password" 
                    name="password" 
                    required 
                    autocomplete="current-password" 
                    class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-1 focus:ring-black focus:border-black transition"
                >
            </div>

            <div class="mt-8">
                <button 
                    type="submit" 
                    class="w-full py-3 px-4 bg-black text-white font-medium rounded-lg hover:bg-gray-900 transition duration-150"
                >
                    Masuk ke Akun
                </button>
            </div>
        </form>

        <!-- Footer -->
        <p class="mt-6 text-center text-xs text-gray-500">
            © {{ date('Y') }} KasirQ. Dikembangkan oleh <span class="font-semibold">AgungADL</span>. Hak Cipta Dilindungi.
        </p>
    </div>
</body>
</html>
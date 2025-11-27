@php
    $user = Auth::user();
@endphp

<div class="w-64 bg-black text-white flex flex-col">
    <!-- Logo & Title -->
    <div class="h-20 flex items-center justify-center border-b border-gray-800">
        <a href="{{ route('dashboard') }}"><img src="{{ asset('images/logo-kasirq-light.svg') }}" alt="Logo KasirQ" class="mx-auto w-40 h-auto"></a>
    </div>

    <!-- Navigation -->
    <nav class="flex-1 p-4 space-y-2">
        <a 
            href="{{ route('dashboard') }}" 
            class="flex items-center p-3 rounded-lg hover:bg-gray-800 transition duration-150 {{ Request::is('dashboard') || Request::is('*/dashboard') ? 'bg-gray-800 font-semibold border border-gray-900' : '' }}"
        >
            <x-icon name="{{ Request::is('dashboard') || Request::is('*/dashboard') ? 'heroicon-s-home' : 'heroicon-o-home' }}" class="w-5 h-5 mr-3" />
            Dashboard
        </a>
        
        @can('isAdmin')
            <div class="pt-4 border-t border-gray-800">
                <p class="text-xs text-gray-400 uppercase mb-2">Admin Panel</p>
                {{-- <a href="{{ route('admin.pos.index') }}" class="flex items-center p-3 rounded-lg hover:bg-gray-800 transition duration-150 {{ Request::is('*/pos') ? 'bg-gray-800 font-semibold border border-gray-900' : ''}}">
                    <x-icon name="{{ Request::is('*/pos')  ? 'heroicon-s-shopping-cart' : 'heroicon-o-shopping-cart' }}" class="w-5 h-5 mr-3" />
                    Transaksi POS
                </a> --}}
                <a href="{{ route('admin.products.index') }}" class="flex items-center p-3 rounded-lg hover:bg-gray-800 transition duration-150 {{ Request::is('*/products') ? 'bg-gray-800 font-semibold border border-gray-900' : '' }}">
                    <x-icon name="{{ Request::is('*/products') ? 'heroicon-s-shopping-bag' : 'heroicon-o-shopping-bag' }}" class="w-5 h-5 mr-3" />
                    Kelola Produk
                </a>
                <a href="{{ route('admin.users.index') }}" class="flex items-center p-3 rounded-lg hover:bg-gray-800 transition duration-150 {{ Request::is('*/users') ? 'bg-gray-800 font-semibold border border-gray-900' : '' }}">
                    <x-icon name="{{ Request::is('*/users') ? 'heroicon-s-user-group' : 'heroicon-o-user-group' }}" class="w-5 h-5 mr-3" />
                    Kelola Kasir
                </a>
                <a href="{{ route('members.index') }}" class="flex items-center p-3 rounded-lg hover:bg-gray-800 transition duration-150 {{ Request::is('members') ? 'bg-gray-800 font-semibold border border-gray-900' : '' }}">
                    <x-icon name="{{ Request::is('members') ? 'heroicon-s-user' : 'heroicon-o-user' }}" class="w-5 h-5 mr-3" />
                    Kelola Member
                </a>
                <a href="{{ route('admin.categories.index') }}" class="flex items-center p-3 rounded-lg hover:bg-gray-800 transition duration-150 {{ Request::is('*/categories') ? 'bg-gray-800 font-semibold border border-gray-900' : '' }}">
                    <x-icon name="{{ Request::is('*/categories') ? 'heroicon-s-square-3-stack-3d' : 'heroicon-o-square-3-stack-3d' }}" class="w-5 h-5 mr-3" />
                    Kelola Kategori
                </a>
                <a href="{{ route('admin.reports.sales') }}" class="flex items-center p-3 rounded-lg hover:bg-gray-800 transition duration-150 {{ Request::is('*/reports/sales') ? 'bg-gray-800 font-semibold border border-gray-900' : '' }}">
                    <x-icon name="{{ Request::is('*/reports/sales') ? 'heroicon-s-chart-bar' : 'heroicon-o-chart-bar'}}" class="w-5 h-5 mr-3" />
                    Laporan Penjualan
                </a>
                <a href="{{ route('admin.reports.stock') }}" class="flex items-center p-3 rounded-lg hover:bg-gray-800 transition duration-150 {{ Request::is('*/reports/stock') ? 'bg-gray-800 font-semibold border border-gray-900' : '' }}">
                    <x-icon name="{{ Request::is('*/reports/stock') ? 'heroicon-s-chart-bar' : 'heroicon-o-chart-bar'}}" class="w-5 h-5 mr-3" />
                    Laporan Stok Barang
                </a>
                <a href="{{ route('admin.settings.index') }}" class="flex items-center p-3 rounded-lg hover:bg-gray-800 transition duration-150 {{ Request::is('*/settings') ? 'bg-gray-800 font-semibold border border-gray-900' : '' }}">
                    <x-icon name="{{ Request::is('*/settings') ? 'heroicon-s-cog-6-tooth' : 'heroicon-o-cog-6-tooth' }}" class="w-5 h-5 mr-3" />
                    Pengaturan Diskon
                </a>
            </div>
        @endcan

        @can('isKasir')
            <div class="pt-4 border-t border-gray-800">
                <p class="text-xs text-gray-400 uppercase mb-2">Kasir Panel</p>
                <a href="{{ route('kasir.pos.index') }}" class="flex items-center p-3 rounded-lg hover:bg-gray-800 transition duration-150 {{ Request::is('*/pos') ? 'bg-gray-800 font-semibold border border-gray-900' : ''}}">
                    <x-icon name="{{ Request::is('*/pos') ? 'heroicon-s-shopping-cart' : 'heroicon-o-shopping-cart' }}" class="w-5 h-5 mr-3" />
                    Transaksi POS
                </a>
                <a href="{{ route('members.index') }}" class="flex items-center p-3 rounded-lg hover:bg-gray-800 transition duration-150 {{ Request::is('members') ? 'bg-gray-800 font-semibold border border-gray-900' : ''}}">
                    <x-icon name="{{ Request::is('members') ? 'heroicon-s-user' : 'heroicon-o-user' }}" class="w-5 h-5 mr-3" />
                    Daftar Member
                </a>
                <a href="{{ route('kasir.reports.sales') }}" class="flex items-center p-3 rounded-lg hover:bg-gray-800 transition duration-150 {{ Request::is('*/reports') ? 'bg-gray-800 font-semibold border border-gray-900' : '' }}">
                    <x-icon name="{{ Request::is('*/reports/sales') ? 'heroicon-s-chart-bar' : 'heroicon-o-chart-bar'}}" class="w-5 h-5 mr-3" />
                    Laporan
                </a>
            </div>
        @endcan
    </nav>

    <!-- Footer Credit -->
    <div class="p-4 text-center text-xs text-gray-500 border-t border-gray-800">
        © {{ date('Y') }} KasirQ. Dikembangkan oleh <span class="font-semibold">AgungADL</span>. Hak Cipta Dilindungi.
    </div>
</div>
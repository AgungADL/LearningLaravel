<?php

namespace App\Http\Controllers;

use App\Models\Member;
use App\Models\Product;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index() {
        $user = Auth::user();

        if ($user->role === 'admin') {
            $totalProducts = Product::count();
            $totalMembers = Member::count();
            $totalCashiers = User::where('role', 'kasir')->count();
            $totalTransactionsToday = Transaction::whereDate('created_at', today())->count();
            $totalRevenueToday = Transaction::whereDate('created_at', today())->sum('grand_total');

            return view('admin.dashboard', [
                'totalProducts' => $totalProducts,
                'totalMembers' => $totalMembers,
                'totalCashiers' => $totalCashiers,
                'totalTransactionsToday' => $totalTransactionsToday,
                'totalRevenueToday' => $totalRevenueToday,
            ]);
        } else if ($user->role === 'kasir') {
            $totalTransactionsToday = Transaction::whereDate('created_at', today())
                ->where('user_id', Auth::id())
                ->count();
            $totalRevenueToday = Transaction::whereDate('created_at', today())
                ->where('user_id', Auth::id())
                ->sum('grand_total');
            $totalProducts = Product::count();
            $totalMembers = Member::count();

            return view('kasir.dashboard', [
                'totalTransactionsToday' => $totalTransactionsToday,
                'totalRevenueToday' => $totalRevenueToday,
                'totalProducts' => $totalProducts,
                'totalMembers' => $totalMembers,
            ]);
        }

        return redirect()->route('login');
    }
}

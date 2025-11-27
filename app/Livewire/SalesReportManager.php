<?php

namespace App\Livewire;

use App\Exports\SalesDetailExport;
use App\Exports\SalesSummaryExport;
use App\Models\Transaction;
use App\Models\TransactionDetail;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;
use Maatwebsite\Excel\Facades\Excel;

class SalesReportManager extends Component
{
    use WithPagination;

    public $startDate;
    public $endDate;
    public $kasirId = '';
    public $viewMode = 'summary'; // 'summary' atau 'detail'

    public function mount()
    {
        $this->endDate = Carbon::today()->format('Y-m-d');
        $this->startDate = Carbon::today()->subDays(30)->format('Y-m-d');
    }

    public function getSalesTransactions()
    {
        $query = Transaction::with(['user', 'member']);

        $query->whereDate('created_at', '>=', $this->startDate)
              ->whereDate('created_at', '<=', $this->endDate);

        if (Auth::user()->role === 'kasir') {
            $query->where('user_id', Auth::id());
        } elseif (Auth::user()->role === 'admin' && $this->kasirId) {
            $query->where('user_id', $this->kasirId);
        }

        return $query->orderBy('created_at', 'desc')->paginate(10);
    }

    public function getSalesDetails()
    {
        $query = TransactionDetail::with(['transaction.user', 'transaction.member', 'product']);

        $query->whereHas('transaction', function ($q) {
            $q->whereDate('created_at', '>=', $this->startDate)
              ->whereDate('created_at', '<=', $this->endDate);

            if (Auth::user()->role === 'kasir') {
                $q->where('user_id', Auth::id());
            } elseif (Auth::user()->role === 'admin' && $this->kasirId) {
                $q->where('user_id', $this->kasirId);
            }
        });

        return $query->orderBy('created_at', 'desc')->paginate(20);
    }

    public function exportToExcel()
    {
        if ($this->viewMode === 'detail') {
            return Excel::download(
                new SalesDetailExport($this->startDate, $this->endDate, $this->kasirId),
                'laporan_penjualan_detail_' . time() . '.xlsx'
            );
        }

        return Excel::download(
            new SalesSummaryExport($this->startDate, $this->endDate, $this->kasirId),
            'laporan_penjualan_ringkasan_' . time() . '.xlsx'
        );
    }

    public function render()
    {
        $allKasirs = Auth::user()->role === 'admin' 
            ? User::where('role', 'kasir')->get() 
            : collect();

        if ($this->viewMode === 'detail') {
            $details = $this->getSalesDetails();
            return view('livewire.sales-report-manager', [
                'details' => $details,
                'allKasirs' => $allKasirs,
            ]);
        }

        $transactions = $this->getSalesTransactions();
        return view('livewire.sales-report-manager', [
            'transactions' => $transactions,
            'allKasirs' => $allKasirs,
        ]);
    }
}

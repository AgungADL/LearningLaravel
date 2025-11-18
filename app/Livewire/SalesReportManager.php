<?php

namespace App\Livewire;

use App\Models\Transaction;
use App\Models\TransactionDetail;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

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
            return $this->exportDetailToExcel();
        }

        return $this->exportSummaryToExcel();
    }

    public function exportSummaryToExcel()
    {
        $query = Transaction::with(['user', 'member']);

        $query->whereDate('created_at', '>=', $this->startDate)
              ->whereDate('created_at', '<=', $this->endDate);

        if (Auth::user()->role === 'kasir') {
            $query->where('user_id', Auth::id());
        } elseif (Auth::user()->role === 'admin' && $this->kasirId) {
            $query->where('user_id', $this->kasirId);
        }

        $data = $query->get();

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="laporan_penjualan_ringkasan_' . time() . '.csv"',
        ];

        $callback = function() use ($data) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['ID Transaksi', 'Tanggal', 'Kasir', 'Member', 'Subtotal', 'Diskon', 'Total Akhir', 'Metode Pembayaran']);

            foreach ($data as $row) {
                fputcsv($file, [
                    $row->id,
                    $row->created_at->format('Y-m-d H:i'),
                    $row->user->name ?? 'N/A',
                    $row->member->name ?? 'Non-Member',
                    $row->subtotal,
                    $row->discount_amount,
                    $row->grand_total,
                    $row->payment_method,
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function exportDetailToExcel()
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

        $data = $query->get();

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="laporan_penjualan_detail_' . time() . '.csv"',
        ];

        $callback = function() use ($data) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['ID Transaksi', 'Tanggal', 'Kasir', 'Member', 'Nama Produk', 'Qty', 'Harga Satuan', 'Subtotal']);

            foreach ($data as $row) {
                fputcsv($file, [
                    $row->transaction->id,
                    $row->transaction->created_at->format('Y-m-d H:i'),
                    $row->transaction->user->name ?? 'N/A',
                    $row->transaction->member->name ?? 'Non-Member',
                    $row->product_name,
                    $row->quantity,
                    $row->price,
                    $row->subtotal,
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
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

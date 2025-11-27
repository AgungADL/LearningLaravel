<?php

namespace App\Exports;

use App\Models\Transaction;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class SalesSummaryExport implements FromQuery, WithHeadings, WithMapping
{
    protected $startDate;
    protected $endDate;
    protected $kasirId;

    public function __construct($startDate, $endDate, $kasirId = null)
    {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->kasirId = $kasirId;
    }

    public function query()
    {
        $query = Transaction::with(['user', 'member'])
            ->whereDate('created_at', '>=', $this->startDate)
            ->whereDate('created_at', '<=', $this->endDate);

        if (Auth::user()->role === 'kasir') {
            $query->where('user_id', Auth::id());
        } elseif (Auth::user()->role === 'admin' && $this->kasirId) {
            $query->where('user_id', $this->kasirId);
        }

        return $query;
    }

    public function headings(): array
    {
        return [
            'ID Transaksi',
            'Tanggal',
            'Kasir',
            'Member',
            'Subtotal',
            'Diskon',
            'Total Akhir',
            'Metode Pembayaran',
        ];
    }

    public function map($transaction): array
    {
        return [
            $transaction->id,
            $transaction->created_at->format('Y-m-d H:i'),
            $transaction->user->name ?? 'N/A',
            $transaction->member->name ?? 'Non-Member',
            $transaction->subtotal,
            $transaction->discount_amount,
            $transaction->grand_total,
            $transaction->payment_method,
        ];
    }
}
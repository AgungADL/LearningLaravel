<?php

namespace App\Exports;

use App\Models\TransactionDetail;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class SalesDetailExport implements FromQuery, WithHeadings, WithMapping
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

        return $query;
    }

    public function headings(): array
    {
        return [
            'ID Transaksi',
            'Tanggal',
            'Kasir',
            'Member',
            'Nama Produk',
            'Qty',
            'Harga Satuan',
            'Subtotal',
        ];
    }

    public function map($detail): array
    {
        return [
            $detail->transaction->id,
            $detail->transaction->created_at->format('Y-m-d H:i'),
            $detail->transaction->user->name ?? 'N/A',
            $detail->transaction->member->name ?? 'Non-Member',
            $detail->product_name,
            $detail->quantity,
            $detail->price,
            $detail->subtotal,
        ];
    }
}
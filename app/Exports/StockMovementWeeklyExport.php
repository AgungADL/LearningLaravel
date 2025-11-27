<?php

namespace App\Exports;

use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class StockMovementWeeklyExport implements FromCollection, WithHeadings
{
    protected $startDate;
    protected $endDate;
    protected $search;
    protected $lowStock;

    public function __construct($startDate, $endDate, $search = null, $lowStock = false)
    {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->search = $search;
        $this->lowStock = $lowStock;
    }

    public function collection()
    {
        $query = Product::with('category');

        if ($this->search) {
            $query->where('name', 'like', '%' . $this->search . '%');
        }

        if ($this->lowStock) {
            $query->where('stock', '<', 10);
        }

        $data = $query->get();

        $results = collect();

        foreach ($data as $product) {
            $movements = $product->stockMovements()
                ->whereBetween('created_at', [
                    Carbon::parse($this->startDate)->startOfDay(),
                    Carbon::parse($this->endDate)->endOfDay()
                ])
                ->get();

            $in = 0;
            $out = 0;

            foreach ($movements as $movement) {
                if ($movement->quantity > 0) {
                    $in += $movement->quantity;
                } else {
                    $out += abs($movement->quantity);
                }
            }

            $netChange = $in - $out;
            $stockStart = $product->stock - $netChange;

            $results->push([
                'name' => $product->name,
                'category' => $product->category->name ?? '-',
                'unit' => 'Pcs',
                'stock_start' => $stockStart,
                'total_in' => $in,
                'total_out' => $out,
                'stock_end' => $product->stock,
            ]);
        }

        return $results;
    }

    public function headings(): array
    {
        return [
            'Nama Produk',
            'Kategori',
            'Satuan',
            'Stok Awal',
            'Total Restock (IN)',
            'Total Pengurangan (OUT)',
            'Stok Akhir',
        ];
    }
}
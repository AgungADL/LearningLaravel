<?php

namespace App\Livewire\Admin;

use App\Exports\StockMovementWeeklyExport;
use App\Models\Product;
use Carbon\Carbon;
use Livewire\Component;
use Livewire\WithPagination;
use Maatwebsite\Excel\Facades\Excel;

class StockReportManager extends Component
{
    use WithPagination;

    public $search = '';
    public $lowStock = false;
    public $weekStartDate = '';
    public $weekEndDate = '';

    public function mount()
    {
        $now = Carbon::now();
        $this->weekStartDate = $now->startOfWeek()->format('Y-m-d'); // Format: 2025-11-10
        $this->weekEndDate = $now->endOfWeek()->format('Y-m-d');     // Format: 2025-11-16
    }

    public function updatedWeekStartDate($value)
    {
        // Pastikan $value adalah Carbon object
        $startDate = Carbon::parse($value);
        $this->weekEndDate = $startDate->endOfWeek()->format('Y-m-d');
    }

    public function getStockProducts()
    {
        $startDate = Carbon::parse($this->weekStartDate)->startOfDay();
        $endDate = Carbon::parse($this->weekEndDate)->endOfDay();

        $query = Product::with('category');

        if ($this->search) {
            $query->where('name', 'like', '%' . $this->search . '%');
        }

        if ($this->lowStock) {
            $query->where('stock', '<', 10);
        }

        $products = $query->orderBy('stock', 'asc')->paginate(10);

        foreach ($products as $product) {
            $movements = $product->stockMovements()
                ->whereBetween('created_at', [$startDate, $endDate])
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
            $stockBefore = $product->stock - $netChange;

            $product->stock_summary = [
                'stock_start' => $stockBefore,
                'total_in' => $in,
                'total_out' => $out,
                'stock_end' => $product->stock,
            ];
        }

        return $products;
    }

    public function exportToExcel()
    {
        return Excel::download(
            new StockMovementWeeklyExport(
                $this->weekStartDate,
                $this->weekEndDate,
                $this->search,
                $this->lowStock
            ),
            'laporan_pergerakan_stok_mingguan_' . time() . '.xlsx'
        );
    }

    public function render()
    {
        $products = $this->getStockProducts();

        return view('livewire.admin.stock-report-manager', [
            'products' => $products,
        ]);
    }
}

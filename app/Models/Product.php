<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $guarded = [];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function stockMovements()
    {
        return $this->hasMany(StockMovement::class);
    }

    public function getStockMovementsForWeek($startDate, $endDate)
    {
        return $this->stockMovements()
            ->whereBetween('created_at', [$startDate, $endDate])
            ->get();
    }

    public function getWeeklyStockSummary($startDate, $endDate)
    {
        $movements = $this->stockMovements()
            ->whereBetween('created_at', [$startDate, $endDate])
            ->get();

        $in = 0;
        $out = 0;

        foreach ($movements as $movement) {
            if ($movement->quantity > 0) {
                // Penambahan stok (masuk)
                $in += $movement->quantity;
            } else {
                // Pengurangan stok (keluar) - jadikan positif untuk ditampilkan
                $out += abs($movement->quantity);
            }
        }

        // Stok sebelum periode = stok sekarang - total perubahan selama periode
        $netChange = $in - $out;
        $stockBefore = $this->stock - $netChange;

        return [
            'stock_start' => $stockBefore,
            'total_in' => $in,
            'total_out' => $out,
            'stock_end' => $this->stock,
        ];
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $guarded = [];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    public function user()
    {
        // Kasir yang membuat transaksi
        return $this->belongsTo(User::class, 'user_id');
    }

    public function member()
    {
        return $this->belongsTo(Member::class, 'member_id');
    }

    public function details()
    {
        return $this->hasMany(TransactionDetail::class);
    }
}

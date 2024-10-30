<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Produk extends Model
{
    use HasFactory;

    protected $table ='produk';
    protected $primaryKey = 'id_produk';
    protected $guarded = [];
    public function penjualan()
{
    return $this->belongsToMany(Penjualan::class, 'detail_penjualan', 'id_produk', 'id_penjualan')
                ->withPivot('jumlah', 'harga_jual', 'subtotal');
}
}



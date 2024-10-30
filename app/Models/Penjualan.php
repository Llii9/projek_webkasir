<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Penjualan extends Model
{
    use HasFactory;
    protected $table = 'penjualan';
    protected $primaryKey = 'id_penjualan';
    protected $guarded = [];

public function user()
{
    return $this->hasOne(User::class, 'id', 'id_user');
}
public function produk()
{
    return $this->belongsToMany(Produk::class, 'detail_penjualan', 'id_penjualan', 'id_produk')
                ->withPivot('jumlah', 'harga_jual', 'subtotal'); // Relasi dengan pivot untuk jumlah produk
}
// Model Penjualan.php
public function detail()
{
    return $this->hasMany(PenjualanDetail::class, 'id_penjualan');
}


}
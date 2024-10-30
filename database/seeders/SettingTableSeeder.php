<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SettingTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run():void
    {
        DB::table('setting')->insert([
            'id_setting' => 1,
            'nama_toko' => 'Toko',
            'alamat' => 'JL.prof.Moh.Yamin, Kabupaten Tegal, Jawa Tengah', 
            'telepon' => '083116207117',
            'tipe_nota' => 1, //kecil
            'diskon' => 0,
            'path_logo' => '/img/logo.png',
        ]);
    }
}

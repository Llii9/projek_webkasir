<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Setting;

class SettingController extends Controller
{
    public function index()
    {
        return view('setting.index');
    }

   public function show()
   {
    return Setting::first();
   }
        public function update(Request $request)
        {
            $setting = Setting::first();
            $setting->nama_toko = $request->nama_toko;
            $setting->alamat = $request->alamat;
            $setting->telepon = $request->telepon;
            $setting->tipe_nota = $request->tipe_nota;
           
           
            
        if ($request->hasFile('path_logo')) {
            $file = $request->file('path_logo');
            $nama = 'logo-' . date('YmdHis') . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('/img'), $nama);

            $setting->path_logo = "/img/$nama";
        }
            $setting->update();

            return response()->json('Data berhasil disimpan', 200);
        }
}


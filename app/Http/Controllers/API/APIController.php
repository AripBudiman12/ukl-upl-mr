<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Kegiatan;
use Illuminate\Http\Request;

class APIController extends Controller
{
    public function getKegiatan()
    {
        $query = Kegiatan::select(
            'tanggal_record',
            'nip',
            'pemrakarsa',
            'judul_kegiatan',
            'lokasi',
            'prov',
            'kota',
            'kewenangan',
            'jenisdokumen'
        );

        return datatables($query)->make(true);
    }
}

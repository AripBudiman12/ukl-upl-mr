<?php

namespace App\Http\Controllers;

use App\Models\Kegiatan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Yajra\DataTables\DataTables;

class KegiatanController extends Controller
{
    public function index()
    {
        $uklupl_sppl = Http::withToken('8|yrllsJEngF4NvGAa0xdQ0bD8LJ4OA6vY9Jak5WXa')->get('https://amdalnet-dev.menlhk.go.id/amdal-api/api/uklupl_sppl');
        $uklupl = Http::withToken('8|yrllsJEngF4NvGAa0xdQ0bD8LJ4OA6vY9Jak5WXa')->get('https://amdalnet-dev.menlhk.go.id/amdal-api/api/uklupl_pusat');
        $sppl = Http::withToken('8|yrllsJEngF4NvGAa0xdQ0bD8LJ4OA6vY9Jak5WXa')->get('https://amdalnet-dev.menlhk.go.id/amdal-api/api/sppl_pusat');
        $uklupl_prov = Http::withToken('8|yrllsJEngF4NvGAa0xdQ0bD8LJ4OA6vY9Jak5WXa')->get('https://amdalnet-dev.menlhk.go.id/amdal-api/api/jml_prov?dokumen=UKL-UPL');
        $sppl_prov = Http::withToken('8|yrllsJEngF4NvGAa0xdQ0bD8LJ4OA6vY9Jak5WXa')->get('https://amdalnet-dev.menlhk.go.id/amdal-api/api/jml_prov?dokumen=SPPL');
        $start_date = "";
        $end_date = "";

        if (request('start_date')) {
            $start_date = str_replace('-', '/', request('start_date'));
            $end_date = str_replace('-', '/', request('end_date'));
            $statistik = Http::withToken('8|yrllsJEngF4NvGAa0xdQ0bD8LJ4OA6vY9Jak5WXa')->get('https://amdalnet-dev.menlhk.go.id/amdal-api/api/statistik?perbulan=0&start_date=' . $start_date . '&end_date=' . $end_date);
        } if (request('perbulan')) {
            $statistik = Http::withToken('8|yrllsJEngF4NvGAa0xdQ0bD8LJ4OA6vY9Jak5WXa')->get('https://amdalnet-dev.menlhk.go.id/amdal-api/api/statistik?perbulan=1&start_date=' . $start_date . '&end_date=' . $end_date);
        } elseif (empty(request(['start_date','end_date'])) && request('perbulan') == 0) {
            $statistik = Http::withToken('8|yrllsJEngF4NvGAa0xdQ0bD8LJ4OA6vY9Jak5WXa')->get('https://amdalnet-dev.menlhk.go.id/amdal-api/api/statistik');
        }

        $uklupl_data = array();
        for ($i = 0; $i < count($uklupl['data']); $i++ ) {
            $uklupl_data[] = $uklupl['data'][$i]['jumlah'];
        }

        $sppl_data = array();
        for ($i = 0; $i < count($sppl['data']); $i++ ) {
            $sppl_data[] = $sppl['data'][$i]['jumlah'];
        }

        $uklupl_sppl_data = array();
        for ($i = 0; $i < count($uklupl_sppl['data']); $i++ ) {
            $uklupl_sppl_data[] = $uklupl_sppl['data'][$i]['jumlah'];
        }

        $prov_total = array();
        $prov_uklupl = array();
        $prov_sppl = array();
        $prov_label = array();
        for ($i = 0; $i < count($sppl_prov['data']); $i++ ) {
            if ($sppl_prov['data'][$i]['jumlah'] != null) {
                $prov_label[] = $uklupl_prov['data'][$i]['prov'];
                $prov_sppl[] = $sppl_prov['data'][$i]['jumlah'];
                $prov_uklupl[] = $uklupl_prov['data'][$i]['jumlah'];
                $prov_total[] = $sppl_prov['data'][$i]['jumlah'] + $uklupl_prov['data'][$i]['jumlah'];
            }
        }

        $stat_label = array();
        $stat_data = array();
        for ($i = 0; $i < count($statistik['data']); $i++) {
            if (request('perbulan')) {
                $stat_label[] = $statistik['data'][$i]['bulan'];
            } elseif (request('perbulan') == 0 || empty(request('perbulan'))) {
                $stat_label[] = $statistik['data'][$i]['tanggal_record'];
            }
            $stat_data[] = $statistik['data'][$i]['jumlah'];
        }

        $api = Http::withToken('8|yrllsJEngF4NvGAa0xdQ0bD8LJ4OA6vY9Jak5WXa')->get('https://amdalnet-dev.menlhk.go.id/amdal-api/api/kegiatan?limit=200');

        return DataTables::of($api['data'])->make(true);

        return view('index', compact(
            'uklupl_data',
            'sppl_data',
            'uklupl_sppl_data',
            'prov_label',
            'prov_uklupl',
            'prov_sppl',
            'prov_total',
            'stat_label',
            'stat_data'
        ));
    }

    public function datatable(Request $request)
    {
        $tota = Http::withToken('8|yrllsJEngF4NvGAa0xdQ0bD8LJ4OA6vY9Jak5WXa')->get('https://amdalnet-dev.menlhk.go.id/amdal-api/api/kegiatan?limit=40000');
        $api = Http::withToken('8|yrllsJEngF4NvGAa0xdQ0bD8LJ4OA6vY9Jak5WXa')->get('https://amdalnet-dev.menlhk.go.id/amdal-api/api/kegiatan?limit=40000');
        // if ($request->ajax()) {
        //     $data = $api;
        //     return Datatables::of($data)
        //             ->addIndexColumn()
        //             ->addColumn('kewenangan', function($row){
        //                  if($row->kewenangan){
        //                     return '<span class="badge badge-primary">Kab / Kot</span>';
        //                  }elseif($row->kewenangan){
        //                     return '<span class="badge badge-primary">Pusat</span>';
        //                  }
        //                  else{
        //                     return '<span class="badge badge-danger">Provinsi</span>';
        //                  }
        //             })
        //             ->filter(function ($instance) use ($request) {
        //                 if ($request->get('kewenangan') == '0' || $request->get('kewenangan') == '1' || $request->get('kewenangan') == '2') {
        //                     $instance->where('kewenangan', $request->get('kewenangan'));
        //                 }
        //                 if (!empty($request->get('search'))) {
        //                      $instance->where(function($w) use($request){
        //                         $search = $request->get('search');
        //                         $w->orWhere('jenis_kegiatan', 'LIKE', "%$search%")
        //                         ->orWhere('jenisdokumen', 'LIKE', "%$search%");
        //                     });
        //                 }
        //             })
        //             ->rawColumns(['kewenangan'])
        //             ->make(true);
        // }

        return DataTables::of($api['data'])->make(true);
    }
}

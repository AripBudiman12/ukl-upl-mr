<?php

namespace App\Http\Controllers;

use App\Models\districts;
use App\Models\provinces;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\DataTables;

class KegiatanController extends Controller
{
    public function user_role()
    {
        $user = Auth::user()->id;
        $kewenangan = User::join('luk_members', 'users.email', 'luk_members.email')
        ->join('feasibility_test_team_members', 'luk_members.id', 'feasibility_test_team_members.id_luk_member')
        ->join('feasibility_test_teams', 'feasibility_test_teams.id', 'feasibility_test_team_members.id_feasibility_test_team')
        ->select('feasibility_test_teams.authority', 'feasibility_test_teams.id_province_name', 'feasibility_test_teams.id_district_name')
        ->where('users.id',$user)->first();

        $prov = null;
        if ($kewenangan->id_province_name) {
            $prov = provinces::find($prov)->name;
        }

        $kabkota = null;
        if ($kewenangan->id_district_name) {
            $kabkota = districts::find($kabkota)->name;
        }

        $data = [
            'kewenangan' => $kewenangan->authority,
            'provinsi' => $prov,
            'kabkota' => $kabkota
        ];

        return $data;
    }

    public function index()
    {
        $uklupl_sppl = Http::withToken('1|QCyB3h7pys9X0g6vwG2gNoMK5y2dDamjTJSUVXbi')->get('https://amdal.menlhk.go.id/data_mr_api/public/api/uklupl_sppl');
        $uklupl = Http::withToken('1|QCyB3h7pys9X0g6vwG2gNoMK5y2dDamjTJSUVXbi')->get('https://amdal.menlhk.go.id/data_mr_api/public/api/uklupl_pusat');
        $sppl = Http::withToken('1|QCyB3h7pys9X0g6vwG2gNoMK5y2dDamjTJSUVXbi')->get('https://amdal.menlhk.go.id/data_mr_api/public/api/sppl_pusat');
        $uklupl_prov = Http::withToken('1|QCyB3h7pys9X0g6vwG2gNoMK5y2dDamjTJSUVXbi')->get('https://amdal.menlhk.go.id/data_mr_api/public/api/jml_prov?dokumen=UKL-UPL');
        $sppl_prov = Http::withToken('1|QCyB3h7pys9X0g6vwG2gNoMK5y2dDamjTJSUVXbi')->get('https://amdal.menlhk.go.id/data_mr_api/public/api/jml_prov?dokumen=SPPL');
        $cluster = Http::withToken('1|QCyB3h7pys9X0g6vwG2gNoMK5y2dDamjTJSUVXbi')->get('https://amdal.menlhk.go.id/data_mr_api/public/api/cluster');
        $start_date = "";
        $end_date = "";

        if (request('start_date')) {
            $start_date = str_replace('-', '/', request('start_date'));
            $end_date = str_replace('-', '/', request('end_date'));
            $statistik = Http::withToken('1|QCyB3h7pys9X0g6vwG2gNoMK5y2dDamjTJSUVXbi')->get('https://amdal.menlhk.go.id/data_mr_api/public/api/statistik?perbulan=0&start_date=' . $start_date . '&end_date=' . $end_date);
        } if (request('perbulan') == 1) {
            $statistik = Http::withToken('1|QCyB3h7pys9X0g6vwG2gNoMK5y2dDamjTJSUVXbi')->get('https://amdal.menlhk.go.id/data_mr_api/public/api/statistik?perbulan=1&start_date=' . $start_date . '&end_date=' . $end_date);
        } elseif (empty(request(['start_date','end_date','perbulan']))) {
            $statistik = Http::withToken('1|QCyB3h7pys9X0g6vwG2gNoMK5y2dDamjTJSUVXbi')->get('https://amdal.menlhk.go.id/data_mr_api/public/api/statistik');
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

        $cluster_label = array();
        $cluster_data = array();
        for ($i = 0; $i < count($cluster['data']); $i++) {
            $cluster_label[] = $cluster['data'][$i]['cluster_formulir'];
            $cluster_data[] = $cluster['data'][$i]['total'];
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

        return view('index', compact(
            'uklupl_data',
            'sppl_data',
            'uklupl_sppl_data',
            'prov_label',
            'prov_uklupl',
            'prov_sppl',
            'prov_total',
            'stat_label',
            'stat_data',
            'cluster_label',
            'cluster_data'
        ));
    }

    public function datatable()
    {
        $limit = request('length');
        $start = request('start');
        $search = request('search');

        $total = Http::withToken('1|QCyB3h7pys9X0g6vwG2gNoMK5y2dDamjTJSUVXbi')->get('https://amdal.menlhk.go.id/data_mr_api/public/api/filteredTotal');
        $api = Http::withToken('1|QCyB3h7pys9X0g6vwG2gNoMK5y2dDamjTJSUVXbi')->get('https://amdal.menlhk.go.id/data_mr_api/public/api/kegiatan?offset=' . $start . '&limit=' . $limit);

        return response()->json([
            "draw" => intval(request('draw')),
            "recordsTotal" => intval($total['data'][0]['count']),
            "recordsFiltered" => intval($total['data'][0]['count']),
            "data" => $api['data'],
        ]);

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

        // $data = Http::withToken('1|QCyB3h7pys9X0g6vwG2gNoMK5y2dDamjTJSUVXbi')->get('https://amdal.menlhk.go.id/data_mr_api/public/api/kegiatan?limit=100&offset=100');
        // return DataTables::of($data['data'])->make(true);
    }
}

<?php

namespace App\Http\Controllers;

use Exception;
use Carbon\Carbon;
use App\Models\User;
use App\Models\districts;
use App\Models\provinces;
use GuzzleHttp\Psr7\Request;
use App\Exports\KegiatanExport;
use Yajra\DataTables\DataTables;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Controllers\KegiatanController;
use Illuminate\Contracts\Pipeline\Hub;
use Throwable;

class DashboardController extends Controller
{
    public function __construct()
    {
        // Increase memory limit to unlimited
        ini_set('memory_limit', '-1');
        // Set the max execution time limit to unlimited
        ini_set('max_execution_time', 0);
        // Set the execution time limit to unlimited
        set_time_limit(0);

        $this->key = json_decode(Http::POST('https://hub.menlhk.go.id/oss_hub/services/getLocalKey'))->token;
    }

    public function index()
    {
        $user = (new Controller)->user_role();
        $start_date = "";
        $end_date = "";

        $province = "";
        $kabkota = "";
        if ($user['provinsi']) {
            $province = getProvince($user['provinsi']);
        }
        if ($user['kabkota']) {
            $kabkota = $user['kabkota'];
        }

        $date = (new Controller)->getDate();
        
        if (request('start_date')) {
            // $start_date = str_replace('-', '/', request('start_date'));
            // $end_date = str_replace('-', '/', request('end_date'));
            $start_date = request('start_date');
            $end_date = request('end_date');
        } else {
            $start_date = str_replace('-', '/', $date['start']);
            $end_date = str_replace('-', '/', $date['now']);
        }
        
        $tanggal = (new Controller)->getDate();
        $tgl_awal = $tanggal['start'];
        $tgl_akhir = $tanggal['now'];

        $dts = new Carbon($start_date);
        $dte = new Carbon($end_date);
        setlocale(\LC_TIME,'ID');

        $dts = $dts->formatLocalized('%e %B %Y');
        $dte = $dte->formatLocalized('%e %B %Y');

        $filterKewenangan = null;
        if (request('filterKewenangan') != null) {
            $filterKewenangan = request('filterKewenangan');
        }

        $kewenangan = $user['kewenangan'];
        $district = '';
        $url_app = config('app.url');
        return view('index', compact(
            'url_app',
            'filterKewenangan',
            'kewenangan',
            'tgl_awal',
            'tgl_akhir',
            'start_date',
            'end_date',
            'dts',
            'dte',
            'province',
            'district'
        ));
    }

    public function statistic()
    {
        $data = Http::get('http://182.23.160.133/api/statistic', [
            'start' => request('start'),
            'end' => request('end'),
            'kewenangan' => request('kewenangan'),
            'province' => request('province'),
            'district' => request('district'),
            'resiko' => 'MR'
        ]);

        $responseData = json_decode($data)->data;

        $result = [
            'labels' => array_column($responseData, 'last_kirim'),
            'data' => array_column($responseData, 'total')
        ];

        return response()->json($result);
    }

    public function total()
    {
        $now = Carbon::now()->format('Y-m-d');
        $data = Http::get('http://182.23.160.133/api/filteredTotal', [
            'start' => '2022-01-01',
            'end' => $now,
            'kewenangan' => request('kewenangan'),
            'province' => request('province'),
            'district' => request('district'),
            'byResiko' => 1,
        ]);

        $totals = [
            'MR' => 0,
            'R' => 0,
        ];

        foreach (json_decode($data)->data as $row) {
            if (isset($totals[$row->jenis_resiko])) {
                $totals[$row->jenis_resiko] = number_format($row->total, 0, ',', '.');
            }
        }

        return response()->json([
            'total_mr' => $totals['MR'],
            'total_r' => $totals['R'],
        ]);
    }

    public function totalByDate()
    {
        $data = Http::get('http://182.23.160.133/api/filteredTotal', [
            'start' => request('start'),
            'end' => request('end'),
            'kewenangan' => request('kewenangan'),
            'province' => request('province'),
            'district' => request('district'),
            'byResiko' => 1,
        ]);
        
        $totals = [
            'MR' => 0,
            'R' => 0
        ];
        
        foreach (json_decode($data)->data as $row) {
            if (isset($totals[$row->jenis_resiko])) {
                $totals[$row->jenis_resiko] = $row->total;
            }
        }
        
        return response()->json([
            'total_mr' => $totals['MR'],
            'total_r' => $totals['R'],
        ]);
    }

    public function totalByAuthority()
    {
        $data = Http::get('http://182.23.160.133/api/kewenangan', [
            'start' => request('start'),
            'end' => request('end'),
            'kewenangan' => request('kewenangan'),
            'province' => request('province'),
            'district' => request('district')
        ]);
        
        $mr = [
            'Pusat' => 0,
            'Provinsi' => 0,
            'Kabupaten/Kota' => 0
        ];
        
        $r = [
            'Pusat' => 0,
            'Provinsi' => 0,
            'Kabupaten/Kota' => 0
        ];
        
        foreach (json_decode($data)->data as $row) {
            if ($row->jenis_resiko == 'MR') {
                $mr[$row->kewenangan] = $row->total;
            } else if ($row->jenis_resiko == 'R') {
                $r[$row->kewenangan] = $row->total;
            }
        }
        
        return response()->json([
            'mr_pusat' => $mr['Pusat'],
            'mr_prov' => $mr['Provinsi'],
            'mr_kabkot' => $mr['Kabupaten/Kota'],
            'r_pusat' => $r['Pusat'],
            'r_prov' => $r['Provinsi'],
            'r_kabkot' => $r['Kabupaten/Kota']
        ]);        
    }

    public function cluster()
    {
        $date = (new KegiatanController)->getDate();
        $user = (new KegiatanController)->user_role();
        $start_date = "";
        $end_date = "";

        $provinsi = "";
        $kabkota = "";
        if ($user['provinsi']) {
            $provinsi = $user['provinsi'];
        }
        if ($user['kabkota']) {
            $kabkota = $user['kabkota'];
        }

        if (request('start')) {
            $start_date = str_replace('-', '/', request('start'));
            $end_date = str_replace('-', '/', request('end'));
        } else {
            $start_date = str_replace('-', '/', $date['start']);
            $end_date = str_replace('-', '/', $date['now']);
        }

        if (request('start_date')) {
            if ($user['kewenangan'] == 'Pusat') {
                $cluster = Http::withToken('1|QCyB3h7pys9X0g6vwG2gNoMK5y2dDamjTJSUVXbi')->get('http://amdal.menlhk.go.id/data_mr_api/public/api/cluster?start_date=' . $start_date . '&end_date=' . $end_date);
            } elseif ($user['kewenangan'] == 'Provinsi') {
                $cluster = Http::withToken('1|QCyB3h7pys9X0g6vwG2gNoMK5y2dDamjTJSUVXbi')->get('http://amdal.menlhk.go.id/data_mr_api/public/api/cluster?provinsi=' . $provinsi . '&start_date=' . $start_date . '&end_date=' . $end_date);
            } elseif ($user['kewenangan'] == 'Kabupaten/Kota') {
                $cluster = Http::withToken('1|QCyB3h7pys9X0g6vwG2gNoMK5y2dDamjTJSUVXbi')->get('http://amdal.menlhk.go.id/data_mr_api/public/api/cluster?provinsi=' . $provinsi . '&kabkota=' . $kabkota . '&start_date=' . $start_date . '&end_date=' . $end_date);
            }
        } else {
            if ($user['kewenangan'] == 'Pusat') {
                $cluster = Http::withToken('1|QCyB3h7pys9X0g6vwG2gNoMK5y2dDamjTJSUVXbi')->get('http://amdal.menlhk.go.id/data_mr_api/public/api/cluster');
            } elseif ($user['kewenangan'] == 'Provinsi') {
                $cluster = Http::withToken('1|QCyB3h7pys9X0g6vwG2gNoMK5y2dDamjTJSUVXbi')->get('http://amdal.menlhk.go.id/data_mr_api/public/api/cluster?provinsi=' . $provinsi);
            } elseif ($user['kewenangan'] == 'Kabupaten/Kota') {
                $cluster = Http::withToken('1|QCyB3h7pys9X0g6vwG2gNoMK5y2dDamjTJSUVXbi')->get('http://amdal.menlhk.go.id/data_mr_api/public/api/cluster?provinsi=' . $provinsi . '&kabkota=' . $kabkota);
            }
        }

        $cluster_label = array();
        $cluster_data = array();
        for ($i = 0; $i < count($cluster['data']); $i++) {
            $cluster_label[] = $cluster['data'][$i]['cluster_short'];
            $cluster_data[] = $cluster['data'][$i]['total'];
        }

        return response()->json([
            'cluster_label' => $cluster_label,
            'cluster_data' => $cluster_data,
        ]);
    }

    public function ByProvince()
    {
        $data = Http::get('http://182.23.160.133/api/totalProvince', [
            'start' => request('start'),
            'end' => request('end'),
            'kewenangan' => request('kewenangan'),
            'province' => request('province'),
            'district' => request('district')
        ]);

        $provinces = Http::get('http://182.23.160.133/api/provinces');
        $provs = array_column(json_decode($provinces)->data, 'province');
        $provs = array_map('strtoupper', $provs);
        $rm = ['', null];

        $prov_label = array_values(array_diff($provs, $rm));
        $mr_data = [];
        $r_data = [];

        foreach ($prov_label as $row) {
            $mr_found = false;
            $r_found = false;

            foreach (json_decode($data)->data as $col) {
                if ($col->province == $row) {
                    if ($col->jenis_resiko == 'MR') {
                        $mr_data[] = $col->total;
                        $mr_found = true;
                    } elseif ($col->jenis_resiko == 'R') {
                        $r_data[] = $col->total;
                        $r_found = true;
                    }
                }
            }

            // If no matching province was found, push 0
            if (!$mr_found) {
                $mr_data[] = 0;
            }
            if (!$r_found) {
                $r_data[] = 0;
            }
        }

        return response()->json([
            'labels' => $prov_label,
            'mr_data' => $mr_data,
            'r_data' => $r_data,
        ]);
    }

    public function datatable_mr()
    {
        // Increase memory limit to unlimited
        ini_set('memory_limit', '-1');
        // Set the max execution time limit to unlimited
        ini_set('max_execution_time', 0);
        // Set the execution time limit to unlimited
        set_time_limit(0);

        $search = null;
        if (request('search')['value'] != null) {
            $search = request('search')['value'];
        }

        $total = Http::get('http://182.23.160.133/api/filteredTotal', [
            'search' => $search,
            'start' => request('start_date'),
            'end' => request('end_date'),
            'kewenangan' => request('kewenangan'),
            'province' => request('province'),
            'district' => request('district'),
            'resiko' => 'MR',
        ]);

        $data = Http::get('http://182.23.160.133/api/data', [
            'offset' => request('start'),
            'limit' => request('length'),
            'search' => $search,
            'start' => request('start_date'),
            'end' => request('end_date'),
            'kewenangan' => request('kewenangan'),
            'province' => request('province'),
            'district' => request('district'),
            'resiko' => 'MR'
        ]);

        return response()->json([
            "draw" => intval(request('draw')),
            "recordsTotal" => json_decode($total)->data[0]->total,
            "recordsFiltered" => json_decode($total)->data[0]->total,
            "data" => json_decode($data)->data,
        ]);
    }

    public function datatable_r()
    {
        // Increase memory limit to unlimited
        ini_set('memory_limit', '-1');
        // Set the max execution time limit to unlimited
        ini_set('max_execution_time', 0);
        // Set the execution time limit to unlimited
        set_time_limit(0);

        $search = null;
        if (request('search')['value'] != null) {
            $search = request('search')['value'];
        }

        $total = Http::get('http://182.23.160.133/api/filteredTotal', [
            'search' => $search,
            'start' => request('start_date'),
            'end' => request('end_date'),
            'kewenangan' => request('kewenangan'),
            'province' => request('province'),
            'district' => request('district'),
            'resiko' => 'R',
        ]);

        $data = Http::get('http://182.23.160.133/api/data', [
            'offset' => request('start'),
            'limit' => request('length'),
            'search' => $search,
            'start' => request('start_date'),
            'end' => request('end_date'),
            'kewenangan' => request('kewenangan'),
            'province' => request('province'),
            'district' => request('district'),
            'resiko' => 'R'
        ]);

        return response()->json([
            "draw" => intval(request('draw')),
            "recordsTotal" => json_decode($total)->data[0]->total,
            "recordsFiltered" => json_decode($total)->data[0]->total,
            "data" => json_decode($data)->data,
        ]);
    }

    public function getSpplFile()
    {
        $now = Carbon::now()->format('Y-m-d');

        try {
            $link = Http::withHeaders([
                'Token' => $this->key,
            ])->asForm()->post('https://hub.menlhk.go.id/oss_hub/services/listSPPLFile', [
                'sd' => '2020-01-01',
                'ed' => $now,
                'id_izin' => request('id_izin')
            ])['responSPPLFile'][0]['file_izin'];
            $status = true;
        } catch (Throwable $th) {
            $link = null;
            $status = false;
        }

        return response()->json([
            'status' => $status,
            'link' => 'https://hub.menlhk.go.id/oss_hub/services/read_file_ds?token=' . $this->key . '&url='. $link
        ]);
    }

    public function getPkplhFile()
    {
        $now = Carbon::now()->format('Y-m-d');

        try {
            $link = Http::withHeaders([
                'Token' => $this->key,
            ])->asForm()->post('https://hub.menlhk.go.id/oss_hub/services/listPKPLHFile', [
                'sd' => '2020-01-01',
                'ed' => $now,
                'id_izin' => request('id_izin')
            ])['responPKPLHFile'][0]['file_izin'];
            $status = true;
        } catch (Throwable $th) {
            $link = null;
            $status = false;
        }

        return response()->json([
            'status' => $status,
            'link' => 'https://hub.menlhk.go.id/oss_hub/services/read_file_ds?token=' . $this->key . '&url='. $link
        ]);
    }
}

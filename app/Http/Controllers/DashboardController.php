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

use function GuzzleHttp\json_decode;

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
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = (new Controller)->user_role();
        $start_date = "";
        $end_date = "";

        $province = "";
        $kabkota = "";
        if ($user['provinsi']) {
            $province = getProvince($user['provinsi']);
        }
        if ($user['kabkota']) {
            // $kabkota = $user['kabkota'];
            $province = getProvince($user['provinsi']);
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

        $filterKewenangan = $user['kewenangan'];
        if (request('filterKewenangan') != null) {
            $filterKewenangan = request('filterKewenangan');
        }

        $distincted = request('distincted') ?  true : null;
        $kewenangan = $user['kewenangan'];
        $district = '';

        return view('dashboard', compact(
            'filterKewenangan',
            'kewenangan',
            'tgl_awal',
            'tgl_akhir',
            'start_date',
            'end_date',
            'dts',
            'dte',
            'province',
            'district',
            'distincted'
        ));
    }

    public function statistic()
    {
        // return 'iya';
        $user = $this->user_role();
        $provinsi = "";
        $kabkota = "";
        if ($user['provinsi']) $provinsi = $user['provinsi'];
        if ($user['kabkota']) $kabkota = $user['kabkota'];

        $date = $this->getDate();

        if (request('start_date')) {
            $start_date = str_replace('-', '/', request('start_date'));
            $end_date = str_replace('-', '/', request('amp;end_date'));
            $statistic = Http::withHeaders([
            'Token' => (new Controller)->getKey(),
            ])->get('http://amdal.menlhk.go.id/data_mr_api/public/api/statistik?dokumen=UKL-UPL&filterKewenangan=' . request('amp;kewenangan') . '&kewenangan=' . $user['kewenangan'] . '&provinsi=' . $provinsi . '&kabkota=' . $kabkota . '&perbulan=0&start_date=' . $start_date . '&end_date=' . $end_date);
        } else {
            $start_date = str_replace('-', '/', $date['start']);
            $end_date = str_replace('-', '/', $date['now']);
        }
        
        if (request('perbulan') == 1) {
            $statistic = Http::withHeaders([
            'Token' => (new Controller)->getKey(),
            ])->get('http://amdal.menlhk.go.id/data_mr_api/public/api/statistik?dokumen=UKL-UPL&filterKewenangan=' . request('filterKewenangan') . '&kewenangan=' . $user['kewenangan'] . '&provinsi=' . $provinsi . '&kabkota=' . $kabkota . '&perbulan=1&start_date=' . $start_date . '&end_date=' . $end_date);
        } else if (empty(request(['start_date','end_date','perbulan']))) {
            $statistic = Http::withHeaders([
            'Token' => (new Controller)->getKey(),
            ])->get('http://amdal.menlhk.go.id/data_mr_api/public/api/statistik?dokumen=UKL-UPL&filterKewenangan=' . request('filterKewenangan') . '&kewenangan=' . $user['kewenangan'] . '&provinsi=' . $provinsi . '&kabkota=' . $kabkota);
        }
        
        $important = Http::withHeaders([
            'Token' => (new Controller)->getKey(),
            ])->get('http://amdal.menlhk.go.id/data_mr_api/public/api/statistik?dokumen=all&kewenangan=' . $user['kewenangan'] . '&perbulan=0&start_date=2022-09-21&end_date=2022-09-23');
        $statistik = array();
        for ($i = 0; $i < count($statistic['data']); $i++) {
            if ($statistic['data'][$i]['tanggal_record'] == '2022/09/22') {
                if ($user['kewenangan'] == 'Pusat') {
                    $statistik[$i]['jumlah'] = $important['data'][1]['jumlah'];
                    $statistik[$i]['tanggal_record'] = $statistic['data'][$i]['tanggal_record'];
                } else {
                    $statistik[$i]['jumlah'] = $statistic['data'][$i]['jumlah'];
                    $statistik[$i]['tanggal_record'] = $statistic['data'][$i]['tanggal_record'];
                }
            }
            $statistik[$i]['jumlah'] = $statistic['data'][$i]['jumlah'];
            $statistik[$i]['tanggal_record'] = $statistic['data'][$i]['tanggal_record'];
        }

        $stat_label = array();
        $stat_data = array();
        for ($i = 0; $i < count($statistik); $i++) {
            if (request('perbulan')) {
                $stat_label[] = $statistik[$i]['bulan'];
            } elseif (request('perbulan') == 0 || empty(request('perbulan'))) {
                $stat_label[] = $statistik[$i]['tanggal_record'];
            }
            $stat_data[] = $statistik[$i]['jumlah'];
        }

        $result = [
            'labels' => $stat_label,
            'data' => $stat_data
        ];

        return response()->json($result);
    }

    public function sppl_total()
    {
        if (request('kewenangan') == 'Pusat') {
            $kewenangan = '';
        } else {
            $kewenangan = request('kewenangan');
        }

        $now = Carbon::now()->format('Y-m-d');
        $data = Http::get('http://182.23.160.133/api/filteredTotal', [
            'start' => '2022-01-01',
            'end' => $now,
            'kewenangan' => $kewenangan,
            'province' => request('amp;province'),
            'district' => request('amp;district'),
        ]);

        return response()->json([
            'total_sppl' => number_format(json_decode($data)->data[0]->total, 0, ',', '.')
        ]);
    }

    public function uklupl_total()
    {
        $user = $this->user_role();
        $provinsi = "";
        $kabkota = "";
        if ($user['provinsi']) $provinsi = $user['provinsi'];
        if ($user['kabkota']) $kabkota = $user['kabkota'];

        if ($user['kewenangan'] == 'Pusat') {
            $jum_uklupl = Http::withHeaders([
            'Token' => (new Controller)->getKey(),
            ])->get('http://amdal.menlhk.go.id/data_mr_api/public/api/uklupl_sppl_tot?dokumen=UKL-UPL&distincted=' . request('amp;distincted'));
        } elseif ($user['kewenangan'] == 'Provinsi') {
            $jum_uklupl = Http::withHeaders([
            'Token' => (new Controller)->getKey(),
            ])->get('http://amdal.menlhk.go.id/data_mr_api/public/api/uklupl_sppl_tot?dokumen=UKL-UPL&provinsi=' . $provinsi . '&distincted=' . request('amp;distincted'));
        } elseif ($user['kewenangan'] == 'Kabupaten/Kota') {
            $jum_uklupl = Http::withHeaders([
            'Token' => (new Controller)->getKey(),
            ])->get('http://amdal.menlhk.go.id/data_mr_api/public/api/uklupl_sppl_tot?dokumen=UKL-UPL&provinsi=' . $provinsi . "&kabkota=" . $kabkota . '&distincted=' . request('amp;distincted'));
        }

        if (!request('distincted')) {
            $total = $jum_uklupl['data'][0]['count'];
            $total_uklupl = $total - ($total * 0.02);
        }

        $total_uklupl = $jum_uklupl['data'][0]['count'];
        return response()->json([
            'total_uklupl' => number_format($total_uklupl, 0, ',', '.'),
        ]);
    }

    public function totalByDate()
    {
        // UKL-UPL
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

        if (request('start_date')) {
            $start_date = str_replace('/', '-', request('start_date'));
            $end_date = str_replace('/', '-', request('amp;end_date'));
        } else {
            $start_date = str_replace('/', '-', $date['start']);
            $end_date = str_replace('/', '-', $date['now']);
        }

        $response = Http::withHeaders(['Token' => (new Controller)->getKey()]);
        if ($user['kewenangan'] == 'Pusat') {
            $uklupl = $response->get('http://amdal.menlhk.go.id/data_mr_api/public/api/uklupl_pusat?start_date=' . $start_date . '&end_date=' . $end_date);
        } elseif ($user['kewenangan'] == 'Provinsi') {
            $uklupl = $response->get('http://amdal.menlhk.go.id/data_mr_api/public/api/uklupl_pusat?provinsi=' . $provinsi . '&start_date=' . $start_date . '&end_date=' . $end_date);
        } elseif ($user['kewenangan'] == 'Kabupaten/Kota') {
            $uklupl = $response->get('http://amdal.menlhk.go.id/data_mr_api/public/api/uklupl_pusat?provinsi=' . $provinsi . '&kabkota=' . $kabkota . '&start_date=' . $start_date . '&end_date=' . $end_date);
        }

        $sppl = Http::get('http://182.23.160.133/api/kewenangan', [
            'start' => request('start_date'),
            'end' => request('amp;end_date'),
            // 'kewenangan' => request('amp;kewenangan'),
            'province' => request('amp;province'),
            'district' => request('amp;district')
        ]);

        $total_uklupl = 0;
        $total_sppl = 0;
        foreach (json_decode($uklupl)->data as $col) {
            $total_uklupl += $col->jumlah;
        }
        foreach (json_decode($sppl)->data as $col) {
            $total_sppl += $col->total;
        }

        return response()->json([
            'total_sppl' => $total_sppl,
            'total_uklupl' => $total_uklupl,
        ]);
        #region Unused Query (With own API)
        // $response = Http::withHeaders(['Token' => (new Controller)->getKey()]);
        // if ($user['kewenangan'] == 'Pusat') {
        //     $uklupl = $response->get('http://amdal.menlhk.go.id/data_mr_api/public/api/totalByDate?dokumen=UKL-UPL&start_date=' . $start_date . '&end_date=' . $end_date);
        // } elseif ($user['kewenangan'] != 'Pusat') {
        //     // $uklupl = $response->get('http://amdal.menlhk.go.id/data_mr_api/public/api/totalByDate?dokumen=UKL-UPL&kewenangan=' . $user['kewenangan'] . '&provinsi=' . $provinsi . '&start_date=' . $start_date . '&end_date=' . $end_date);
        //     $uklupl = $response->get('http://amdal.menlhk.go.id/data_mr_api/public/api/totalByDate?dokumen=UKL-UPL&provinsi=' . $provinsi . '&start_date=' . $start_date . '&end_date=' . $end_date);
        // }

        // SPPL
        // $sppl = Http::get('http://182.23.160.133/api/filteredTotal', [
        //     'start' => $start_date,
        //     'end' => $end_date,
        //     // 'kewenangan' => request('amp;kewenangan'),
        //     'province' => request('amp;province'),
        //     'district' => request('amp;district')
        // ]);

        // return response()->json([
        //     'total_sppl' => json_decode($sppl)->data[0]->total,
        //     'total_uklupl' => json_decode($uklupl)->data[0]->jumlah,
        // ]);
        #endregion
        
    }

    public function totalUkluplByAuthority()
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

        if (request('start_date')) {
            $start_date = str_replace('/', '-', request('start_date'));
            $end_date = str_replace('/', '-', request('amp;end_date'));
        } else {
            $start_date = str_replace('/', '-', $date['start']);
            $end_date = str_replace('/', '-', $date['now']);
        }

        $response = Http::withHeaders(['Token' => (new Controller)->getKey()]);
        if (request('amp;kewenangan') == 'Pusat') {
            $uklupl = $response->get('http://amdal.menlhk.go.id/data_mr_api/public/api/uklupl_pusat?start_date=' . $start_date . '&end_date=' . $end_date);
        } elseif (request('amp;kewenangan') == 'Provinsi') {
            $uklupl = $response->get('http://amdal.menlhk.go.id/data_mr_api/public/api/uklupl_pusat?provinsi=' . $provinsi . '&start_date=' . $start_date . '&end_date=' . $end_date);
        } elseif (request('amp;kewenangan') == 'Kabupaten/Kota') {
            $uklupl = $response->get('http://amdal.menlhk.go.id/data_mr_api/public/api/uklupl_pusat?provinsi=' . $provinsi . '&kabkota=' . $kabkota . '&start_date=' . $start_date . '&end_date=' . $end_date);
        }

        return response()->json([
            'pusat' => json_decode($uklupl)->data[2]->jumlah,
            'prov' => json_decode($uklupl)->data[1]->jumlah,
            'kabkot' => json_decode($uklupl)->data[0]->jumlah,
        ]);
    }

    public function totalSpplByAuthority()
    {
        $data = Http::get('http://182.23.160.133/api/kewenangan', [
            'start' => request('start_date'),
            'end' => request('amp;end_date'),
            // 'kewenangan' => request('amp;kewenangan'),
            'province' => request('amp;province'),
            'district' => request('amp;district')
        ]);

        return response()->json([
            'pusat' => json_decode($data)->data[2]->total,
            'prov' => json_decode($data)->data[1]->total,
            'kabkot' => json_decode($data)->data[0]->total,
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

        if (request('start_date')) {
            $start_date = str_replace('-', '/', request('start_date'));
            $end_date = str_replace('-', '/', request('amp;end_date'));
        } else {
            $start_date = str_replace('-', '/', $date['start']);
            $end_date = str_replace('-', '/', $date['now']);
        }

        $response = Http::withHeaders(['Token' => (new Controller)->getKey()]);
        if (request('amp;kewenangan') == 'Pusat') {
            $cluster = $response->get('http://amdal.menlhk.go.id/data_mr_api/public/api/cluster?start_date=' . $start_date . '&end_date=' . $end_date);
        } elseif (request('amp;kewenangan') == 'Provinsi') {
            $cluster = $response->get('http://amdal.menlhk.go.id/data_mr_api/public/api/cluster?provinsi=' . $provinsi . '&start_date=' . $start_date . '&end_date=' . $end_date);
        } elseif (request('amp;kewenangan') == 'Kabupaten/Kota') {
            $cluster = $response->get('http://amdal.menlhk.go.id/data_mr_api/public/api/cluster?provinsi=' . $provinsi . '&kabkota=' . $kabkota . '&start_date=' . $start_date . '&end_date=' . $end_date);
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

        if (request('start_date')) {
            $start_date = str_replace('-', '/', request('start_date'));
            $end_date = str_replace('-', '/', request('amp;end_date'));
        } else {
            $start_date = str_replace('-', '/', $date['start']);
            $end_date = str_replace('-', '/', $date['now']);
        }

        $data = Http::get('http://182.23.160.133/api/totalProvince', [
            'start' => request('start_date'),
            'end' => request('amp;end_date'),
            'kewenangan' => request('amp;kewenangan'),
            'province' => request('amp;province'),
            'district' => request('amp;district')
        ]);

        $provinces = Http::get('http://182.23.160.133/api/provinces');
        $provs = array_column(json_decode($provinces)->data, 'propinsi');
        $provs = array_map('strtoupper', $provs);
        $rm = ['', null];

        $prov_label = array_values(array_unique(array_diff($provs, $rm)));

        $response = Http::withHeaders(['Token' => (new Controller)->getKey()]);
        if ($user['kewenangan'] == 'Pusat') {
            $uklupl = $response->get('http://amdal.menlhk.go.id/data_mr_api/public/api/jml_prov?dokumen=UKL-UPL&start_date=' . $start_date . '&end_date=' . $end_date);
        } elseif ($user['kewenangan'] != 'Pusat') {
            $uklupl = $response->get('http://amdal.menlhk.go.id/data_mr_api/public/api/jml_prov?dokumen=UKL-UPL&kewenangan=' . $user['kewenangan'] . '&provinsi=' . $provinsi . '&start_date=' . $start_date . '&end_date=' . $end_date);
        }

        foreach ($prov_label as $row) {
            foreach (json_decode($data)->data as $col) {
                if (strtoupper($col->province) == $row) {
                    $total_sppl[] = $col->total;
                }
            }

            foreach (json_decode($uklupl)->data as $col) {
                if (strtoupper($col->prov) == $row) {
                    $total_uklupl[] = $col->jumlah;
                }
            }
        }

        return response()->json([
            'labels' => $prov_label,
            'total_sppl' => $total_sppl,
            'total_uklupl' => $total_uklupl
        ]);
    }

    public function datatable_sppl()
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

        $start_date = str_replace('/', '-', request('start_date'));
        $end_date = str_replace('/', '-', request('amp;end_date'));

        $data = Http::get('http://182.23.160.133/api/data', [
            'offset' => request('start'),
            'limit' => request('length'),
            'search' => $search,
            'start' => $start_date,
            'end' => $end_date,
            'kewenangan' => request('amp;kewenangan'),
            'province' => request('amp;province'),
            'district' => request('amp;district'),
        ]);

        return response()->json([
            "draw" => intval(request('draw')),
            "recordsTotal" => json_decode($data)->total,
            "recordsFiltered" => json_decode($data)->total,
            "data" => json_decode($data)->data,
        ]);
    }

    public function datatable_uklupl()
    {
        // Increase memory limit to unlimited
        ini_set('memory_limit', '-1');
        // Set the max execution time limit to unlimited
        ini_set('max_execution_time', 0);
        // Set the execution time limit to unlimited
        set_time_limit(0);

        $date = (new KegiatanController)->getDate();
        $user = (new KegiatanController)->user_role();
        $limit = request('length');
        $start = request('start');
        $search = null;
        if (request('search')['value'] != null) {
            $search = request('search')['value'];
        }

        $user = $this->user_role();
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

        if (request('start_date')) {
            $start_date = str_replace('/', '-', request('start_date'));
            $end_date = str_replace('/', '-', request('amp;end_date'));
        } else {
            $start_date = str_replace('/', '-', $date['start']);
            $end_date = str_replace('/', '-', $date['now']);
        }

        $response = Http::withHeaders(['Token' => (new Controller)->getKey()]);
        if (request('amp;kewenangan') == "Pusat") {
            $api = $response->get('http://amdal.menlhk.go.id/data_mr_api/public/api/kegiatan?search=' . $search . '&offset=' . $start . '&limit=' . $limit . '&start_date=' . $start_date . '&end_date=' . $end_date);
            $total = $response->get('http://amdal.menlhk.go.id/data_mr_api/public/api/filteredTotal?search=' . $search . '&start_date=' . $start_date . '&end_date=' . $end_date);
        } elseif (request('amp;kewenangan') == "Provinsi") {
            $api = $response->get('http://amdal.menlhk.go.id/data_mr_api/public/api/kegiatan?search=' . $search . '&provinsi=' . $provinsi . '&offset=' . $start . '&limit=' . $limit . '&start_date=' . $start_date . '&end_date=' . $end_date);
            $total = $response->get('http://amdal.menlhk.go.id/data_mr_api/public/api/filteredTotal?search=' . $search . '&provinsi=' . $provinsi . '&start_date=' . $start_date . '&end_date=' . $end_date);
        } elseif (request('amp;kewenangan') == 'Kabupaten/Kota') {
            $api = $response->get('http://amdal.menlhk.go.id/data_mr_api/public/api/kegiatan?search=' . $search . '&provinsi=' . $provinsi . '&kabkota=' . $kabkota . '&offset=' . $start . '&limit=' . $limit . '&start_date=' . $start_date . '&end_date=' . $end_date);
            $total = $response->get('http://amdal.menlhk.go.id/data_mr_api/public/api/filteredTotal?search=' . $search . '&provinsi=' . $provinsi . '&kabkota=' . $kabkota . '&start_date=' . $start_date . '&end_date=' . $end_date);
        }
        
        return response()->json([
            "draw" => intval(request('draw')),
            "recordsTotal" => intval($total['data'][0]['count']),
            "recordsFiltered" => intval($total['data'][0]['count']),
            "data" => $api['data'],
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

    public function getLampiranFile()
    {
        $now = Carbon::now()->format('Y-m-d');
        $status = false;

        try {
            $link = Http::withHeaders([
                'Token' => $this->key,
            ])->asForm()->post('https://hub.menlhk.go.id/oss_hub/services/listSPPLAmdal', [
                'sd' => '2020-01-01',
                'ed' => $now,
                'id_izin' => request('id_izin')
            ])['responSPPL'][0]['url_lampiran'];

            if ($link) {
                $status = true;
            }
        } catch (Throwable $th) {
            $link = null;
        }

        return response()->json([
            'status' => $status,
            'link' => $link
        ]);
    }
}

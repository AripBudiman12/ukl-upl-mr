<?php

namespace App\Http\Controllers;

use App\Exports\KegiatanExport;
use App\Models\districts;
use App\Models\provinces;
use App\Models\User;
use Carbon\Carbon;
use Exception;
use GuzzleHttp\Psr7\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\DataTables;

class KegiatanController extends Controller
{
    public function index()
    {
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

        $date = $this->getDate();

        if (request('start_date')) {
            $start_date = str_replace('-', '/', request('start_date'));
            $end_date = str_replace('-', '/', request('end_date'));
            $statistic = Http::withToken('1|QCyB3h7pys9X0g6vwG2gNoMK5y2dDamjTJSUVXbi')->get('http://amdal.menlhk.go.id/data_mr_api/public/api/statistik?dokumen=UKL-UPL&filterKewenangan=' . request('filterKewenangan') . '&kewenangan=' . $user['kewenangan'] . '&provinsi=' . $provinsi . '&kabkota=' . $kabkota . '&perbulan=0&start_date=' . $start_date . '&end_date=' . $end_date);
        } else {
            $start_date = str_replace('-', '/', $date['start']);
            $end_date = str_replace('-', '/', $date['now']);
        }
        
        if (request('perbulan') == 1) {
            $statistic = Http::withToken('1|QCyB3h7pys9X0g6vwG2gNoMK5y2dDamjTJSUVXbi')->get('http://amdal.menlhk.go.id/data_mr_api/public/api/statistik?dokumen=UKL-UPL&filterKewenangan=' . request('filterKewenangan') . '&kewenangan=' . $user['kewenangan'] . '&provinsi=' . $provinsi . '&kabkota=' . $kabkota . '&perbulan=1&start_date=' . $start_date . '&end_date=' . $end_date);
        } else if (empty(request(['start_date','end_date','perbulan']))) {
            $statistic = Http::withToken('1|QCyB3h7pys9X0g6vwG2gNoMK5y2dDamjTJSUVXbi')->get('http://amdal.menlhk.go.id/data_mr_api/public/api/statistik?dokumen=UKL-UPL&filterKewenangan=' . request('filterKewenangan') . '&kewenangan=' . $user['kewenangan'] . '&provinsi=' . $provinsi . '&kabkota=' . $kabkota);
        }
        
        $important = Http::withToken('1|QCyB3h7pys9X0g6vwG2gNoMK5y2dDamjTJSUVXbi')->get('http://amdal.menlhk.go.id/data_mr_api/public/api/statistik?dokumen=all&kewenangan=' . $user['kewenangan'] . '&perbulan=0&start_date=2022-09-21&end_date=2022-09-23');
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

        #region
        if (request('start_date')) {
            if ($user['kewenangan'] == 'Pusat') {
                $uklupl = Http::withToken('1|QCyB3h7pys9X0g6vwG2gNoMK5y2dDamjTJSUVXbi')->get('http://amdal.menlhk.go.id/data_mr_api/public/api/uklupl_pusat?start_date=' . $start_date . '&end_date=' . $end_date);
                $sppl = Http::withToken('1|QCyB3h7pys9X0g6vwG2gNoMK5y2dDamjTJSUVXbi')->get('http://amdal.menlhk.go.id/data_mr_api/public/api/sppl_pusat?start_date=' . $start_date . '&end_date=' . $end_date);
                $uklupl_sppl = Http::withToken('1|QCyB3h7pys9X0g6vwG2gNoMK5y2dDamjTJSUVXbi')->get('http://amdal.menlhk.go.id/data_mr_api/public/api/uklupl_sppl?start_date=' . $start_date . '&end_date=' . $end_date);
                $uklupl_prov = Http::withToken('1|QCyB3h7pys9X0g6vwG2gNoMK5y2dDamjTJSUVXbi')->get('http://amdal.menlhk.go.id/data_mr_api/public/api/jml_prov?dokumen=UKL-UPL&start_date=' . $start_date . '&end_date=' . $end_date);
                $sppl_prov = Http::withToken('1|QCyB3h7pys9X0g6vwG2gNoMK5y2dDamjTJSUVXbi')->get('http://amdal.menlhk.go.id/data_mr_api/public/api/jml_prov?dokumen=SPPL&start_date=' . $start_date . '&end_date=' . $end_date);
                $cluster = Http::withToken('1|QCyB3h7pys9X0g6vwG2gNoMK5y2dDamjTJSUVXbi')->get('http://amdal.menlhk.go.id/data_mr_api/public/api/cluster?start_date=' . $start_date . '&end_date=' . $end_date);
            } elseif ($user['kewenangan'] == 'Provinsi') {
                $uklupl = Http::withToken('1|QCyB3h7pys9X0g6vwG2gNoMK5y2dDamjTJSUVXbi')->get('http://amdal.menlhk.go.id/data_mr_api/public/api/uklupl_pusat?provinsi=' . $provinsi . '&start_date=' . $start_date . '&end_date=' . $end_date);
                $sppl = Http::withToken('1|QCyB3h7pys9X0g6vwG2gNoMK5y2dDamjTJSUVXbi')->get('http://amdal.menlhk.go.id/data_mr_api/public/api/sppl_pusat?provinsi=' . $provinsi . '&start_date=' . $start_date . '&end_date=' . $end_date);
                $uklupl_sppl = Http::withToken('1|QCyB3h7pys9X0g6vwG2gNoMK5y2dDamjTJSUVXbi')->get('http://amdal.menlhk.go.id/data_mr_api/public/api/uklupl_sppl?kewenangan=' . $user['kewenangan'] . '&provinsi=' . $provinsi . '&start_date=' . $start_date . '&end_date=' . $end_date);
                $uklupl_prov = Http::withToken('1|QCyB3h7pys9X0g6vwG2gNoMK5y2dDamjTJSUVXbi')->get('http://amdal.menlhk.go.id/data_mr_api/public/api/jml_prov?dokumen=UKL-UPL&kewenangan=' . $user['kewenangan'] . '&provinsi=' . $provinsi . '&start_date=' . $start_date . '&end_date=' . $end_date);
                $sppl_prov = Http::withToken('1|QCyB3h7pys9X0g6vwG2gNoMK5y2dDamjTJSUVXbi')->get('http://amdal.menlhk.go.id/data_mr_api/public/api/jml_prov?dokumen=SPPL&kewenangan=' . $user['kewenangan'] . '&provinsi=' . $provinsi . '&start_date=' . $start_date . '&end_date=' . $end_date);
                $cluster = Http::withToken('1|QCyB3h7pys9X0g6vwG2gNoMK5y2dDamjTJSUVXbi')->get('http://amdal.menlhk.go.id/data_mr_api/public/api/cluster?provinsi=' . $provinsi . '&start_date=' . $start_date . '&end_date=' . $end_date);
            } elseif ($user['kewenangan'] == 'Kabupaten/Kota') {
                $uklupl = Http::withToken('1|QCyB3h7pys9X0g6vwG2gNoMK5y2dDamjTJSUVXbi')->get('http://amdal.menlhk.go.id/data_mr_api/public/api/uklupl_pusat?provinsi=' . $provinsi . '&kabkota=' . $kabkota . '&start_date=' . $start_date . '&end_date=' . $end_date);
                $sppl = Http::withToken('1|QCyB3h7pys9X0g6vwG2gNoMK5y2dDamjTJSUVXbi')->get('http://amdal.menlhk.go.id/data_mr_api/public/api/sppl_pusat?provinsi=' . $provinsi . '&kabkota=' . $kabkota . '&start_date=' . $start_date . '&end_date=' . $end_date);
                $uklupl_sppl = Http::withToken('1|QCyB3h7pys9X0g6vwG2gNoMK5y2dDamjTJSUVXbi')->get('http://amdal.menlhk.go.id/data_mr_api/public/api/uklupl_sppl?kewenangan=' . $user['kewenangan'] . '&provinsi=' . $provinsi . '&kabkota=' . $kabkota . '&start_date=' . $start_date . '&end_date=' . $end_date);
                $uklupl_prov = Http::withToken('1|QCyB3h7pys9X0g6vwG2gNoMK5y2dDamjTJSUVXbi')->get('http://amdal.menlhk.go.id/data_mr_api/public/api/jml_prov?dokumen=UKL-UPL&kewenangan=' . $user['kewenangan'] . '&provinsi=' . $provinsi . '&start_date=' . $start_date . '&end_date=' . $end_date);
                $sppl_prov = Http::withToken('1|QCyB3h7pys9X0g6vwG2gNoMK5y2dDamjTJSUVXbi')->get('http://amdal.menlhk.go.id/data_mr_api/public/api/jml_prov?dokumen=SPPL&kewenangan=' . $user['kewenangan'] . '&provinsi=' . $provinsi . '&start_date=' . $start_date . '&end_date=' . $end_date);
                $cluster = Http::withToken('1|QCyB3h7pys9X0g6vwG2gNoMK5y2dDamjTJSUVXbi')->get('http://amdal.menlhk.go.id/data_mr_api/public/api/cluster?provinsi=' . $provinsi . '&kabkota=' . $kabkota . '&start_date=' . $start_date . '&end_date=' . $end_date);
            }
        } else {
            if ($user['kewenangan'] == 'Pusat') {
                $uklupl = Http::withToken('1|QCyB3h7pys9X0g6vwG2gNoMK5y2dDamjTJSUVXbi')->get('http://amdal.menlhk.go.id/data_mr_api/public/api/uklupl_pusat');
                $sppl = Http::withToken('1|QCyB3h7pys9X0g6vwG2gNoMK5y2dDamjTJSUVXbi')->get('http://amdal.menlhk.go.id/data_mr_api/public/api/sppl_pusat');
                $uklupl_sppl = Http::withToken('1|QCyB3h7pys9X0g6vwG2gNoMK5y2dDamjTJSUVXbi')->get('http://amdal.menlhk.go.id/data_mr_api/public/api/uklupl_sppl');
                $uklupl_prov = Http::withToken('1|QCyB3h7pys9X0g6vwG2gNoMK5y2dDamjTJSUVXbi')->get('http://amdal.menlhk.go.id/data_mr_api/public/api/jml_prov?dokumen=UKL-UPL');
                $sppl_prov = Http::withToken('1|QCyB3h7pys9X0g6vwG2gNoMK5y2dDamjTJSUVXbi')->get('http://amdal.menlhk.go.id/data_mr_api/public/api/jml_prov?dokumen=SPPL');
                $cluster = Http::withToken('1|QCyB3h7pys9X0g6vwG2gNoMK5y2dDamjTJSUVXbi')->get('http://amdal.menlhk.go.id/data_mr_api/public/api/cluster');
            } elseif ($user['kewenangan'] == 'Provinsi') {
                $uklupl = Http::withToken('1|QCyB3h7pys9X0g6vwG2gNoMK5y2dDamjTJSUVXbi')->get('http://amdal.menlhk.go.id/data_mr_api/public/api/uklupl_pusat?provinsi=' . $provinsi);
                $sppl = Http::withToken('1|QCyB3h7pys9X0g6vwG2gNoMK5y2dDamjTJSUVXbi')->get('http://amdal.menlhk.go.id/data_mr_api/public/api/sppl_pusat?provinsi=' . $provinsi);
                $uklupl_sppl = Http::withToken('1|QCyB3h7pys9X0g6vwG2gNoMK5y2dDamjTJSUVXbi')->get('http://amdal.menlhk.go.id/data_mr_api/public/api/uklupl_sppl?kewenangan=' . $user['kewenangan'] . '&provinsi=' . $provinsi);
                $uklupl_prov = Http::withToken('1|QCyB3h7pys9X0g6vwG2gNoMK5y2dDamjTJSUVXbi')->get('http://amdal.menlhk.go.id/data_mr_api/public/api/jml_prov?dokumen=UKL-UPL&kewenangan=' . $user['kewenangan'] . '&provinsi=' . $provinsi);
                $sppl_prov = Http::withToken('1|QCyB3h7pys9X0g6vwG2gNoMK5y2dDamjTJSUVXbi')->get('http://amdal.menlhk.go.id/data_mr_api/public/api/jml_prov?dokumen=SPPL&kewenangan=' . $user['kewenangan'] . '&provinsi=' . $provinsi);
                $cluster = Http::withToken('1|QCyB3h7pys9X0g6vwG2gNoMK5y2dDamjTJSUVXbi')->get('http://amdal.menlhk.go.id/data_mr_api/public/api/cluster?provinsi=' . $provinsi);
            } elseif ($user['kewenangan'] == 'Kabupaten/Kota') {
                $uklupl = Http::withToken('1|QCyB3h7pys9X0g6vwG2gNoMK5y2dDamjTJSUVXbi')->get('http://amdal.menlhk.go.id/data_mr_api/public/api/uklupl_pusat?provinsi=' . $provinsi . '&kabkota=' . $kabkota);
                $sppl = Http::withToken('1|QCyB3h7pys9X0g6vwG2gNoMK5y2dDamjTJSUVXbi')->get('http://amdal.menlhk.go.id/data_mr_api/public/api/sppl_pusat?provinsi=' . $provinsi . '&kabkota=' . $kabkota);
                $uklupl_sppl = Http::withToken('1|QCyB3h7pys9X0g6vwG2gNoMK5y2dDamjTJSUVXbi')->get('http://amdal.menlhk.go.id/data_mr_api/public/api/uklupl_sppl?kewenangan=' . $user['kewenangan'] . '&provinsi=' . $provinsi . '&kabkota=' . $kabkota);
                $uklupl_prov = Http::withToken('1|QCyB3h7pys9X0g6vwG2gNoMK5y2dDamjTJSUVXbi')->get('http://amdal.menlhk.go.id/data_mr_api/public/api/jml_prov?dokumen=UKL-UPL&kewenangan=' . $user['kewenangan'] . '&provinsi=' . $provinsi);
                $sppl_prov = Http::withToken('1|QCyB3h7pys9X0g6vwG2gNoMK5y2dDamjTJSUVXbi')->get('http://amdal.menlhk.go.id/data_mr_api/public/api/jml_prov?dokumen=SPPL&kewenangan=' . $user['kewenangan'] . '&provinsi=' . $provinsi);
                $cluster = Http::withToken('1|QCyB3h7pys9X0g6vwG2gNoMK5y2dDamjTJSUVXbi')->get('http://amdal.menlhk.go.id/data_mr_api/public/api/cluster?provinsi=' . $provinsi . '&kabkota=' . $kabkota);
            }
        }
        #endregion

        #region Total UKL-UPL dan SPPL
        $tanggal_now = Carbon::now()->format('Y/m/d');
        if ($user['kewenangan'] == 'Pusat') {
            $jum_uklupl = Http::withToken('1|QCyB3h7pys9X0g6vwG2gNoMK5y2dDamjTJSUVXbi')->get('http://amdal.menlhk.go.id/data_mr_api/public/api/uklupl_sppl_tot?dokumen=UKL-UPL');
            $jum_sppl = Http::withToken('1|QCyB3h7pys9X0g6vwG2gNoMK5y2dDamjTJSUVXbi')->get('http://amdal.menlhk.go.id/data_mr_api/public/api/uklupl_sppl_tot?dokumen=SPPL');
        } elseif ($user['kewenangan'] == 'Provinsi') {
            $jum_uklupl = Http::withToken('1|QCyB3h7pys9X0g6vwG2gNoMK5y2dDamjTJSUVXbi')->get('http://amdal.menlhk.go.id/data_mr_api/public/api/uklupl_sppl_tot?dokumen=UKL-UPL&provinsi=' . $provinsi);
            $jum_sppl = Http::withToken('1|QCyB3h7pys9X0g6vwG2gNoMK5y2dDamjTJSUVXbi')->get('http://amdal.menlhk.go.id/data_mr_api/public/api/uklupl_sppl_tot?dokumen=SPPL&provinsi=' . $provinsi);
        } elseif ($user['kewenangan'] == 'Kabupaten/Kota') {
            $jum_uklupl = Http::withToken('1|QCyB3h7pys9X0g6vwG2gNoMK5y2dDamjTJSUVXbi')->get('http://amdal.menlhk.go.id/data_mr_api/public/api/uklupl_sppl_tot?dokumen=UKL-UPL&provinsi=' . $provinsi . "&kabkota=" . $kabkota);
            $jum_sppl = Http::withToken('1|QCyB3h7pys9X0g6vwG2gNoMK5y2dDamjTJSUVXbi')->get('http://amdal.menlhk.go.id/data_mr_api/public/api/uklupl_sppl_tot?dokumen=SPPL&provinsi=' . $provinsi . "&kabkota=" . $kabkota);
        }

        $total_uklupl = $jum_uklupl['data'][0]['count'];
        $total_sppl = $jum_sppl['data'][0]['count'];
        #endregion

        $tot_uklupl = 0;
        for ($i = 0; $i < count($uklupl_prov['data']); $i++) {
            $tot_uklupl += $uklupl_prov['data'][$i]['jumlah'];
        }

        $tot_sppl = 0;
        for ($i = 0; $i < count($sppl_prov['data']); $i++) {
            $tot_sppl += $sppl_prov['data'][$i]['jumlah'];
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

        $prov_uklupl = array();
        $prov_sppl = array();
        $prov_label = array();

        if (count($uklupl_prov['data']) > count($sppl_prov['data'])) {
            for ($i = 0; $i < count($uklupl_prov['data']); $i++ ) {
                // fix this
            } 
        }

        for ($i = 0; $i < count($sppl_prov['data']); $i++ ) {
            if ($sppl_prov['data'][$i]['jumlah'] != null && $sppl_prov['data'][$i]['prov'] != null) {
                $prov_label[] = $uklupl_prov['data'][$i]['prov'];
                $prov_sppl[] = $sppl_prov['data'][$i]['jumlah'];
                $prov_uklupl[] = $uklupl_prov['data'][$i]['jumlah'];
            }
        }

        $cluster_label = array();
        $cluster_data = array();
        for ($i = 0; $i < count($cluster['data']); $i++) {
            $cluster_label[] = $cluster['data'][$i]['cluster_short'];
            $cluster_data[] = $cluster['data'][$i]['total'];
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

        $tanggal = $this->getDate();
        $tgl_awal = $tanggal['start'];
        $tgl_akhir = $tanggal['now'];

        $dts = new Carbon($start_date);
        $dte = new Carbon($end_date);
        setlocale(\LC_TIME,'ID');

        $dts = $dts->formatLocalized('%e %B %Y');
        $dte = $dte->formatLocalized('%e %B %Y');

        if ($user['kewenangan'] == "Pusat") {
            $total = Http::withToken('1|QCyB3h7pys9X0g6vwG2gNoMK5y2dDamjTJSUVXbi')->get('http://amdal.menlhk.go.id/data_mr_api/public/api/filteredTotal?start_date=' . $start_date . '&end_date=' . $end_date);
        } elseif ($user['kewenangan'] == "Provinsi") {
            $total = Http::withToken('1|QCyB3h7pys9X0g6vwG2gNoMK5y2dDamjTJSUVXbi')->get('http://amdal.menlhk.go.id/data_mr_api/public/api/filteredTotal?provinsi=' . $provinsi . '&start_date=' . $start_date . '&end_date=' . $end_date);
        } elseif ($user['kewenangan'] == 'Kabupaten/Kota') {
            $total = Http::withToken('1|QCyB3h7pys9X0g6vwG2gNoMK5y2dDamjTJSUVXbi')->get('http://amdal.menlhk.go.id/data_mr_api/public/api/filteredTotal?provinsi=' . $provinsi . '&kabkota=' . $kabkota . '&start_date=' . $start_date . '&end_date=' . $end_date);
        }
        $totalData = intval($total['data'][0]['count']);

        $filterKewenangan = null;
        if (request('filterKewenangan') != null) {
            $filterKewenangan = request('filterKewenangan');
        }

        $kewenangan = $user['kewenangan'];
        return view('index-backup', compact(
            'filterKewenangan',
            'kewenangan',
            'uklupl_data',
            'sppl_data',
            'uklupl_sppl_data',
            'prov_label',
            'prov_uklupl',
            'prov_sppl',
            'stat_label',
            'stat_data',
            'cluster_label',
            'cluster_data',
            'total_uklupl',
            'total_sppl',
            'tgl_awal',
            'tgl_akhir',
            'tot_uklupl',
            'tot_sppl',
            'start_date',
            'end_date',
            'totalData',
            'dts',
            'dte'
        ));
    }

    public function data()
    {
        $feed = 'https://docs.google.com/spreadsheets/d/e/2PACX-1vRh-J-ibk1-2ypJ0twyxEChh6Wd-zbQFLylo0xUrrgJWaKiYo_VJmiAqWPSF-RD4K0fFAtQx9jXtt2u/pub?output=csv';

        // variabel ini akan digunakan untuk melooping data
        $keys = array();
        $jadwal = array();

        //fungsi untuk mengkonversi csv ke array asosiatif
        function csvToArray($file, $delimiter) {
            if (($handle = fopen($file, 'r')) !== FALSE) {
                $i = 0;
                while (($lineArray = fgetcsv($handle, 4000, $delimiter, '"')) !== FALSE) {
                for ($j = 0; $j < count($lineArray); $j++) {
                    $arr[$i][$j] = $lineArray[$j];
                }
                $i++;
                }
                fclose($handle);
            }
            return $arr;
        }

        $data = csvToArray($feed, ',');

        $count = count($data) - 1;

        $labels = array_shift($data);

        foreach ($labels as $label) {
            $keys[] = $label;
        }

        for ($j = 0; $j < $count; $j++) {
            $d = array_combine($keys, $data[$j]);
            $jadwal[$j] = $d;
        }

        $data = collect($jadwal);

        return $data;
    }

    public function datatable()
    {
        $limit = request('length');
        $start = request('start');
        $search = null;
        if (request('search')['value'] != null) {
            $search = request('search')['value'];
        }

        $user = $this->user_role();
        $start_date = request('date_start');
        $end_date = request('amp;date_end');

        $provinsi = "";
        $kabkota = "";
        if ($user['provinsi']) {
            $provinsi = $user['provinsi'];
        }
        if ($user['kabkota']) {
            $kabkota = $user['kabkota'];
        }

        #region
        // if (request('start_date')) {
        //     if ($user['kewenangan'] == "Pusat") {
        //         $api = Http::withToken('1|QCyB3h7pys9X0g6vwG2gNoMK5y2dDamjTJSUVXbi')->get('http://amdal.menlhk.go.id/data_mr_api/public/api/kegiatan?offset=' . $start . '&limit=' . $limit . '&start_date=' . $start_date . '&end_date=' . $end_date);
        //     } elseif ($user['kewenangan'] == "Provinsi") {
        //         $api = Http::withToken('1|QCyB3h7pys9X0g6vwG2gNoMK5y2dDamjTJSUVXbi')->get('http://amdal.menlhk.go.id/data_mr_api/public/api/kegiatan?provinsi=' . $provinsi . '&offset=' . $start . '&limit=' . $limit . '&start_date=' . $start_date . '&end_date=' . $end_date);
        //     } elseif ($user['kewenangan'] == 'Kabupaten/Kota') {
        //         $api = Http::withToken('1|QCyB3h7pys9X0g6vwG2gNoMK5y2dDamjTJSUVXbi')->get('http://amdal.menlhk.go.id/data_mr_api/public/api/kegiatan?provinsi=' . $provinsi . '&kabkota=' . $kabkota . '&offset=' . $start . '&limit=' . $limit . '&start_date=' . $start_date . '&end_date=' . $end_date);
        //     }
        // } else {
        //     if ($user['kewenangan'] == "Pusat") {
        //         $api = Http::withToken('1|QCyB3h7pys9X0g6vwG2gNoMK5y2dDamjTJSUVXbi')->get('http://amdal.menlhk.go.id/data_mr_api/public/api/kegiatan?offset=' . $start . '&limit=' . $limit);
        //     } elseif ($user['kewenangan'] == "Provinsi") {
        //         $api = Http::withToken('1|QCyB3h7pys9X0g6vwG2gNoMK5y2dDamjTJSUVXbi')->get('http://amdal.menlhk.go.id/data_mr_api/public/api/kegiatan?provinsi=' . $provinsi . '&offset=' . $start . '&limit=' . $limit);
        //     } elseif ($user['kewenangan'] == 'Kabupaten/Kota') {
        //         $api = Http::withToken('1|QCyB3h7pys9X0g6vwG2gNoMK5y2dDamjTJSUVXbi')->get('http://amdal.menlhk.go.id/data_mr_api/public/api/kegiatan?provinsi=' . $provinsi . '&kabkota=' . $kabkota . '&offset=' . $start . '&limit=' . $limit);
        //     }
        // }
        #endregion

        if ($search != null) {
            if ($user['kewenangan'] == "Pusat") {
                $api = Http::withToken('1|QCyB3h7pys9X0g6vwG2gNoMK5y2dDamjTJSUVXbi')->get('http://amdal.menlhk.go.id/data_mr_api/public/api/kegiatan?search=' . $search . '&offset=' . $start . '&limit=' . $limit . '&start_date=' . $start_date . '&end_date=' . $end_date);
                $total = Http::withToken('1|QCyB3h7pys9X0g6vwG2gNoMK5y2dDamjTJSUVXbi')->get('http://amdal.menlhk.go.id/data_mr_api/public/api/filteredTotal?search=' . $search . '&start_date=' . $start_date . '&end_date=' . $end_date);
            } elseif ($user['kewenangan'] == "Provinsi") {
                $api = Http::withToken('1|QCyB3h7pys9X0g6vwG2gNoMK5y2dDamjTJSUVXbi')->get('http://amdal.menlhk.go.id/data_mr_api/public/api/kegiatan?search=' . $search . '&provinsi=' . $provinsi . '&offset=' . $start . '&limit=' . $limit . '&start_date=' . $start_date . '&end_date=' . $end_date);
                $total = Http::withToken('1|QCyB3h7pys9X0g6vwG2gNoMK5y2dDamjTJSUVXbi')->get('http://amdal.menlhk.go.id/data_mr_api/public/api/filteredTotal?search=' . $search . '&provinsi=' . $provinsi . '&start_date=' . $start_date . '&end_date=' . $end_date);
            } elseif ($user['kewenangan'] == 'Kabupaten/Kota') {
                $api = Http::withToken('1|QCyB3h7pys9X0g6vwG2gNoMK5y2dDamjTJSUVXbi')->get('http://amdal.menlhk.go.id/data_mr_api/public/api/kegiatan?search=' . $search . '&provinsi=' . $provinsi . '&kabkota=' . $kabkota . '&offset=' . $start . '&limit=' . $limit . '&start_date=' . $start_date . '&end_date=' . $end_date);
                $total = Http::withToken('1|QCyB3h7pys9X0g6vwG2gNoMK5y2dDamjTJSUVXbi')->get('http://amdal.menlhk.go.id/data_mr_api/public/api/filteredTotal?search=' . $search . '&provinsi=' . $provinsi . '&kabkota=' . $kabkota . '&start_date=' . $start_date . '&end_date=' . $end_date);
            }
        } else {
            if ($user['kewenangan'] == "Pusat") {
                $api = Http::withToken('1|QCyB3h7pys9X0g6vwG2gNoMK5y2dDamjTJSUVXbi')->get('http://amdal.menlhk.go.id/data_mr_api/public/api/kegiatan?offset=' . $start . '&limit=' . $limit . '&start_date=' . $start_date . '&end_date=' . $end_date);
                $total = Http::withToken('1|QCyB3h7pys9X0g6vwG2gNoMK5y2dDamjTJSUVXbi')->get('http://amdal.menlhk.go.id/data_mr_api/public/api/filteredTotal?start_date=' . $start_date . '&end_date=' . $end_date);
            } elseif ($user['kewenangan'] == "Provinsi") {
                $api = Http::withToken('1|QCyB3h7pys9X0g6vwG2gNoMK5y2dDamjTJSUVXbi')->get('http://amdal.menlhk.go.id/data_mr_api/public/api/kegiatan?provinsi=' . $provinsi . '&offset=' . $start . '&limit=' . $limit . '&start_date=' . $start_date . '&end_date=' . $end_date);
                $total = Http::withToken('1|QCyB3h7pys9X0g6vwG2gNoMK5y2dDamjTJSUVXbi')->get('http://amdal.menlhk.go.id/data_mr_api/public/api/filteredTotal?provinsi=' . $provinsi . '&start_date=' . $start_date . '&end_date=' . $end_date);
            } elseif ($user['kewenangan'] == 'Kabupaten/Kota') {
                $api = Http::withToken('1|QCyB3h7pys9X0g6vwG2gNoMK5y2dDamjTJSUVXbi')->get('http://amdal.menlhk.go.id/data_mr_api/public/api/kegiatan?provinsi=' . $provinsi . '&kabkota=' . $kabkota . '&offset=' . $start . '&limit=' . $limit . '&start_date=' . $start_date . '&end_date=' . $end_date);
                $total = Http::withToken('1|QCyB3h7pys9X0g6vwG2gNoMK5y2dDamjTJSUVXbi')->get('http://amdal.menlhk.go.id/data_mr_api/public/api/filteredTotal?provinsi=' . $provinsi . '&kabkota=' . $kabkota . '&start_date=' . $start_date . '&end_date=' . $end_date);
            }
        }
        
        // return 'http://amdal.menlhk.go.id/data_mr_api/public/api/kegiatan?search=' . $search . 'offset=' . $start . '&limit=' . $limit . '&start_date=' . $start_date . '&end_date=' . $end_date;
        // return 'http://amdal.menlhk.go.id/data_mr_api/public/api/filteredTotal?start_date=' . $start_date . '&end_date=' . $end_date;
        // return $total;
        // $total = Http::withToken('1|QCyB3h7pys9X0g6vwG2gNoMK5y2dDamjTJSUVXbi')->get('http://amdal.menlhk.go.id/data_mr_api/public/api/filteredTotal?start_date=2023-04-01&end_date=2023-07-24');
        return response()->json([
            "draw" => intval(request('draw')),
            "recordsTotal" => intval($total['data'][0]['count']),
            "recordsFiltered" => intval($total['data'][0]['count']),
            "data" => $api['data'],
        ]);
    }

    public function getDate()
    {
        $month = Carbon::now()->subMonths(3)->format('Y-m');
        $now = Carbon::now()->format('Y-m-d');

        $date = Carbon::now()->format('d');
        $subtract = $date - ($date - 1);

        $start = $month . "-0" . $subtract;

        $data = [
            'start' => $start,
            'now' => $now
        ];

        return $data;
    }

    public function export()
    {
        try {
            $exported = Excel::download(new KegiatanExport(request('length'), request('start'), request('date_start'), request('date_end')), 'uklupl_mr.xlsx');
        } catch (\Exception $e) {
            return back()->with('message', 'Data yang diambil terlalu banyak, coba untuk kurangi jumlah file yang diambil');
        }
        return $exported;
    }

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
            $prov = provinces::find($kewenangan->id_province_name)->name;
        }

        $kabkota = null;
        if ($kewenangan->id_district_name) {
            $kabkota = districts::find($kewenangan->id_district_name)->name;
        }

        $data = [
            'kewenangan' => $kewenangan->authority,
            'provinsi' => $prov,
            'kabkota' => $kabkota
        ];

        return $data;
    }
}

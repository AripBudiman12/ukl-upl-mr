<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ChartController extends Controller
{
    public function statistic()
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

        return response()->json([
            'data' => $stat_data,
            'label' => $stat_label
        ]);
    }

    public function total()
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
            $end_date = str_replace('-', '/', request('end_date'));
        } else {
            $start_date = str_replace('-', '/', $date['start']);
            $end_date = str_replace('-', '/', $date['now']);
        }
        
        #region
        if (request('start_date')) {
            if ($user['kewenangan'] == 'Pusat') {
                $uklupl_prov = Http::withToken('1|QCyB3h7pys9X0g6vwG2gNoMK5y2dDamjTJSUVXbi')->get('http://amdal.menlhk.go.id/data_mr_api/public/api/jml_prov?dokumen=UKL-UPL&start_date=' . $start_date . '&end_date=' . $end_date);
                $sppl_prov = Http::withToken('1|QCyB3h7pys9X0g6vwG2gNoMK5y2dDamjTJSUVXbi')->get('http://amdal.menlhk.go.id/data_mr_api/public/api/jml_prov?dokumen=SPPL&start_date=' . $start_date . '&end_date=' . $end_date);
            } elseif ($user['kewenangan'] == 'Provinsi') {
                $uklupl_prov = Http::withToken('1|QCyB3h7pys9X0g6vwG2gNoMK5y2dDamjTJSUVXbi')->get('http://amdal.menlhk.go.id/data_mr_api/public/api/jml_prov?dokumen=UKL-UPL&kewenangan=' . $user['kewenangan'] . '&provinsi=' . $provinsi . '&start_date=' . $start_date . '&end_date=' . $end_date);
                $sppl_prov = Http::withToken('1|QCyB3h7pys9X0g6vwG2gNoMK5y2dDamjTJSUVXbi')->get('http://amdal.menlhk.go.id/data_mr_api/public/api/jml_prov?dokumen=SPPL&kewenangan=' . $user['kewenangan'] . '&provinsi=' . $provinsi . '&start_date=' . $start_date . '&end_date=' . $end_date);
            } elseif ($user['kewenangan'] == 'Kabupaten/Kota') {
                $uklupl_prov = Http::withToken('1|QCyB3h7pys9X0g6vwG2gNoMK5y2dDamjTJSUVXbi')->get('http://amdal.menlhk.go.id/data_mr_api/public/api/jml_prov?dokumen=UKL-UPL&kewenangan=' . $user['kewenangan'] . '&provinsi=' . $provinsi . '&start_date=' . $start_date . '&end_date=' . $end_date);
                $sppl_prov = Http::withToken('1|QCyB3h7pys9X0g6vwG2gNoMK5y2dDamjTJSUVXbi')->get('http://amdal.menlhk.go.id/data_mr_api/public/api/jml_prov?dokumen=SPPL&kewenangan=' . $user['kewenangan'] . '&provinsi=' . $provinsi . '&start_date=' . $start_date . '&end_date=' . $end_date);
            }
        } else {
            if ($user['kewenangan'] == 'Pusat') {
                $uklupl_prov = Http::withToken('1|QCyB3h7pys9X0g6vwG2gNoMK5y2dDamjTJSUVXbi')->get('http://amdal.menlhk.go.id/data_mr_api/public/api/jml_prov?dokumen=UKL-UPL');
                $sppl_prov = Http::withToken('1|QCyB3h7pys9X0g6vwG2gNoMK5y2dDamjTJSUVXbi')->get('http://amdal.menlhk.go.id/data_mr_api/public/api/jml_prov?dokumen=SPPL');
            } elseif ($user['kewenangan'] == 'Provinsi') {
                $uklupl_prov = Http::withToken('1|QCyB3h7pys9X0g6vwG2gNoMK5y2dDamjTJSUVXbi')->get('http://amdal.menlhk.go.id/data_mr_api/public/api/jml_prov?dokumen=UKL-UPL&kewenangan=' . $user['kewenangan'] . '&provinsi=' . $provinsi);
                $sppl_prov = Http::withToken('1|QCyB3h7pys9X0g6vwG2gNoMK5y2dDamjTJSUVXbi')->get('http://amdal.menlhk.go.id/data_mr_api/public/api/jml_prov?dokumen=SPPL&kewenangan=' . $user['kewenangan'] . '&provinsi=' . $provinsi);
            } elseif ($user['kewenangan'] == 'Kabupaten/Kota') {
                $uklupl_prov = Http::withToken('1|QCyB3h7pys9X0g6vwG2gNoMK5y2dDamjTJSUVXbi')->get('http://amdal.menlhk.go.id/data_mr_api/public/api/jml_prov?dokumen=UKL-UPL&kewenangan=' . $user['kewenangan'] . '&provinsi=' . $provinsi);
                $sppl_prov = Http::withToken('1|QCyB3h7pys9X0g6vwG2gNoMK5y2dDamjTJSUVXbi')->get('http://amdal.menlhk.go.id/data_mr_api/public/api/jml_prov?dokumen=SPPL&kewenangan=' . $user['kewenangan'] . '&provinsi=' . $provinsi);
            }
        }
        #endregion

        $tot_uklupl = 0;
        for ($i = 0; $i < count($uklupl_prov['data']); $i++) {
            $tot_uklupl += $uklupl_prov['data'][$i]['jumlah'];
        }

        $tot_sppl = 0;
        for ($i = 0; $i < count($sppl_prov['data']); $i++) {
            $tot_sppl += $sppl_prov['data'][$i]['jumlah'];
        }

        return response()->json([
            'tot_uklupl' => $tot_uklupl,
            'tot_sppl' => $tot_sppl,
        ]);
    }

    public function authority()
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
            $end_date = str_replace('-', '/', request('end_date'));
        } else {
            $start_date = str_replace('-', '/', $date['start']);
            $end_date = str_replace('-', '/', $date['now']);
        }

        if (request('start_date')) {
            if ($user['kewenangan'] == 'Pusat') {
                $uklupl = Http::withToken('1|QCyB3h7pys9X0g6vwG2gNoMK5y2dDamjTJSUVXbi')->get('http://amdal.menlhk.go.id/data_mr_api/public/api/uklupl_pusat?start_date=' . $start_date . '&end_date=' . $end_date);
                $sppl = Http::withToken('1|QCyB3h7pys9X0g6vwG2gNoMK5y2dDamjTJSUVXbi')->get('http://amdal.menlhk.go.id/data_mr_api/public/api/sppl_pusat?start_date=' . $start_date . '&end_date=' . $end_date);
            } elseif ($user['kewenangan'] == 'Provinsi') {
                $uklupl = Http::withToken('1|QCyB3h7pys9X0g6vwG2gNoMK5y2dDamjTJSUVXbi')->get('http://amdal.menlhk.go.id/data_mr_api/public/api/uklupl_pusat?provinsi=' . $provinsi . '&start_date=' . $start_date . '&end_date=' . $end_date);
                $sppl = Http::withToken('1|QCyB3h7pys9X0g6vwG2gNoMK5y2dDamjTJSUVXbi')->get('http://amdal.menlhk.go.id/data_mr_api/public/api/sppl_pusat?provinsi=' . $provinsi . '&start_date=' . $start_date . '&end_date=' . $end_date);
            } elseif ($user['kewenangan'] == 'Kabupaten/Kota') {
                $uklupl = Http::withToken('1|QCyB3h7pys9X0g6vwG2gNoMK5y2dDamjTJSUVXbi')->get('http://amdal.menlhk.go.id/data_mr_api/public/api/uklupl_pusat?provinsi=' . $provinsi . '&kabkota=' . $kabkota . '&start_date=' . $start_date . '&end_date=' . $end_date);
                $sppl = Http::withToken('1|QCyB3h7pys9X0g6vwG2gNoMK5y2dDamjTJSUVXbi')->get('http://amdal.menlhk.go.id/data_mr_api/public/api/sppl_pusat?provinsi=' . $provinsi . '&kabkota=' . $kabkota . '&start_date=' . $start_date . '&end_date=' . $end_date);
            }
        } else {
            if ($user['kewenangan'] == 'Pusat') {
                $uklupl = Http::withToken('1|QCyB3h7pys9X0g6vwG2gNoMK5y2dDamjTJSUVXbi')->get('http://amdal.menlhk.go.id/data_mr_api/public/api/uklupl_pusat');
                $sppl = Http::withToken('1|QCyB3h7pys9X0g6vwG2gNoMK5y2dDamjTJSUVXbi')->get('http://amdal.menlhk.go.id/data_mr_api/public/api/sppl_pusat');
            } elseif ($user['kewenangan'] == 'Provinsi') {
                $uklupl = Http::withToken('1|QCyB3h7pys9X0g6vwG2gNoMK5y2dDamjTJSUVXbi')->get('http://amdal.menlhk.go.id/data_mr_api/public/api/uklupl_pusat?provinsi=' . $provinsi);
                $sppl = Http::withToken('1|QCyB3h7pys9X0g6vwG2gNoMK5y2dDamjTJSUVXbi')->get('http://amdal.menlhk.go.id/data_mr_api/public/api/sppl_pusat?provinsi=' . $provinsi);
            } elseif ($user['kewenangan'] == 'Kabupaten/Kota') {
                $uklupl = Http::withToken('1|QCyB3h7pys9X0g6vwG2gNoMK5y2dDamjTJSUVXbi')->get('http://amdal.menlhk.go.id/data_mr_api/public/api/uklupl_pusat?provinsi=' . $provinsi . '&kabkota=' . $kabkota);
                $sppl = Http::withToken('1|QCyB3h7pys9X0g6vwG2gNoMK5y2dDamjTJSUVXbi')->get('http://amdal.menlhk.go.id/data_mr_api/public/api/sppl_pusat?provinsi=' . $provinsi . '&kabkota=' . $kabkota);
            }
        }

        $uklupl_data = array();
        for ($i = 0; $i < count($uklupl['data']); $i++ ) {
            $uklupl_data[] = $uklupl['data'][$i]['jumlah'];
        }

        $sppl_data = array();
        for ($i = 0; $i < count($sppl['data']); $i++ ) {
            $sppl_data[] = $sppl['data'][$i]['jumlah'];
        }

        return response()->json([
            'sppl_data' => $sppl_data,
            'uklupl_data' => $uklupl_data
        ]);
    }

    public function province()
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
            $end_date = str_replace('-', '/', request('end_date'));
        } else {
            $start_date = str_replace('-', '/', $date['start']);
            $end_date = str_replace('-', '/', $date['now']);
        }

        if (request('start_date')) {
            if ($user['kewenangan'] == 'Pusat') {
                $uklupl_prov = Http::withToken('1|QCyB3h7pys9X0g6vwG2gNoMK5y2dDamjTJSUVXbi')->get('http://amdal.menlhk.go.id/data_mr_api/public/api/jml_prov?dokumen=UKL-UPL&start_date=' . $start_date . '&end_date=' . $end_date);
                $sppl_prov = Http::withToken('1|QCyB3h7pys9X0g6vwG2gNoMK5y2dDamjTJSUVXbi')->get('http://amdal.menlhk.go.id/data_mr_api/public/api/jml_prov?dokumen=SPPL&start_date=' . $start_date . '&end_date=' . $end_date);
            } elseif ($user['kewenangan'] == 'Provinsi') {
                $uklupl_prov = Http::withToken('1|QCyB3h7pys9X0g6vwG2gNoMK5y2dDamjTJSUVXbi')->get('http://amdal.menlhk.go.id/data_mr_api/public/api/jml_prov?dokumen=UKL-UPL&kewenangan=' . $user['kewenangan'] . '&provinsi=' . $provinsi . '&start_date=' . $start_date . '&end_date=' . $end_date);
                $sppl_prov = Http::withToken('1|QCyB3h7pys9X0g6vwG2gNoMK5y2dDamjTJSUVXbi')->get('http://amdal.menlhk.go.id/data_mr_api/public/api/jml_prov?dokumen=SPPL&kewenangan=' . $user['kewenangan'] . '&provinsi=' . $provinsi . '&start_date=' . $start_date . '&end_date=' . $end_date);
            } elseif ($user['kewenangan'] == 'Kabupaten/Kota') {
                $uklupl_prov = Http::withToken('1|QCyB3h7pys9X0g6vwG2gNoMK5y2dDamjTJSUVXbi')->get('http://amdal.menlhk.go.id/data_mr_api/public/api/jml_prov?dokumen=UKL-UPL&kewenangan=' . $user['kewenangan'] . '&provinsi=' . $provinsi . '&start_date=' . $start_date . '&end_date=' . $end_date);
                $sppl_prov = Http::withToken('1|QCyB3h7pys9X0g6vwG2gNoMK5y2dDamjTJSUVXbi')->get('http://amdal.menlhk.go.id/data_mr_api/public/api/jml_prov?dokumen=SPPL&kewenangan=' . $user['kewenangan'] . '&provinsi=' . $provinsi . '&start_date=' . $start_date . '&end_date=' . $end_date);
            }
        } else {
            if ($user['kewenangan'] == 'Pusat') {
                $uklupl_prov = Http::withToken('1|QCyB3h7pys9X0g6vwG2gNoMK5y2dDamjTJSUVXbi')->get('http://amdal.menlhk.go.id/data_mr_api/public/api/jml_prov?dokumen=UKL-UPL');
                $sppl_prov = Http::withToken('1|QCyB3h7pys9X0g6vwG2gNoMK5y2dDamjTJSUVXbi')->get('http://amdal.menlhk.go.id/data_mr_api/public/api/jml_prov?dokumen=SPPL');
            } elseif ($user['kewenangan'] == 'Provinsi') {
                $uklupl_prov = Http::withToken('1|QCyB3h7pys9X0g6vwG2gNoMK5y2dDamjTJSUVXbi')->get('http://amdal.menlhk.go.id/data_mr_api/public/api/jml_prov?dokumen=UKL-UPL&kewenangan=' . $user['kewenangan'] . '&provinsi=' . $provinsi);
                $sppl_prov = Http::withToken('1|QCyB3h7pys9X0g6vwG2gNoMK5y2dDamjTJSUVXbi')->get('http://amdal.menlhk.go.id/data_mr_api/public/api/jml_prov?dokumen=SPPL&kewenangan=' . $user['kewenangan'] . '&provinsi=' . $provinsi);
            } elseif ($user['kewenangan'] == 'Kabupaten/Kota') {
                $uklupl_prov = Http::withToken('1|QCyB3h7pys9X0g6vwG2gNoMK5y2dDamjTJSUVXbi')->get('http://amdal.menlhk.go.id/data_mr_api/public/api/jml_prov?dokumen=UKL-UPL&kewenangan=' . $user['kewenangan'] . '&provinsi=' . $provinsi);
                $sppl_prov = Http::withToken('1|QCyB3h7pys9X0g6vwG2gNoMK5y2dDamjTJSUVXbi')->get('http://amdal.menlhk.go.id/data_mr_api/public/api/jml_prov?dokumen=SPPL&kewenangan=' . $user['kewenangan'] . '&provinsi=' . $provinsi);
            }
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

        return response()->json([
            'prov_label' => $prov_label,
            'prov_sppl' => $prov_sppl,
            'prov_uklupl' => $prov_uklupl
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
            $end_date = str_replace('-', '/', request('end_date'));
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
}

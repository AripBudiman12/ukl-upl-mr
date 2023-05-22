<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class JadwalRapatController extends Controller
{
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

    public function table()
    {
        $data = $this->data();
        $result = $data;
        $limit = request('length');
        $start = request('start');
        $search = request('search');

        if (!$search && $limit && $start) {
            $result = $data->skip($start)->take($limit);
        } else if ($search && !$limit && !$start) {
            $result = $data->filter(function ($item) use ($search) {
                return false !== stripos($item['Nama_Perusahaan'], $search) or stripos($item['Kegiatan'], $search) or stripos($item['Tanggal_Rapat'], $search) or stripos($item['Jam_Rapat'], $search) or stripos($item['Keterangan'], $search);
            });
        } else if ($search && $limit && $start) {
            $result = $data->filter(function ($item) use ($search) {
                return false !== stripos($item['Nama_Perusahaan'], $search) or stripos($item['Kegiatan'], $search) or stripos($item['Tanggal_Rapat'], $search) or stripos($item['Jam_Rapat'], $search) or stripos($item['Keterangan'], $search);
            })->skip($start)->take($limit);
        }

        $datas = array();
        foreach ($result as $tag) {
            $datas[] = [
                'nomor' => $tag['Nomor'],
                'nama_perusahaan' => $tag['Nama_Perusahaan'],
                'title' => $tag['Kegiatan'],
                'start' => $tag['Tanggal_Rapat'],
                'end' => $tag['Tanggal_Rapat'],
                'jam_rapat' => $tag['Jam_Rapat'],
                'keterangan' => $tag['Keterangan'],
            ];
        }

        return response()->json([
            'draw' => intval(request('val')),
            'recordsTotal' => intval($data->count()),
            'recordsFiltered' => intval($result->count()),
            'data' => $datas
        ]);
    }

    public function calendar()
    {
        $data = $this->data();
        $result = $data;
        $limit = request('length');
        $start = request('start');
        $search = request('search');

        if (!$search && $limit && $start) {
            $result = $data->skip($start)->take($limit);
        } else if ($search && !$limit && !$start) {
            $result = $data->filter(function ($item) use ($search) {
                // replace stristr with your choice of matching function
                return false !== stripos($item['Nama_Perusahaan'], $search) or stripos($item['Kegiatan'], $search) or stripos($item['Tanggal_Rapat'], $search) or stripos($item['Jam_Rapat'], $search) or stripos($item['Keterangan'], $search);
            });
        } else if ($search && $limit && $start) {
            $result = $data->filter(function ($item) use ($search) {
                // replace stristr with your choice of matching function
                return false !== stripos($item['Nama_Perusahaan'], $search) or stripos($item['Kegiatan'], $search) or stripos($item['Tanggal_Rapat'], $search) or stripos($item['Jam_Rapat'], $search) or stripos($item['Keterangan'], $search);
            })->skip($start)->take($limit);
        }

        $datas = array();
        foreach ($result as $tag) {
            $datas[] = [
                'nomor' => $tag['Nomor'],
                'nama_perusahaan' => $tag['Nama_Perusahaan'],
                'title' => $tag['Kegiatan'],
                // 'date' => date('D M d Y H:i:s', strtotime($tag['date'])) . ' GMT ' . date('O', strtotime($tag['date'])),
                'date' => (int) date('d', strtotime($tag['date'])),
                'month' => (int) date('m', strtotime($tag['date'])),
                'year' => (int) date('Y', strtotime($tag['date'])),
                'start' => date('Y-m-d', strtotime($tag['date'])),
                'end' => date('Y-m-d', strtotime($tag['date'])),
                'jam_rapat' => $tag['Jam_Rapat'],
                'keterangan' => $tag['Keterangan'],
            ];
        }

        return response()->json([
            'draw' => intval(request('val')),
            'recordsTotal' => intval($data->count()),
            'recordsFiltered' => intval($result->count()),
            'data' => $datas
        ]);
    }
}

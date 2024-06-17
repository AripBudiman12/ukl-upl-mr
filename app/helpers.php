<?php

if (!function_exists('getProvince')) {
    function getProvince($province) {
        $provinces = [
            'ACEH' => 'ACEH',
            'BALI' => 'BALI',
            'BANTEN' => 'BANTEN',
            'BENGKULU' => 'BENGKULU',
            'DI YOGYAKARTA' => 'DAERAH ISTIMEWA YOGYAKARTA',
            'DKI JAKARTA' => 'DKI JAKARTA',
            'GORONTALO' => 'GORONTALO',
            'JAMBI' => 'JAMBI',
            'JAWA BARAT' => 'JAWA BARAT',
            'JAWA TENGAH' => 'JAWA TENGAH',
            'JAWA TIMUR' => 'JAWA TIMUR',
            'KALIMANTAN BARAT' => 'KALIMANTAN BARAT',
            'KALIMANTAN SELATAN' => 'Kalimantan Selatan',
            'KALIMANTAN TENGAH' => 'Kalimantan Tengah',
            'KALIMANTAN TIMUR' => 'Kalimantan Timur',
            'KALIMANTAN UTARA' => 'Kalimantan Utara',
            'KEPULAUAN BANGKA BELITUNG' => 'Kepulauan Bangka Belitung',
            'KEPULAUAN RIAU' => 'Kepulauan Riau',
            'LAMPUNG' => 'Lampung',
            'MALUKU' => 'Maluku',
            'MALUKU UTARA' => 'Maluku Utara',
            'NUSA TENGGARA TIMUR' => 'Nusa Tenggara Timur',
            'NUSA TENGGARA BARAT' => 'Nusa Tengggara Barat',
            'PAPUA' => 'Papua',
            'PAPUA BARAT' => 'PAPUA BARAT',
            'Papua Barat Daya' => 'PAPUA BARAT DAYA',
            'PAPUA PEGUNUNGAN' => 'PAPUA PEGUNUNGAN',
            'PAPUA SELATAN' => 'PAPUA SELATAN',
            'PAPUA TENGAH' => 'PAPUA TENGAH',
            'RIAU' => 'RIAU',
            'SULAWESI BARAT' => 'SULAWESI BARAT',
            'SULAWESI SELATAN' => 'SULAWESI SELATAN',
            'SULAWESI TENGAH' => 'SULAWESI TENGAH',
            'SULAWESI TENGGARA' => 'SULAWESI TENGGARA',
            'SULAWESI UTARA' => 'SULAWESI UTARA',
            'SUMATERA BARAT' => 'SUMATERA BARAT',
            'SUMATERA SELATAN' => 'Sumatera Selatan',
            'SUMATERA UTARA' => 'Sumatera Utara'
        ];
        
        return $provinces[$province] ?? null;
    }
}

?>
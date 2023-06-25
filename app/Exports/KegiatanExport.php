<?php

namespace App\Exports;

use App\Models\User;
use App\Models\districts;
use App\Models\provinces;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use PhpOffice\PhpSpreadsheet\Cell\StringValueBinder;

class KegiatanExport extends StringValueBinder implements FromCollection, WithHeadings, ShouldAutoSize
{
    use Exportable;

    public function __construct($length, $start, $date_start, $date_end)
    {
        $this->length = $length;
        $this->start = $start;
        $this->date_start = $date_start;
        $this->date_end = $date_end;
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        $limit = $this->length;
        $start = $this->start;
        $start_date = $this->date_start;
        $end_date = $this->date_end;
        
        $user = $this->user_role();
        $provinsi = "";
        $kabkota = "";
        if ($user['provinsi']) {
            $provinsi = $user['provinsi'];
        }
        if ($user['kabkota']) {
            $kabkota = $user['kabkota'];
        }

        if ($user['kewenangan'] == "Pusat") {
            $api = Http::withToken('1|QCyB3h7pys9X0g6vwG2gNoMK5y2dDamjTJSUVXbi')->get('http://amdal.menlhk.go.id/data_mr_api/public/api/kegiatan?offset=' . $start . '&limit=' . $limit . '&start_date=' . $start_date . '&end_date=' . $end_date);
        } else if ($user['kewenangan'] == "Provinsi") {
            $api = Http::withToken('1|QCyB3h7pys9X0g6vwG2gNoMK5y2dDamjTJSUVXbi')->get('http://amdal.menlhk.go.id/data_mr_api/public/api/kegiatan?provinsi=' . $provinsi . '&offset=' . $start . '&limit=' . $limit . '&start_date=' . $start_date . '&end_date=' . $end_date);
        } else if ($user['kewenangan'] == 'Kabupaten/Kota') {
            $api = Http::withToken('1|QCyB3h7pys9X0g6vwG2gNoMK5y2dDamjTJSUVXbi')->get('http://amdal.menlhk.go.id/data_mr_api/public/api/kegiatan?provinsi=' . $provinsi . '&kabkota=' . $kabkota . '&offset=' . $start . '&limit=' . $limit . '&start_date=' . $start_date . '&end_date=' . $end_date);
        }

        $datas = json_decode($api)->data;
        $data = array();
        for ($i=0; $i < count($datas); $i++) { 
            $data[$i] = [
                'tanggal_record' => $datas[$i]->tanggal_input,
                'nib' => "'" . $datas[$i]->nib,
                'pemrakarsa' => $datas[$i]->pemrakarsa,
                'notelp' => "'" . $datas[$i]->notelp,
                'email' => $datas[$i]->email,
                'judul_kegiatan' => $datas[$i]->judul_kegiatan,
                'lokasi' => $datas[$i]->lokasi,
                'prov' => $datas[$i]->prov,
                'kota' => $datas[$i]->kota,
                'kewenangan' => $datas[$i]->kewenangan,
                'jenisdokumen' => $datas[$i]->jenisdokumen,
            ];
        }

        return collect($data);
    }

    public function headings(): array
    {
        return [
            'Tanggal Record',
            'NIB',
            'Pemrakarsa',
            'Nomor Telepon',
            'Email',
            'Judul Kegiatan',
            'Lokasi',
            'Provinsi',
            'Kab / Kota',
            'Kewenangan',
            'Jenis Dokumen',
        ];
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

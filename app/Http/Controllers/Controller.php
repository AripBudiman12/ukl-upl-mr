<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\User;
use App\Models\districts;
use App\Models\provinces;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

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
}

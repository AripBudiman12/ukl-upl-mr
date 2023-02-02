<?php

## Read value
$draw = $_POST['draw'];
$row = $_POST['start'];
$rowperpage = $_POST['length']; // Rows display per page
$columnIndex = $_POST['order'][0]['column']; // Column index
$columnName = $_POST['columns'][$columnIndex]['data']; // Column name
$columnSortOrder = $_POST['order'][0]['dir']; // asc or desc
$searchValue = $_POST['search']['value']; // Search value

## Search
$searchQuery = "oss_nib!=$1";$searchQueryVal = array();
$searchQueryVal[] = "";
if($searchValue != ''){
   $searchQuery .= " and (pemrakarsa ilike $2 or
        judul_kegiatan ilike $3 or
        kl.lokasi ilike $4) ";

    $searchQueryVal[] = '%'.$searchValue.'%';
    $searchQueryVal[] = '%'.$searchValue.'%';
    $searchQueryVal[] = '%'.$searchValue.'%';

}

## Total number of records without filter
$sql = "select count(*) as allcount from kegiatan left join user_pemrakarsa on kegiatan.id_pemrakarsa = user_pemrakarsa.id_pemrakarsa
			left join kegiatan_lokasi as kl on kegiatan.id_kegiatan = kl.id_kegiatan
			left join idn_adm1 AS i ON kl.id_prov = id_1
			left join idn_adm2 AS i2 ON kl.id_kota = id_2 ";
$result = pg_query($con,$sql);
$records = pg_fetch_assoc($result);
$totalRecords = $records['allcount'];

## Total number of record with filter
$sql = "select count(*) as allcount from kegiatan left join user_pemrakarsa on kegiatan.id_pemrakarsa = user_pemrakarsa.id_pemrakarsa
			left join kegiatan_lokasi as kl on kegiatan.id_kegiatan = kl.id_kegiatan
			left join idn_adm1 AS i ON kl.id_prov = id_1
			left join idn_adm2 AS i2 ON kl.id_kota = id_2 where ".$searchQuery;
$result = pg_query_params($con,$sql,$searchQueryVal);
$records = pg_fetch_assoc($result);
$totalRecordwithFilter = $records['allcount'];

## Fetch records
$sql = "select kegiatan.sid, oss_nib as nib, pemrakarsa, judul_kegiatan,skala,kewenangan,
			to_char(to_timestamp(tanggal_input,'dd/MM/YYYY HH24:MI:ss'),'YYYY/MM/DD HH24:MI:ss') AS tanggal_record,tanggal_input,
			jenisdokumen,id_proyek,jenis_risiko,kbli,file,pkplh_doc, kl.lokasi, name_1 as prov, name_2 as kota
			from kegiatan left join user_pemrakarsa on kegiatan.id_pemrakarsa = user_pemrakarsa.id_pemrakarsa
			left join kegiatan_lokasi as kl on kegiatan.id_kegiatan = kl.id_kegiatan
			left join idn_adm1 AS i ON kl.id_prov = id_1
			left join idn_adm2 AS i2 ON kl.id_kota = id_2
			where ".$searchQuery." order by ".$columnName." ".$columnSortOrder." limit $rowperpage OFFSET $row";

$empRecords = pg_query_params($con,$sql,$searchQueryVal);
$data = array();


$i=0;
while ($row = pg_fetch_assoc($empRecords)) {
	$link = '';
	if ($row['file'] !=""){
			$link = '<a href="http://amdal.menlhk.go.id/amdalnet/'.$row['file'].'" target="_blank"><span class="fa fa-download"></span> Download</a>';
	}
   $i++;
   $data[] = array(
	  "nib"=>$row['nib'],
      "pemrakarsa"=>$row['pemrakarsa'],
      "judul_kegiatan"=>$row['judul_kegiatan'],
      "lokasi"=>$row['lokasi'],
	  "prov"=>$row['prov'],
	  "kota"=>$row['kota'],
      "kewenangan"=>$row['kewenangan'],
	  "tanggal_record"=>$row['tanggal_record'],
	  "jenisdokumen"=>$row['jenisdokumen'],
	  "link"=>$link
   );

}

## Response
$response = array(
  "draw" => intval($draw),
  "iTotalRecords" => $totalRecords,
  "iTotalDisplayRecords" => $totalRecordwithFilter,
  "aaData" => $data
);

echo json_encode($response);

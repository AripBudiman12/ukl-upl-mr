@extends('layouts.master')

@section('section')
    <div class="container-fluid mt-3 pt-3" style="background-color: #628462;">
        <div class="row-12 mb-3">
            {{-- <a href="https://amdalnet-dev.menlhk.go.id/#/dashboard"><button class="btn btn-light float-left">Kembali</button></a> --}}
            {{-- <a class="nav-link" data-widget="pushmenu" href="#" role="button"><button class="btn btn-light float-left">Kembali</button></a> --}}
            <h3 class="text-center"><b style="color:white;">Daftar Rekap Dokumen Resiko Menengah Rendah dan Rendah</b></h3>
        </div>

        {{-- JUMLAH DATA KEDUA JENIS --}}
        <div class="row">
            <div class="col-12 col-sm-6 col-md-6">
                <div class="info-box">
                    <span class="info-box-icon bg-info elevation-1"><i class="fas fa-book"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Jumlah Data UKL-UPL</span>
                        <div id="loading_total_uklupl">
                            <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Proses...
                        </div>
                        <span class="info-box-number" id="total_uklupl" style="display: none;">
                        </span>
                    </div>
                </div>
            </div>

            <div class="col-12 col-sm-6 col-md-6">
                <div class="info-box mb-3">
                    <span class="info-box-icon bg-danger elevation-1"><i class="fas fa-book"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Jumlah Data Jenis Resiko Rendah</span>
                        <div id="loading_total_sppl">
                            <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Proses...
                        </div>
                        <span class="info-box-number" id="total_sppl" style="display: none;">
                        </span>
                    </div>
                </div>
            </div>
        </div>

        {{-- PEMFILTERAN --}}
        <div class="row">
            <div class="col-sm-12">
                <div class="info-box align-items-center">
                    <form action="{{ route('chart-index') }}">
                        <table>
                            <tbody>
                                <tr>
                                    @if ($kewenangan == 'Pusat')
                                        <td class="align-middle">
                                            <label for="start_date" class="mx-2">Kewenangan:</label>
                                        </td>
                                        <td>
                                            <select class="form-control" name="filterKewenangan" id="filterKewenangan">
                                                <option value="all" {{ $filterKewenangan == 'all' ? 'selected' : '' }}>Semua</option>
                                                <option value="Pusat" {{ $filterKewenangan == 'Pusat' ? 'selected' : '' }}>Pusat</option>
                                                <option value="Provinsi" {{ $filterKewenangan == 'Provinsi' ? 'selected' : '' }}>Provinsi</option>
                                                <option value="Kabupaten/Kota" {{ $filterKewenangan == 'Kabupaten/Kota' ? 'selected' : '' }}>Kabupaten/Kota</option>
                                            </select>
                                        </td>
                                    @endif
                                    {{-- <td class="align-middle">
                                        <label for="start_date" class="mx-2">Perbulan:</label>
                                    </td>
                                    <td>
                                        <select class="form-control" name="perbulan" id="perbulan">
                                            <option value="0">Pilih</option>
                                            <option value="0">Pertanggal</option>
                                            <option value="1">Perbulan</option>
                                        </select>
                                    </td> --}}
                                    <td class="align-middle">
                                        <label for="start_date" class="mx-2">Tanggal Awal:</label>
                                    </td>
                                    <?php
                                        if ($start_date != null) {
                                            $date_start = date('Y-m-d', strtotime($start_date));
                                            $date_end = date('Y-m-d', strtotime($end_date));
                                        } else {
                                            $date_start = null;
                                            $date_end = null;
                                        }
                                    ?>
                                    <td>
                                        <input type="date" class="form-control" name="start_date" id="start_date"
                                            value="{{ $date_start }}">
                                    </td>
                                    <td class="align-middle">
                                        <label for="end_date" class="mx-2">Tanggal Akhir:</label>
                                    </td>
                                    <td>
                                        <input type="date" class="form-control" name="end_date" id="end_date"
                                            value="{{ $date_end }}">
                                    </td>
                                    <td>
                                        <div>
                                            <button type="submit" class="btn btn-primary ml-2" onload="hide_loading();"><i class="fa fa-filter"></i></button>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </form>
                </div>
            </div>
        </div>

        {{-- Grafik UKL-UPL Menengah Rendah --}}
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title"><b>Grafik Resiko Menengah Rendah</b>
                        @if ($start_date != null)
                            ({{ $dts }} s/d {{ $dte }})
                        @endif
                        </h5>
                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                <i class="fas fa-minus"></i>
                            </button>
                        </div>
                    </div>

                    {{-- Grafik Kegiatan --}}
                    <div class="card-body">
                        <div id="loading-spinner">
                            <span class="loader"></span>
                        </div>
                        {{-- <span class="loader-text">loading</span> --}}
                        <div class="row" id="canvas_kegiatan" style="display: none;">
                            <div class="col-md-12">
                                <div class="chart">
                                    <canvas id="Kegiatan" height="80" style="height: 80px;"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            {{-- JUMLAH KEDUA JENIS RESIKO --}}
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title"><b>Jumlah Data Resiko Menengah Rendah dan Rendah</b>
                            @if ($start_date != null)
                                ({{ $dts }} s/d {{ $dte }})
                            @endif
                        </h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                <i class="fas fa-minus"></i>
                            </button>
                        </div>
                    </div>

                    {{-- Grafik Jumlah Data UKL-UPL MR dan SPPL --}}
                    <div class="card-body">
                        <div id="loading_total_both">
                            <span class="loader"></span>
                        </div>
                        <div class="row" id="canvas_total_both" style="display: none;">
                            <div class="col-md-8">
                                <div class="chart-responsive">
                                    <canvas id="total_both" height="150"></canvas>
                                </div>

                            </div>

                            <div class="col-md-4">
                                <ul class="chart-legend clearfix">
                                    <li><i class="far fa-circle text-success"></i> Resiko Menengah Rendah</li>
                                    <li><i class="far fa-circle text-danger"></i> Resiko Rendah</li>
                                </ul>
                            </div>

                            <div class="row">
                                <div class="col-14 col-sm-7 col-md-7" style="padding-left: 20px;">
                                    <div class="info-box">
                                        <span class="info-box-icon bg-success elevation-1"><i class="fas fa-book"></i></span>
                                        <div class="info-box-content">
                                            <span class="info-box-text">Jumlah Jenis Resiko Menengah Rendah</span>
                                            <span class="info-box-number" id="mr_total">
                                                {{-- {{ number_format($tot_uklupl, 0, ',', '.') }} --}}
                                            </span>
                                        </div>
                                    </div>
                                </div>
        
                                <div class="col-10 col-sm-5 col-md-5" style="padding-right: 20px;">
                                    <div class="info-box mb-3">
                                        <span class="info-box-icon bg-danger elevation-1"><i class="fas fa-book"></i></span>
                                        <div class="info-box-content">
                                            <span class="info-box-text">Jumlah Jenis Resiko Rendah</span>
                                            <span class="info-box-number" id="r_total">
                                                {{-- {{ number_format($tot_sppl, 0, ',', '.') }} --}}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- JUMLAH DATA MR PER KEWENANGAN --}}
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title"><b>Jumlah Resiko Menengah Rendah per Kewenangan</b>
                        @if ($start_date != null)
                            ({{ $dts }} s/d {{ $dte }})
                        @endif
                        </h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                <i class="fas fa-minus"></i>
                            </button>
                        </div>
                    </div>

                    <div class="card-body">
                        <div id="loading_auth_mr">
                            <span class="loader"></span>
                        </div>
                        <div class="row" id="canvas_auth_mr" style="display: none;">
                            <div class="col-md-8">
                                <div class="chart-responsive">
                                    <canvas id="auth_mr" height="150"></canvas>
                                </div>

                            </div>

                            <div class="col-md-4">
                                <ul class="chart-legend clearfix">
                                    <li><i class="far fa-circle text-danger"></i> Pusat</li>
                                    <li><i class="far fa-circle text-success"></i> Provinsi</li>
                                    <li><i class="far fa-circle text-warning"></i> Kab/Kota</li>
                                </ul>
                            </div>

                            <div class="row">
                                <div class="col-8 col-sm-4 col-md-4" style="padding-left: 20px;">
                                    <div class="info-box">
                                        <span class="info-box-icon bg-danger elevation-1"><i class="fas fa-book"></i></span>
                                        <div class="info-box-content">
                                            <span class="info-box-text">Pusat</span>
                                            <span class="info-box-number" id="pusat_auth_mr">
                                                {{-- <span>{{ number_format($uklupl_data[0], 0, ',', '.') }}</span> --}}
                                            </span>
                                        </div>
                                    </div>
                                </div>
        
                                <div class="col-8 col-sm-4 col-md-4">
                                    <div class="info-box mb-3">
                                        <span class="info-box-icon bg-success elevation-1"><i class="fas fa-book"></i></span>
                                        <div class="info-box-content">
                                            <span class="info-box-text">Provinsi</span>
                                            <span class="info-box-number" id="prov_auth_mr">
                                                {{-- <span>{{ number_format($uklupl_data[1], 0, ',', '.') }}</span> --}}
                                            </span>
                                        </div>
                                    </div>
                                </div>
        
                                <div class="col-8 col-sm-4 col-md-4" style="padding-right: 20px;">
                                    <div class="info-box mb-3">
                                        <span class="info-box-icon bg-warning elevation-1"><i class="fas fa-book"></i></span>
                                        <div class="info-box-content">
                                            <span class="info-box-text">Kab/Kota</span>
                                            <span class="info-box-number" id="kabkot_auth_mr">
                                                {{-- <span>{{ number_format($uklupl_data[2], 0, ',', '.') }}</span> --}}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- JUMLAH DATA R PER KEWENANGAN --}}
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title"><b>Jumlah Resiko Rendah per Kewenangan</b>
                        @if ($start_date != null)
                            ({{ $dts }} s/d {{ $dte }})
                        @endif
                        </h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                <i class="fas fa-minus"></i>
                            </button>
                        </div>
                    </div>

                    <div class="card-body">
                        <div id="loading_auth_r">
                            <span class="loader"></span>
                        </div>
                        <div class="row" id="canvas_auth_r" style="display: none;">
                            <div class="col-md-8">
                                <div class="chart-responsive">
                                    <canvas id="auth_r" height="150"></canvas>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <ul class="chart-legend clearfix">
                                    <li><i class="far fa-circle text-danger"></i> Pusat</li>
                                    <li><i class="far fa-circle text-success"></i> Provinsi</li>
                                    <li><i class="far fa-circle text-warning"></i> Kab/Kota</li>
                                </ul>
                            </div>

                            <div class="row">
                                <div class="col-8 col-sm-4 col-md-4" style="padding-left: 20px;">
                                    <div class="info-box">
                                        <span class="info-box-icon bg-danger elevation-1"><i class="fas fa-book"></i></span>
                                        <div class="info-box-content">
                                            <span class="info-box-text">Pusat</span>
                                            <span class="info-box-number" id="pusat_auth_r">
                                                {{-- <span>{{ number_format($sppl_data[0], 0, ',', '.') }}</span> --}}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-8 col-sm-4 col-md-4">
                                    <div class="info-box mb-3">
                                        <span class="info-box-icon bg-success elevation-1"><i class="fas fa-book"></i></span>
                                        <div class="info-box-content">
                                            <span class="info-box-text">Provinsi</span>
                                            <span class="info-box-number" id="prov_auth_r">
                                                {{-- <span>{{ number_format($sppl_data[1], 0, ',', '.') }}</span> --}}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-8 col-sm-4 col-md-4" style="padding-right: 20px;">
                                    <div class="info-box mb-3">
                                        <span class="info-box-icon bg-warning elevation-1"><i class="fas fa-book"></i></span>
                                        <div class="info-box-content">
                                            <span class="info-box-text">Kab/Kota</span>
                                            <span class="info-box-number" id="kabkot_auth_r">
                                                {{-- <span>{{ number_format($sppl_data[2], 0, ',', '.') }}</span> --}}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- JUMLAH DATA BERDASARKAN CLUSTER KBLI --}}
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title"><b>Jumlah UKL-UPL Menengah Rendah Berdasarkan Cluster KBLI</b>
                        @if ($start_date != null)
                            ({{ $dts }} s/d {{ $dte }})
                        @endif
                        </h5>
                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                <i class="fas fa-minus"></i>
                            </button>
                        </div>
                    </div>

                    <div class="card-body">
                        <div id="loading_cluster">
                            <span class="loader"></span>
                        </div>
                        <div class="row" id="canvas_cluster" style="display: none;">
                            <div class="col-md-6">
                                <div class="chart-responsive">
                                    <canvas id="cluster" height="150"></canvas>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <ul class="chart-legend clearfix">
                                    <li><i class="far fa-circle" style="color: rgba(255, 99, 132)"></i> Kegiatan Perdagangan, Jasa, Pariwisata dg Sarpras</li>
                                    <li><i class="far fa-circle" style="color: rgba(255, 159, 64)"></i> Industri Berbasis Produksi dg Pembangunan Sarpras</li>
                                    <li><i class="far fa-circle" style="color: rgba(255, 205, 86)"></i> Industri Berbasis Lahan dg Pembangunan Sarpras</li>
                                    <li><i class="far fa-circle" style="color: rgba(75, 192, 192)"></i> SPBU Mini dan Sejenisnya</li>
                                    <li><i class="far fa-circle" style="color: rgba(255, 190, 12)"></i> Budidaya Perikanan Air Payau di Darat</li>
                                    <li><i class="far fa-circle" style="color: rgba(234, 29, 157)"></i> Budi Daya Perikanan Air Tawar di Darat</li>
                                    <li><i class="far fa-circle" style="color: rgba(153, 102, 255)"></i> Budidaya Perikanan Air Laut di Perairan</li>
                                    <li><i class="far fa-circle" style="color: rgba(201, 203, 207)"></i> Perbenihan dan Budi Daya Pertanian</li>
                                    <li><i class="far fa-circle" style="color: rgba(205, 156, 100)"></i> Pembibitan dan Budi Daya Ternak</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- JUMLAH PER PROVINSI --}}
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title"><b>Jumlah Resiko Menengah Rendah dan Rendah di setiap Provinsi</b>
                        @if ($start_date != null)
                            ({{ $dts }} s/d {{ $dte }})
                        @endif
                        </h5>
                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                <i class="fas fa-minus"></i>
                            </button>
                        </div>
                    </div>

                    <div class="card-body">
                        <div id="loading_byprov">
                            <span class="loader"></span>
                        </div>
                        <div class="row" id="canvas_byprov">
                            <div class="col-md-12">
                                <div class="chart">
                                    <canvas id="byprov" height="80" style="height: 80px;"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- DATATABLE MR --}}
        <div class="card" id="mr_card" style="background-color: #133715; color: white; display: none;">
            <div class="card-body">
                <div class="form-group mt-2" style="display: flex; justify-content: center; align-items: center;">
                    <h2><b>Daftar Resiko Menengah Rendah</b></h2>
                </div>
                @if (session('message'))
                    <div class="alert alert-danger" role="alert">
                        {{ session('message') }}
                    </div>
                @endif
                <div class="d-flex justify-content-between">
                    {{-- <button type="button" class="btn btn-sm btn-success" data-toggle="modal" data-target="#modalExport">
                        <i class="fas fa-file-excel">&nbsp;&nbsp;&nbsp;Export</i>
                    </button> --}}
                    <h4>
                        Tanggal : {{ $dts }} s/d {{ $dte }}
                    </h4>
                </div>
                <table id='dataTableMr' class="table hover table-bordered table-striped" style="table-layout: fixed; font-size: 12px;">
                    <thead>
                        <tr>
                            <th>Tanggal Records</th>
                            <th>NIB</th>
                            <th>KBLI</th>
                            <th>Bidang</th>
                            <th>ID Izin</th>
                            <th>Judul</th>
                            <th>Alamat</th>
                            <th>Provinsi</th>
                            <th>Kabupaten/Kota</th>
                            <th>Kewenangan</th>
                            <th>SPPL</th>
                            <th>PKPLH</th>
                            <th>Lampiran</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>

        {{-- DATATABLE R --}}
        <div class="card" id="r_card" style="background-color: #133715; color: white; display: none;">
            <div class="card-body">
                <div class="form-group mt-2" style="display: flex; justify-content: center; align-items: center;">
                    <h2><b>Daftar Resiko Rendah</b></h2>
                </div>
                @if (session('message'))
                    <div class="alert alert-danger" role="alert">
                        {{ session('message') }}
                    </div>
                @endif
                <div class="d-flex justify-content-between">
                    {{-- <button type="button" class="btn btn-sm btn-success" data-toggle="modal" data-target="#modalExport">
                        <i class="fas fa-file-excel">&nbsp;&nbsp;&nbsp;Export</i>
                    </button> --}}
                    <h4>
                        Tanggal : {{ $dts }} s/d {{ $dte }}
                    </h4>
                </div>
                <table id='dataTableR' class="table hover table-bordered table-striped" style="table-layout: fixed; font-size: 12px;">
                    <thead>
                        <tr>
                            <th>Tanggal Records</th>
                            <th>NIB</th>
                            <th>KBLI</th>
                            <th>Bidang</th>
                            <th>ID Izin</th>
                            <th>Judul</th>
                            <th>Alamat</th>
                            <th>Provinsi</th>
                            <th>Kabupaten/Kota</th>
                            <th>Kewenangan</th>
                            <th>SPPL</th>
                            <th>Lampiran</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- MODAL DOWNLOAD FILE --}}
    <div class="modal fade" id="dataModal" tabindex="-1" role="dialog" aria-labelledby="dataModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="dataModalLabel">Unduh</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div id="loading_file">
                        <span class="loader"></span>
                    </div>
                    <div id="modalContent" style="display: none">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            var kewenangan = <?php echo json_encode($filterKewenangan); ?>;
            var start_date = <?php echo json_encode($start_date); ?>;
            var end_date = <?php echo json_encode($end_date); ?>;
            var province = <?php echo json_encode($province); ?>;
            var district = '';

            // TOTAL DATA
            $.ajax({
                url: "{{ route('api.sppl_total', ['kewenangan' => $filterKewenangan, 'province' => $province, 'district' => $district]) }}",
                method: 'GET',
                success: function(data) {
                    $('#loading_total_sppl').hide();
                    $('#total_sppl').show();
                    $('#total_sppl').text(data.total_sppl);
                }
            })

            $.ajax({
                url: "{{ route('api.uklupl_total', ['kewenangan' => $filterKewenangan, 'province' => $province, 'district' => $district]) }}",
                method: 'GET',
                success: function(data) {
                    $('#loading_total_uklupl').hide();
                    $('#total_uklupl').show();
                    $('#total_uklupl').text(data.total_uklupl);
                }
            })

            // STATISIK
            $.ajax({
                // url: `/statistic?kewenangan=${kewenangan}&start_date=${start_date}&end_date=${end_date}&province=${province}&district=${district}`,
                url: "{{ route('api.statistic', ['start_date' => $start_date, 'end_date' => $date_end, 'kewenangan' => $filterKewenangan, 'province' => $province, 'district' => $district]) }}",
                method: 'GET',
                success: function(data) {
                    $('#loading-spinner').hide();
                    $('#canvas_kegiatan').show();

                    var ctx = document.getElementById('Kegiatan').getContext('2d');
                    var KegiatanChart = new Chart(ctx, {
                        type: 'bar',
                        data: {
                            'labels': data.labels,
                            'datasets': [
                                {
                                    'label': 'Statistik',
                                    'backgroundColor': '#4BAF48',
                                    'borderColor': 'rgba(75,192,192,1)',
                                    'data': data.data,
                                }
                            ]
                        },
                    });
                },
                error: function() {
                    $('#loading-spinner').text('Gagal memuat data');
                }
            })

            // TOTAL BY DATE
            $.ajax({
                // url: `/totalByDate?kewenangan=${kewenangan}&start_date=${start_date}&end_date=${end_date}&province=${province}&district=${district}`,
                url: "{{ route('api.totalByDate', ['start_date' => $start_date, 'end_date' => $date_end, 'kewenangan' => $filterKewenangan, 'province' => $province, 'district' => $district]) }}",
                method: 'GET',
                success: function(data) {
                    $('#loading_total_both').hide();
                    $('#canvas_total_both').show();
                    $('#mr_total').text(data.total_mr);
                    $('#r_total').text(data.total_r);

                    var ctx = document.getElementById('total_both').getContext('2d');
                    var totalDate = new Chart(ctx, {
                        type: 'doughnut',
                        data: {
                            'labels': ['Menengah Rendah', 'Rendah'],
                            'datasets': [
                                {
                                    'data': [data.total_r, data.total_mr],
                                    'backgroundColor': ['#f56954', '#00a65a'],
                                }
                            ]
                        },
                        options: {
                            legend: {
                                display: false
                            }
                        }
                    });
                },
                error: function() {
                    $('#loading_total_both').text('Gagal memuat data');
                }
            })

            // TOTAL BY AUTHORITY
            $.ajax({
                // url: `/totalByAuthority?kewenangan=${kewenangan}&start_date=${start_date}&end_date=${end_date}&province=${province}&district=${district}`,
                url: "{{ route('api.totalByAuthority', ['start_date' => $start_date, 'end_date' => $date_end, 'kewenangan' => $filterKewenangan, 'province' => $province, 'district' => $district]) }}",
                method: 'GET',
                success: function(data) {
                    $('#loading_auth_mr').hide();
                    $('#loading_auth_r').hide();
                    $('#canvas_auth_mr').show();
                    $('#canvas_auth_r').show();
                    $('#pusat_auth_mr').text(data.mr_pusat);
                    $('#prov_auth_mr').text(data.mr_prov);
                    $('#kabkot_auth_mr').text(data.mr_kabkot);
                    $('#pusat_auth_r').text(data.r_pusat);
                    $('#prov_auth_r').text(data.r_prov);
                    $('#kabkot_auth_r').text(data.r_kabkot);

                    var ctx = document.getElementById('auth_mr').getContext('2d');
                    var totalAuthMr = new Chart(ctx, {
                        type: 'doughnut',
                        data: {
                            'labels': ['Pusat', 'Provinsi', 'Kabupaten/Kota'],
                            'datasets': [
                                {
                                    'data': [data.mr_pusat, data.mr_prov, data.mr_kabkot],
                                    'backgroundColor': ['#f56954', '#00a65a', '#f39c12'],
                                }
                            ]
                        },
                        options: {
                            legend: {
                                display: false
                            }
                        }
                    });
                    
                    var ctx = document.getElementById('auth_r').getContext('2d');
                    var totalAuthR = new Chart(ctx, {
                        type: 'doughnut',
                        data: {
                            'labels': ['Pusat', 'Provinsi', 'Kabupaten/Kota'],
                            'datasets': [
                                {
                                    'data': [data.r_pusat, data.r_prov, data.r_kabkot],
                                    'backgroundColor': ['#f56954', '#00a65a', '#f39c12'],
                                }
                            ]
                        },
                        options: {
                            legend: {
                                display: false
                            }
                        }
                    });
                },
                error: function() {
                    $('#loading_auth_mr').text('Gagal memuat data');
                    $('#loading_auth_r').text('Gagal memuat data');
                }
            })

            // DATA BY CLUSTER KBLI
            $.ajax({
                // url: `/cluster?kewenangan=${kewenangan}&start_date=${start_date}&end_date=${end_date}&province=${province}&district=${district}`,
                url: "{{ route('api.cluster', ['start_date' => $start_date, 'end_date' => $date_end, 'kewenangan' => $filterKewenangan, 'province' => $province, 'district' => $district]) }}",
                method: 'GET',
                success: function(data) {
                    $('#loading_cluster').hide();
                    $('#canvas_cluster').show();

                    var ctx = document.getElementById('cluster').getContext('2d');
                    var cluster = new Chart(ctx, {
                        type: 'doughnut',
                        data: {
                            'labels': data.cluster_label,
                            'datasets': [
                                {
                                    'label': 'Total',
                                    'data': data.cluster_data,
                                    'backgroundColor': ['rgba(255, 99, 132)',
                                        'rgba(255, 159, 64)',
                                        'rgba(255, 205, 86)',
                                        'rgba(75, 192, 192)',
                                        'rgba(255, 190, 12)',
                                        'rgba(234, 29, 157)',
                                        'rgba(153, 102, 255)',
                                        'rgba(201, 203, 207)',
                                        'rgba(205, 156, 100)',
                                    ],
                                }
                            ]
                        },
                        options: {
                            legend: {
                                display: false
                            }
                        }
                    });
                },
                error: function() {
                    $('#loading_cluster').text('Gagal memuat data');
                }
            })

            // DATA PER PROVINSI
            $.ajax({
                // url: `/ByProvince?kewenangan=${kewenangan}&start_date=${start_date}&end_date=${end_date}&province=${province}&district=${district}`,
                url: "{{ route('api.ByProvince', ['start_date' => $start_date, 'end_date' => $date_end, 'kewenangan' => $filterKewenangan, 'province' => $province, 'district' => $district]) }}",
                method: 'GET',
                success: function(data) {
                    $('#loading_byprov').hide();
                    $('#canvas_byprov').show();

                    var ctx = document.getElementById('byprov').getContext('2d');
                    var options = {
                        maintainAspectRatio: true,
                        responsive: true,
                        legend: {
                            display: true
                        },
                        scales: {
                            x: {
                                stacked: true
                            },
                            y: {
                                stacked: true
                            }
                        }
                    }
                    var totalBothChart = new Chart(ctx, {
                        type: 'bar',
                        data: {
                            'labels': data.labels,
                            'datasets': [
                                {
                                    'label': 'Menengah Rendah',
                                    'data': data.mr_data,
                                    'fill': false,
                                    'backgroundColor': '#f56954',
                                    'tension': 0.1
                                },
                                {
                                    'label': 'Rendah',
                                    'data': data.r_data,
                                    'fill': false,
                                    'backgroundColor': '#7FFF00',
                                    'tension': 0.1
                                },
                            ]
                        },
                        options: options
                    });
                    $('#mr_card').show();
                    datatable_mr();
                },
                error: function() {
                    $('#loading_byprov').text('Gagal memuat data');
                    $('#mr_card').show();
                    datatable_mr();
                }
            })

            // DATATABLE MR
            function datatable_mr () {
                $('#dataTableMr').DataTable({
                    'responsive': false,
                    'lengthChange': true,
                    'autoWidth': true,
                    'pageLength': 10,
                    'processing': true,
                    'serverSide': true,
                    'serverMethod': 'post',
                    "order": [
                        [0, "desc"]
                    ],
                    'deferRender': true,
                    'scrollX': true,
                    'scrollY': false,
                    scroller: {
                        loadingIndicator: true
                    },
                    ajax: {
                        type: "GET",
                        url: "{{ route('datatable_mr', ['start_date' => $start_date, 'end_date' => $date_end, 'kewenangan' => $filterKewenangan, 'province' => $province, 'district' => $district]) }}",
                    },
                    'columns': [
                        { data: 'last_kirim', name: 'last_kirim' },
                        { data: 'nib', name: 'nib' },
                        { data: 'kbli', name: 'kbli' },
                        { data: 'bidang', name: 'bidang' },
                        { data: 'id_izin', name: 'id_izin' },
                        { data: 'judul', name: 'judul' },
                        { data: 'alamat', name: 'alamat' },
                        { data: 'province', name: 'province' },
                        { data: 'district', name: 'district' },
                        { data: 'kewenangan', name: 'kewenangan' },
                        {
                            data: 'id_izin',
                            name: 'sppl',
                            render: function (data, type, row) {
                                return `<button class="btn btn-sm btn-success btn-sppl" data-id_izin="${data}">Unduh</button>`;
                            }
                        },
                        {
                            data: 'id_izin',
                            name: 'pkplh_otomatis',
                            render: function (data, type, row) {
                                return `<button class="btn btn-sm btn-success btn-pkplh" data-id_izin="${data}">Unduh</button>`;
                            }
                        },
                        {
                            data: 'id_izin',
                            name: 'lampiran',
                            render: function (data, type, row) {
                                return `<button class="btn btn-sm btn-success btn-lampiran" data-id_izin="${data}">Unduh</button>`;
                            }
                        },
                    ],
                });
                $('#r_card').show();
                datatable_r();
            }

            // DATATABLE R
            function datatable_r () {
                $('#dataTableR').DataTable({
                    'responsive': false,
                    'lengthChange': true,
                    'autoWidth': true,
                    'pageLength': 10,
                    'processing': true,
                    'serverSide': true,
                    'serverMethod': 'post',
                    "order": [
                        [0, "desc"]
                    ],
                    'deferRender': true,
                    'scrollX': true,
                    'scrollY': false,
                    scroller: {
                        loadingIndicator: true
                    },
                    ajax: {
                        type: "GET",
                        url: "{{ route('datatable_r', ['start_date' => $start_date, 'end_date' => $date_end, 'kewenangan' => $filterKewenangan, 'province' => $province, 'district' => $district]) }}",
                    },
                    'columns': [
                        { data: 'last_kirim', name: 'last_kirim' },
                        { data: 'nib', name: 'nib' },
                        { data: 'kbli', name: 'kbli' },
                        { data: 'bidang', name: 'bidang' },
                        { data: 'id_izin', name: 'id_izin' },
                        { data: 'judul', name: 'judul' },
                        { data: 'alamat', name: 'alamat' },
                        { data: 'province', name: 'province' },
                        { data: 'district', name: 'district' },
                        { data: 'kewenangan', name: 'kewenangan' },
                        {
                            data: 'id_izin',
                            name: 'sppl',
                            render: function (data, type, row) {
                                return `<button class="btn btn-sm btn-success btn-sppl" data-id_izin="${data}">Unduh</button>`;
                            }
                        },
                        {
                            data: 'id_izin',
                            name: 'lampiran',
                            render: function (data, type, row) {
                                return `<button class="btn btn-sm btn-success btn-lampiran" data-id_izin="${data}">Unduh</button>`;
                            }
                        },
                    ],
                });
            }

            $(document).on('click', '.btn-lampiran', function() {
                var id_izin = $(this).data('id_izin');
                var actionType = $(this).text().trim();
                $('#dataModalLabel').text(actionType + ' SPPL');
                $('#modalContent').html('');
                $('#dataModal').modal('show');

                $.ajax({
                    url: `{{ route('getLampiranFile') }}`, // Adjust the URL to your endpoint
                    type: 'GET',
                    data: { id_izin: id_izin },
                    success: function(response) {
                        $('#loading_file').hide();
                        $('#modalContent').show();

                        if (response.status == true) {
                            $('#modalContent').html(`<div><span class="mb-2">Klik tombol di bawah untuk mengunduh</span><a target="_blank" class="btn btn-success" href="${response.link}">Unduh</a></div>`);
                        } else {
                            $('#modalContent').html(`<div><span class="mb-2">File tidak tersedia</span></div>`);
                        }
                    },
                    error: function(xhr, status, error) {
                        $('#loading_file').hide();
                        $('#modalContent').show();
                        $('#modalContent').html(`<div><span class="mb-2">File tidak tersedia</span></div>`);
                    }
                });
            });

            $(document).on('click', '.btn-sppl', function() {
                var id_izin = $(this).data('id_izin');
                var actionType = $(this).text().trim();
                $('#dataModalLabel').text(actionType + ' SPPL');
                $('#modalContent').html('');
                $('#dataModal').modal('show');

                $.ajax({
                    url: `{{ route('getSpplFile') }}`, // Adjust the URL to your endpoint
                    type: 'GET',
                    data: { id_izin: id_izin },
                    success: function(response) {
                        $('#loading_file').hide();
                        $('#modalContent').show();

                        if (response.status == true) {
                            $('#modalContent').html(`<div><span class="mb-2">Klik tombol di bawah untuk mengunduh</span><a target="_blank" class="btn btn-success" href="${response.link}">Unduh</a></div>`);
                        } else {
                            $('#modalContent').html(`<div><span class="mb-2">File tidak tersedia</span></div>`);
                        }
                    },
                    error: function(xhr, status, error) {
                        $('#loading_file').hide();
                        $('#modalContent').show();
                        $('#modalContent').html(`<div><span class="mb-2">File tidak tersedia</span></div>`);
                    }
                });
            });

            $(document).on('click', '.btn-pkplh', function() {
                var id_izin = $(this).data('id_izin');
                var actionType = $(this).text().trim();
                $('#dataModalLabel').text(actionType + ' PKPLH');
                $('#modalContent').html('');
                $('#dataModal').modal('show');

                $.ajax({
                    url: `{{ route('getPkplhFile') }}`, // Adjust the URL to your endpoint
                    type: 'GET',
                    data: { id_izin: id_izin },
                    success: function(response) {
                        $('#loading_file').hide();
                        $('#modalContent').show();

                        if (response.status == true) {
                            $('#modalContent').html(`<div><span class="mb-2">Klik tombol di bawah untuk mengunduh</span><a target="_blank" class="btn btn-success" href="${response.link}">Unduh</a></div>`);
                        } else {
                            $('#modalContent').html(`<div><span class="mb-2">File tidak tersedia</span></div>`);
                        }
                    },
                    error: function(xhr, status, error) {
                        $('#loading_file').hide();
                        $('#modalContent').show();
                        $('#modalContent').html(`<div><span class="mb-2">File tidak tersedia</span></div>`);
                    }
                });
            });
        });
    </script>
@endpush
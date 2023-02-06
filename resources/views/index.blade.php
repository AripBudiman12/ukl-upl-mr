@extends('layouts.master')

@section('section')
    <div class="container-fluid mt-3 pt-3" style="background-color: #628462;">
        <div class="row-12 mb-3">
            <a href="https://amdalnet.menlhk.go.id/"><button class="btn btn-light float-left">Kembali</button></a>
            <h3 class="text-center"><b style="color:white; margin-left: -90px">Daftar Rekap SPPL dan UKL-UPL Menengah
                    Rendah</b></h3>
        </div>

        <div class="row">
            <div class="col-12 col-sm-6 col-md-6">
                <div class="info-box">
                    <span class="info-box-icon bg-info elevation-1"><i class="fas fa-book"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Jumlah UKL-UPL Menengah Rendah</span>
                        <span class="info-box-number">
                            {{ $total_uklupl }}
                        </span>
                    </div>
                </div>
            </div>

            <div class="col-12 col-sm-6 col-md-6">
                <div class="info-box mb-3">
                    <span class="info-box-icon bg-danger elevation-1"><i class="fas fa-book"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Jumlah SPPL</span>
                        <span class="info-box-number">
                            {{ $total_sppl }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        {{-- <div class="clearfix hidden-md-up"></div>
            <div class="col-12 col-sm-6 col-md-3">
                <div class="info-box mb-3">
                    <span class="info-box-icon bg-success elevation-1"><i class="fas fa-shopping-cart"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Sales</span>
                        <span class="info-box-number">760</span>
                    </div>

                </div>

            </div>

            <div class="col-12 col-sm-6 col-md-3">
                <div class="info-box mb-3">
                    <span class="info-box-icon bg-warning elevation-1"><i class="fas fa-users"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">New Members</span>
                        <span class="info-box-number">2,000</span>
                    </div>

                </div>
            </div> --}}

        <div class="row">
            <div class="col-sm-12">
                <div class="info-box">
                    <form action="{{ route('index') }}">
                        <table>
                            <tbody>
                                <tr>
                                    <td>
                                        <select class="form-control" name="perbulan" id="perbulan">
                                            <option value="0">Pilih</option>
                                            <option value="0">Pertanggal</option>
                                            <option value="1">Perbulan</option>
                                        </select>
                                    </td>
                                    <td class="align-middle">
                                        <label for="start_date" class="mx-2">Tanggal Awal:</label>
                                    </td>
                                    <td>
                                        <input type="date" class="form-control" name="start_date" id="start_date"
                                            value="{{ $tgl_awal }}">
                                    </td>
                                    <td class="align-middle">
                                        <label for="end_date" class="mx-2">Tanggal Akhir:</label>
                                    </td>
                                    <td>
                                        <input type="date" class="form-control" name="end_date" id="end_date"
                                            value="{{ $tgl_akhir }}">
                                    </td>
                                    <td>
                                        <button type="submit" class="btn btn-primary ml-2"><i
                                                class="fa fa-filter"></i></button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </form>
                </div>
            </div>
        </div>


        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">Statistik Penginputan Kegiatan</h5>
                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                <i class="fas fa-minus"></i>
                            </button>
                        </div>
                    </div>

                    <div class="card-body">
                        <div class="row">
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
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Jumlah UKL-UPL dan SPPL</h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                <i class="fas fa-minus"></i>
                            </button>
                        </div>
                    </div>

                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-8">
                                <div class="chart-responsive">
                                    <canvas id="pieChart" height="150"></canvas>
                                </div>

                            </div>

                            <div class="col-md-4">
                                <ul class="chart-legend clearfix">
                                    <li><i class="far fa-circle text-success"></i> UKLUPL Menengah Rendah</li>
                                    <li><i class="far fa-circle text-danger"></i> SPPL</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Jumlah UKL-UPL Menengah Rendah per Kewenangan</h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                <i class="fas fa-minus"></i>
                            </button>
                        </div>
                    </div>

                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-8">
                                <div class="chart-responsive">
                                    <canvas id="uklupl" height="150"></canvas>
                                </div>

                            </div>

                            <div class="col-md-4">
                                <ul class="chart-legend clearfix">
                                    <li><i class="far fa-circle text-danger"></i> Kab/Kota</li>
                                    <li><i class="far fa-circle text-success"></i> Provinsi</li>
                                    <li><i class="far fa-circle text-warning"></i> Pusat</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Jumlah SPPL per Kewenangan</h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                <i class="fas fa-minus"></i>
                            </button>
                        </div>
                    </div>

                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-8">
                                <div class="chart-responsive">
                                    <canvas id="sppl" height="150"></canvas>
                                </div>

                            </div>
                            <div class="col-md-4">
                                <ul class="chart-legend clearfix">
                                    <li><i class="far fa-circle text-danger"></i> Kab/Kota</li>
                                    <li><i class="far fa-circle text-success"></i> Provinsi</li>
                                    <li><i class="far fa-circle text-warning"></i> Pusat</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- cluster --}}
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">Jumlah UKL-UPL Menengah Rendah Berdasarkan Cluster KBLI</h5>
                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                <i class="fas fa-minus"></i>
                            </button>
                        </div>
                    </div>

                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-8">
                                <div class="chart-responsive">
                                    <canvas id="Cluster" height="150"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">Jumlah UKLUPL & SPPL di setiap Provinsi</h5>
                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                <i class="fas fa-minus"></i>
                            </button>
                        </div>
                    </div>

                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-12">
                                {{-- <p class="text-center">
                                    <strong>Sales: 1 Jan, 2014 - 30 Jul, 2014</strong>
                                </p> --}}
                                <div class="chart">
                                    <canvas id="Statistic" height="80" style="height: 80px;"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card" style="background-color: #133715; color: white;">
            <div class="card-header" style="background-color: #628462;">
                <div class="form-group">
                    {{-- <table border="0">
                        <form action="/">
                            <tr>
                                <td>
                                    <label><strong>Kewenangan :</strong></label>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <select id='kewenangan' class="form-control" style="width: 200px">
                                        <option value="">--Pilih Kewenangan--</option>
                                        <option value="0">Kab / Kot</option>
                                        <option value="1">Provinsi</option>
                                        <option value="2">Pusat</option>
                                    </select>
                                </td>
                                <td >
                                    <button type="submit" class="btn btn-success"><i class="fa fa-filter"></i></button>
                                </td>
                            </tr>
                        </form>
                    </table> --}}
                </div>
            </div>
            <div class="card-body">
                <table id='dataTable' class="table hover table-bordered table-striped" style="table-layout: fixed;">
                    <thead>
                        <tr>
                            <th>Tanggal Record</th>
                            <th>NIB</th>
                            <th>Pemrakarsa</th>
                            <th>Judul Usaha/Kegiatan</th>
                            <th>Lokasi</th>
                            <th>Provinsi</th>
                            <th>Kab/Kota</th>
                            <th>Kewenagan</th>
                            <th>Jenis Dokling</th>
                        </tr>
                    </thead>
                    <tbody>

                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script type="text/javascript">
        $(document).ready(function() {
            fetch_data()

            function fetch_data() {
                $('#dataTable').DataTable({

                    'responsive': true,
                    'lengthChange': true,
                    'autoWidth': false,
                    'processing': true,
                    'serverSide': true,
                    'serverMethod': 'post',
                    "order": [
                        [0, "desc"]
                    ],
                    "deferRender": true,
                    "scrollX": false,

                    ajax: function(data, callback, settings) {
                        var out = [];

                        for (var i = data.start, ien = data.start + data.length; i < ien; i++) {
                            out.push([i + '-1', i + '-2', i + '-3', i + '-4', i + '-5', i + '6', i +
                                '7', i + '8', i + '9', i + '10'
                            ]);
                        }

                        setTimeout(function() {
                            callback({
                                draw: data.draw,
                                data: out,
                                recordsTotal: 5000000,
                                recordsFiltered: 5000000
                            });
                        }, 50);
                    },
                    scrollY: 500,
                    scroller: {
                        loadingIndicator: true
                    },

                    ajax: {
                        url: "{{ route('index.data') }}",
                        type: "GET",
                        data: function(d) {
                            d.kewenangan = $("#kewenangan").val()
                        }
                    },

                    'columns': [{
                            data: 'tanggal_input',
                            name: 'tanggal_input'
                        },
                        {
                            data: 'oss_nib',
                            name: 'oss_nib'
                        },
                        {
                            data: 'pemrakarsa',
                            name: 'pemrakarsa'
                        },
                        {
                            data: 'judul_kegiatan',
                            name: 'judul_kegiatan'
                        },
                        {
                            data: 'lokasi',
                            name: 'lokasi'
                        },
                        {
                            data: 'name_1',
                            name: 'name_1'
                        },
                        {
                            data: 'name_2',
                            name: 'name_2'
                        },
                        {
                            data: 'kewenangan',
                            name: 'kewenangan'
                        },
                        {
                            data: 'jenisdokumen',
                            name: 'jenisdokumen'
                        }
                    ],

                });
            }

            $('#kewenangan').change(function() {
                table.draw();
            });

            function filter() {
                table.ajax.reload(null, false)
            }

            $(function() {
                'use strict'

                /* ChartJS
                 * -------
                 * Here we will create a few charts using ChartJS
                 */

                // First Donut

                let sppl_data = <?php echo json_encode($sppl_data); ?>;
                let uklupl_data = <?php echo json_encode($uklupl_data); ?>;
                let total_uklupl = <?php echo json_encode($total_uklupl); ?>;
                let total_sppl = <?php echo json_encode($total_sppl); ?>;


                var ProvinsiCanvas = $('#pieChart').get(0).getContext('2d')
                var pieData = {
                    labels: [
                        'SPPL',
                        'UKLUPL',
                    ],
                    datasets: [{
                        data: [total_sppl,total_uklupl],
                        backgroundColor: ['#f56954', '#00a65a']
                    }]
                }
                var pieOptions = {
                    legend: {
                        display: false
                    }
                }
                var pieChart = new Chart(ProvinsiCanvas, {
                    type: 'doughnut',
                    data: pieData,
                    options: pieOptions
                });

                // Second Donut

                var ukluplChart = $('#uklupl').get(0).getContext('2d')
                var ukluplData = {
                    labels: [
                        'Kab/Kota',
                        'Provinsi',
                        'Pusat',
                    ],
                    datasets: [{
                        data: uklupl_data,
                        backgroundColor: ['#f56954', '#00a65a', '#f39c12']
                    }]
                }
                var ukluplOptions = {
                    legend: {
                        display: false
                    }
                }
                var uklupl = new Chart(ukluplChart, {
                    type: 'doughnut',
                    data: ukluplData,
                    options: ukluplOptions
                });

                // Three Donut

                var spplChart = $('#sppl').get(0).getContext('2d')
                var spplData = {
                    labels: [
                        'Kab/Kota',
                        'Provinsi',
                        'Pusat',
                    ],
                    datasets: [{
                        data: sppl_data,
                        backgroundColor: ['#f56954', '#00a65a', '#f39c12']
                    }]
                }
                var spplOptions = {
                    legend: {
                        display: false
                    }
                }
                var sppl = new Chart(spplChart, {
                    type: 'doughnut',
                    data: spplData,
                    options: spplOptions
                });

                //-----------------------
                // - MONTHLY SALES CHART -
                //-----------------------

                var StatisticCanvas = $('#Statistic').get(0).getContext('2d')

                let prov_label = <?php echo json_encode($prov_label); ?>;
                let prov_uklupl = <?php echo json_encode($prov_uklupl); ?>;
                let prov_sppl = <?php echo json_encode($prov_sppl); ?>;
                let prov_total = <?php echo json_encode($prov_total); ?>;

                var StatisticData = {
                    labels: prov_label,
                    datasets: [{
                            label: 'UKLUPL',
                            data: prov_uklupl,
                            fill: false,
                            backgroundColor: '#f56954',
                            tension: 0.1
                        },
                        {
                            label: 'SPPL',
                            data: prov_sppl,
                            fill: false,
                            backgroundColor: '#7FFF00',
                            tension: 0.1
                        }
                    ]
                }

                var StatisticOptions = {
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

                var Statistic = new Chart(StatisticCanvas, {
                    type: 'bar',
                    data: StatisticData,
                    options: StatisticOptions
                })

                //STATISIK KEGIATAN

                var KegiatanCanvas = $('#Kegiatan').get(0).getContext('2d')

                let stat_label = <?php echo json_encode($stat_label); ?>;
                let stat_data = <?php echo json_encode($stat_data); ?>;

                var KegiatanData = {
                    labels: stat_label,
                    datasets: [{
                        label: 'Statistik',
                        data: stat_data,
                        fill: false,
                        borderColor: '#00a65a',
                        tension: 0.1
                    }]
                }

                var KegiatanOptions = {
                    maintainAspectRatio: true,
                    responsive: true,
                }

                var Kegiatan = new Chart(KegiatanCanvas, {
                    type: 'line',
                    data: KegiatanData,
                    options: KegiatanOptions
                })

                // Cluster Donut

                var ClusterCanvas = $('#Cluster').get(0).getContext('2d')

                let cluster_label = <?php echo json_encode($cluster_label); ?>;
                let cluster_data = <?php echo json_encode($cluster_data); ?>;

                var ClusterData = {
                    labels: cluster_label,
                    datasets: [{
                        label: 'Total',
                        data: cluster_data,
                        backgroundColor: ['rgba(255, 99, 132)',
                            'rgba(255, 159, 64)',
                            'rgba(255, 205, 86)',
                            'rgba(75, 192, 192)',
                            'rgba(54, 162, 235)',
                            'rgba(153, 102, 255)',
                            'rgba(201, 203, 207)',
                            'rgba(255, 99, 132)',
                            'rgba(255, 159, 64)',
                            'rgba(255, 205, 86)',
                            'rgba(75, 192, 192)'
                        ]
                    }]
                }
                var ClusterOptions = {
                    legend: {
                        display: false
                    }
                }
                var Cluster = new Chart(ClusterCanvas, {
                    type: 'doughnut',
                    data: ClusterData,
                    options: ClusterOptions
                });

                $('#world-map-markers').mapael({
                    map: {
                        name: 'usa_states',
                        zoom: {
                            enabled: true,
                            maxLevel: 10
                        }
                    }
                });
            });
        });
    </script>

    // {{-- @include('layouts.uklupl_sppl') --}}
@endpush

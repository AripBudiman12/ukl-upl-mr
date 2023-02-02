<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>UKL-UPL Menengah Rendah dan SPPL</title>

    <link rel="stylesheet" type="text/css" href="{{ asset('css/preview.css') }}">
    <link rel="stylesheet" href="{{ asset('css/jquery.datatables.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/responsive.datatables.min.css') }}}">
    <link rel="stylesheet" href="{{ asset('css/cloudflare.twitter-bootstrap.css') }}">

    <link rel="stylesheet" href="{{ asset('plugins/fontawesome-free/css/all.min.css') }}">
    {{-- <link rel="stylesheet" href="{{ asset('dist/css/adminlte.min.css') }}"> --}}
    <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">

</head>

<body>
    <div class="title" style="background-color: #133715; color: white;">
        <div style="padding: 10px; text-align: center;">
            <a href="https://amdalnet.menlhk.go.id" target="_blank">
                <img src="{{ asset('img/lhk_logo.png') }}" style="max-height: 80px; height:auto;">
                <img src="{{ asset('img/logo-amdal-white.png') }}" href="amdalnet.menlhk.go.id"
                    style="max-height: 80px; height:auto;">
            </a>
            <h1 class="card-title">Daftar Rekap SPPL dan UKL-UPL Menengah Rendah</h1>
        </div>

    </div>

    <div class="card" style="background-color: #133715; color: white;">
        <div class="card-header" style="background-color: #628462;">
            <div class="form-group">
                <label><strong>Kewenangan :</strong></label>
                <select id='status' class="form-control" style="width: 200px">
                    <option value="">--Pilih Kewenangan--</option>
                    <option value="0">Kab / Kot</option>
                    <option value="1">Provinsi</option>
                    <option value="2">Pusat</option>
                </select>
            </div>
        </div>
        <div class="card-body" style="background-color: #deebde;">
            <table id='dataTable' class="table hover table-bordered table-striped"
                style="table-layout: fixed; background-color: white;">
                <thead>
                    <tr>
                        <th>Jenis Kegiatan/Usaha</th>
                        <th>Judul Usaha/Kegiatan</th>
                        <th>Kewenagan</th>
                        <th>Jenis Dokling</th>
                    </tr>
                </thead>
                <tbody>

                </tbody>
            </table>
        </div>
    </div>

    <table>
        <tr>
            <td colspan="2" style="padding-left: 30px;">
                <br>
                <div style="background-color: white;">
                    <canvas id="myChartt"></canvas>
                </div>
            </td>
        </tr>
        <tr>
            <td style="padding-left: 30px;">
                <br>
                <div style="background-color: white; width: 700px; height: 300px;">
                    <canvas id="myChart"></canvas>
                </div>
                <br>
            </td>
            <td style="padding-left: 30px;">
                <br>
                <div style="background-color: white; width: 700px; height: 300px;">
                    <div id="piechart_3d"></div>
                </div>
                <br>
            </td>
        </tr>
    </table>

    <div class="wrapper">
        <div class="content-wrapper">
            <section class="content">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">Browser Usage</h3>
                                    <div class="card-tools">
                                        <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                            <i class="fas fa-minus"></i>
                                        </button>
                                        <button type="button" class="btn btn-tool" data-card-widget="remove">
                                            <i class="fas fa-times"></i>
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
                                                <li><i class="far fa-circle text-danger"></i> Chrome</li>
                                                <li><i class="far fa-circle text-success"></i> IE</li>
                                                <li><i class="far fa-circle text-warning"></i> FireFox</li>
                                                <li><i class="far fa-circle text-info"></i> Safari</li>
                                                <li><i class="far fa-circle text-primary"></i> Opera</li>
                                                <li><i class="far fa-circle text-secondary"></i> Navigator</li>
                                            </ul>
                                        </div>

                                    </div>

                                </div>

                                <div class="card-footer p-0">
                                    <ul class="nav nav-pills flex-column">
                                        <li class="nav-item">
                                            <a href="#" class="nav-link">
                                                United States of America
                                                <span class="float-right text-danger">
                                                    <i class="fas fa-arrow-down text-sm"></i>
                                                    12%</span>
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a href="#" class="nav-link">
                                                India
                                                <span class="float-right text-success">
                                                    <i class="fas fa-arrow-up text-sm"></i> 4%
                                                </span>
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a href="#" class="nav-link">
                                                China
                                                <span class="float-right text-warning">
                                                    <i class="fas fa-arrow-left text-sm"></i> 0%
                                                </span>
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>
    <!-- Script -->
    <script src="{{ asset('js/jquery-3.5.1.js') }}"></script>
    <script src="{{ asset('js/1.12.1-jquery.datatables.min.js') }}"></script>
    <script src="{{ asset('js/2.2.9-datatables.responsive.min.js') }}"></script>
    <script src="{{ asset('js/datatables.bootstrap4.min.js') }}"></script>


    {{-- <script src="{{ asset('plugins/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script> --}}
    <script src="{{ asset('dist/js/adminlte.js') }}"></script>
    <script src="{{ asset('plugins/chart.js/Chart.min.js') }}"></script>
    {{-- <script src="{{ asset('dist/js/demo.js') }}"></script> --}}
    <script src="{{ asset('dist/js/pages/dashboard3.js') }}"></script>


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


                    ajax: {
                        url: "{{ route('index.data') }}",
                        type: "GET"
                    },



                    'columns': [{
                            data: 'jenis_kegiatan',
                            name: 'jenis_kegiatan'
                        },
                        {
                            data: 'judul_kegiatan',
                            name: 'judul_kegiatan'
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
        });
    </script>


    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const ctx = document.getElementById('myChartt');

        let data = <?php echo json_encode($data); ?>;
        let label = <?php echo json_encode($label); ?>;

        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: label,
                datasets: [{
                    label: '# of Votes',
                    data: data,
                    borderWidth: 3
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    </script>

    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    <script>
        google.charts.load("current", {
            packages: ["corechart"]
        });
        google.charts.setOnLoadCallback(drawChart);

        function drawChart() {
            let donuts = <?php echo json_encode($donut); ?>;

            var data = google.visualization.arrayToDataTable(donuts);

            var options = {
                title: 'Kewenangan SPPL dan UKL-UPL Menengah Rendah',
                autoWidth: true,
                is3D: true,
            };

            var chart = new google.visualization.PieChart(document.getElementById('piechart_3d'));
            chart.draw(data, options);
        }
    </script>

    <script src="{{ mix('/js/app.js') }}"></script>

</body>

</html>

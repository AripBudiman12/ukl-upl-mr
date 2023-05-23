<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Jadwal Rapat</title>
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.css" />
    <link rel="stylesheet" href="{{ asset('css/responsive.datatables.min.css') }}}">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.4.0/fullcalendar.css" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.18.1/moment.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.4.0/fullcalendar.min.js"></script>
    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>

    {{-- <script src="{{ asset('js/jquery-3.5.1.js') }}"></script> --}}
    <script src="{{ asset('js/1.12.1-jquery.datatables.min.js') }}"></script>
    <script src="{{ asset('js/2.2.9-datatables.responsive.min.js') }}"></script>
    <style>
        div.dataTables_length {
            margin-bottom: 2em;
            background: transparent;
        }
    </style>
</head>

<body style="background-color: rgb(231, 255, 231)">
    <div class="modal fade mt-auto" id="bookingModal" tabindex="+1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header justify-content-center">
                    <h5 class="modal-title text-center" id="exampleModalLabel">Detail Rapat</h5>
                    {{-- <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button> --}}
                </div>
                <div class="modal-body">
                    <div>
                        <span><b>Nama Perusahaan :</b></span>
                        <br>
                        <span style="font-size: 18px" id="perusahaan"></span>
                    </div>
                    <div>
                        <span><b>Kegiatan :</b></span>
                        <br>
                        <span style="font-size: 18px" id="kegiatan"></span>
                    </div>
                    <div>
                        <span><b>Tanggal Rapat :</b></span>
                        <br>
                        <span style="font-size: 18px" id="tanggal"></span>
                    </div>
                    <div>
                        <span><b>Jam Rapat :</b></span>
                        <br>
                        <span style="font-size: 18px" id="jam"></span>
                    </div>
                    <div>
                        <span><b>Keterangan :</b></span>
                        <br>
                        <span style="font-size: 18px" id="keterangan"></span>
                    </div>
                </div>
                {{-- <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div> --}}
            </div>
        </div>
    </div>

    <div class="container">
        <div class="mx-lg-5">
            <h3 class="text-center mt-3 mb-3">Jadwal Rapat</h3>
            <hr>
            <div class="mt-3 mb-3">
    
                <div id="calendar">
    
                </div>
    
            </div>
        </div>
        <hr class="my-5">
        <div class="card mb-3">
            <div class="card-body">
                <table id='dataTable' class="table hover table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama Perusahaan</th>
                            <th>Kegiatan</th>
                            <th>Tanggal Rapat</th>
                            <th>Jam Mulai Rapat</th>
                            <th>Keterangan</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.min.js"
        integrity="sha384-QJHtvGhmr9XOIpI6YVutG+2QOK9T+ZnN4kzFN1RtK3zEFEIsxhlmWl5/YESvpZ13" crossorigin="anonymous">
    </script>
    <script>
        $(document).ready(function() {
            function fetch_data() {
                $('#dataTable').DataTable({
                    'responsive': true,
                    'lengthChange': true,
                    'autoWidth': true,
                    'pageLength': 10,
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
                    scrollY: false,
                    scroller: {
                        loadingIndicator: true
                    },
                    ajax: {
                        type: "GET",
                        url: "{{ route('tabel.data') }}",
                    },
                    'columns': [
                        {data: 'nomor', name: 'nomor'},
                        {data: 'nama_perusahaan', name: 'nama_perusahaan'},
                        {data: 'title', name: 'title'},
                        {data: 'start', name: 'start'},
                        {data: 'jam_rapat', name: 'jam_rapat'},
                        {data: 'keterangan', name: 'keterangan'},
                    ],
                });
            }
            fetch_data()

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            var jadwal = @json($datas);

            $('#calendar').fullCalendar({
                header: {
                    left: 'prev',
                    center: 'title',
                    right: 'next',
                },
                events: jadwal,
                selectable: true,
                selectHelper: true,
                eventColor: '#1e751e',
                eventClick: function(event) {
                    $('#bookingModal').modal('toggle');
                    $('#perusahaan').text(event.perusahaan);
                    $('#kegiatan').text(event.kegiatan);
                    $('#tanggal').text(event.tanggal);
                    $('#jam').text(event.jam);
                    $('#keterangan').text(event.keterangan);
                },
            });
        });
    </script>
</body>

</html>

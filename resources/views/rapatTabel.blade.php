@extends('layouts.master')

@section('section')
    <div class="container-fluid pt-3" style="background-color: #628462;">
        <div class="row-12 mb-3">
            <h3 class="text-center"><b style="color:white;">Jadwal Rapat</b></h3>
        </div>

        <div class="card" style="background-color: #133715; color: white;">
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
@endsection

@push('scripts')
    <script type="text/javascript">
        $(document).ready(function() {
            function fetch_data() {
                $('#dataTable').DataTable({
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
        });
    </script>
@endpush

@extends('layouts.master')

@section('section')
    <div class="container-fluid pt-3" style="background-color: #628462;">
        <div class="row-12 mb-3">
            {{-- <a href="https://amdalnet-dev.menlhk.go.id/#/dashboard"><button class="btn btn-light float-left">Kembali</button></a> --}}
            {{-- <a class="nav-link" data-widget="pushmenu" href="#" role="button"><button class="btn btn-light float-left">Kembali</button></a> --}}
            <h3 class="text-center"><b style="color:white;">Jadwal Rapat</b></h3>
        </div>

        <div class="card" style="background-color: #133715; color: white;">
            {{-- <div class="card-header" style="background-color: #628462;">
                <div class="form-group"></div>
            </div> --}}
            <div class="card-body">
                <table id='dataTable' class="table hover table-bordered table-striped" style="table-layout: fixed;">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama Perusahaan</th>
                            <th>Kegiatan</th>
                            <th>Tanggal Rapat</th>
                            <th>Jam Mulai Rapat</th>
                            <th>Keterangan</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script type="text/javascript">
        // $(document).ready(function() {
            var table = $('#dataTable').DataTable({
                autoWidth: true,
                lengthChange: true,
                scrollX: true,
                lengthmenu: [
                    [10, 25, 50, -1],
                    [10, 25, 50, 'All']
                ],
                processing: true,
                serverSide: true,
                ajax: "{{ route('tabel.data') }}",
                columns: [
                    {data: 'nomor', name: 'nomor'},
                    {data: 'nama_perusahaan', name: 'nama_perusahaan'},
                    {data: 'title', name: 'title'},
                    {data: 'start', name: 'start'},
                    {data: 'jam_rapat', name: 'jam_rapat'},
                    {data: 'keterangan', name: 'keterangan'},
                ],
                buttons: ['excel', 'colvis'],
            });
            // function filter() {
            //     table.ajax.reload(null, false)
            // }
        // });
    </script>

    // {{-- @include('layouts.uklupl_sppl') --}}
@endpush

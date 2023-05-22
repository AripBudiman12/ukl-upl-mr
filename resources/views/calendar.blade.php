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

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.4.0/fullcalendar.css" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.18.1/moment.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.4.0/fullcalendar.min.js"></script>
    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
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

    <div class="mx-lg-5">
        <h3 class="text-center mt-3 mb-3">Jadwal Rapat</h3>
        <hr>
        <div class="mt-3 mb-3">

            <div id="calendar">

            </div>

        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.min.js"
        integrity="sha384-QJHtvGhmr9XOIpI6YVutG+2QOK9T+ZnN4kzFN1RtK3zEFEIsxhlmWl5/YESvpZ13" crossorigin="anonymous">
    </script>
    <script>
        $(document).ready(function() {

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

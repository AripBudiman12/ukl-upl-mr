<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Dashboard MR & R</title>

    {{-- css --}}
    <link rel="stylesheet" type="text/css" href="{{ asset('css/preview.css') }}">
    <link rel="stylesheet" href="{{ asset('css/jquery.datatables.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/responsive.datatables.min.css') }}}">
    <link rel="stylesheet" href="{{ asset('css/cloudflare.twitter-bootstrap.css') }}">
    <link rel="stylesheet" href="{{ asset('css/loading.css') }}">

    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <link rel="stylesheet" href="{{ asset('plugins/fontawesome-free/css/all.min.css') }}">
    <link rel="stylesheet" href="{{ asset('plugins/overlayScrollbars/css/OverlayScrollbars.min.css') }}">
    <link rel="stylesheet" href="{{ asset('dist/css/adminlte.min.css?v=3.2.0') }}">

    <style>
        #modalContent {
            display: flex;
            justify-content: center;
            align-items: center;
            text-align: center;
        }
        #modalContent > div {
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        #preloader {
            position: fixed;
            left: 0;
            top: 0;
            z-index: 99999;
            height: 100%;
            width: 100%;
            background: #fff;
            display: flex;
        }
        .loader{
            margin: auto;
            height: 100px;
            width: 100px;
            border-radius: 100%;
            position: relative;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .loader-text{
            margin: auto;
            height: 50px;
            width: 50px;
            /* border-radius: 50%; */
            position: relative;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .loader:before{
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            height: 100%;
            width: 100%;
            background: #000;
            border-radius: 50%;
            opacity: 0;
            animation: popin 1.5s linear infinite 0s;
        }
        .loader:after{
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            height: 100%;
            width: 100%;
            background: #000;
            border-radius: 50%;
            opacity: 0;
            animation: popin 1.5s linear infinite 0.5s;
        }

        @keyframes popin{
            0%{
                opacity: 0;
                transform: scale(0);
            }
            1%{
                opacity: 0.1;
                transform: scale(0);
            }
            99%{
                opacity: 0;
                transform: scale(2);
            }
            100%{
                opacity: 0;
                transform: scale(0);
            }
        }
    </style>

    {{-- <script nonce="35ffcdf7-fe60-4ada-a7ee-09091d8615ed">
        (function(w, d) {
            ! function(f, g, h, i) {
                f[h] = f[h] || {};
                f[h].executed = [];
                f.zaraz = {
                    deferred: [],
                    listeners: []
                };
                f.zaraz.q = [];
                f.zaraz._f = function(j) {
                    return function() {
                        var k = Array.prototype.slice.call(arguments);
                        f.zaraz.q.push({
                            m: j,
                            a: k
                        })
                    }
                };
                for (const l of ["track", "set", "debug"]) f.zaraz[l] = f.zaraz._f(l);
                f.zaraz.init = () => {
                    var m = g.getElementsByTagName(i)[0],
                        n = g.createElement(i),
                        o = g.getElementsByTagName("title")[0];
                    o && (f[h].t = g.getElementsByTagName("title")[0].text);
                    f[h].x = Math.random();
                    f[h].w = f.screen.width;
                    f[h].h = f.screen.height;
                    f[h].j = f.innerHeight;
                    f[h].e = f.innerWidth;
                    f[h].l = f.location.href;
                    f[h].r = g.referrer;
                    f[h].k = f.screen.colorDepth;
                    f[h].n = g.characterSet;
                    f[h].o = (new Date).getTimezoneOffset();
                    if (f.dataLayer)
                        for (const s of Object.entries(Object.entries(dataLayer).reduce(((t, u) => ({
                                ...t[1],
                                ...u[1]
                            }))))) zaraz.set(s[0], s[1], {
                            scope: "page"
                        });
                    f[h].q = [];
                    for (; f.zaraz.q.length;) {
                        const v = f.zaraz.q.shift();
                        f[h].q.push(v)
                    }
                    n.defer = !0;
                    for (const w of [localStorage, sessionStorage]) Object.keys(w || {}).filter((y => y.startsWith(
                        "_zaraz_"))).forEach((x => {
                        try {
                            f[h]["z_" + x.slice(7)] = JSON.parse(w.getItem(x))
                        } catch {
                            f[h]["z_" + x.slice(7)] = w.getItem(x)
                        }
                    }));
                    n.referrerPolicy = "origin";
                    n.src = "/cdn-cgi/zaraz/s.js?z=" + btoa(encodeURIComponent(JSON.stringify(f[h])));
                    m.parentNode.insertBefore(n, m)
                };
                ["complete", "interactive"].includes(g.readyState) ? zaraz.init() : f.addEventListener(
                    "DOMContentLoaded", zaraz.init)
            }(w, d, "zarazData", "script");
        })(window, document);
    </script> --}}

    {{-- <script>
        (function ($) {
            // "use strict";

            var preloader = $("#preloader");
            $(window).on("load", function () {
                setTimeout(() => {
                    preloader.fadeOut("slow", function () {
                        $(this).remove();
                    });
                }, 20);
            });
        })(jQuery);
    </script> --}}
</head>

<body class="hold-transition layout-fixed" style="background-color: #133715;">
    <section class="content">
        @yield('section')
    </section>

    <!-- JS Includes -->
    <script src="{{ asset('plugins/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js') }}"></script>
    <script src="{{ asset('dist/js/adminlte.js?v=3.2.0') }}"></script>
    <script src="{{ asset('plugins/jquery-mousewheel/jquery.mousewheel.js') }}"></script>
    <script src="{{ asset('plugins/raphael/raphael.min.js') }}"></script>
    <script src="{{ asset('plugins/jquery-mapael/jquery.mapael.min.js') }}"></script>
    <script src="{{ asset('plugins/jquery-mapael/maps/usa_states.min.js') }}"></script>
    <script src="{{ asset('plugins/chart.js/Chart.min.js') }}"></script>
    <script src="{{ asset('dist/js/demo.js') }}"></script>

    {{-- <script src="{{ asset('plugins/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js') }}"></script>
    <script src="{{ asset('dist/js/adminlte.js?v=3.2.0') }}"></script>
    <script src="{{ asset('plugins/jquery-mousewheel/jquery.mousewheel.js') }}"></script>
    <script src="{{ asset('plugins/raphael/raphael.min.js') }}"></script>
    <script src="{{ asset('plugins/jquery-mapael/jquery.mapael.min.js') }}"></script>
    <script src="{{ asset('plugins/jquery-mapael/maps/usa_states.min.js') }}"></script>
    <script src="{{ asset('plugins/chart.js/Chart.min.js') }}"></script> --}}
    {{-- <script src="https://cdn.jsdelivr.net/npm/chart.js"></script> --}}
    {{-- <script src="{{ asset('dist/js/demo.js') }}"></script> --}}
    {{-- <script src="{{ asset('dist/js/pages/dashboard2.js') }}"></script> --}}

    {{-- <!-- Script -->
    <script src="{{ asset('js/loading.js') }}"></script>
    <script src="{{ asset('js/jquery-3.5.1.js') }}"></script>
    <script src="{{ asset('js/1.12.1-jquery.datatables.min.js') }}"></script>
    <script src="{{ asset('js/2.2.9-datatables.responsive.min.js') }}"></script>
    <script src="{{ asset('js/datatables.bootstrap4.min.js') }}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.10.2/fullcalendar.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script> --}}

    <!-- DataTables Scripts -->
    <script src="{{ asset('js/1.12.1-jquery.datatables.min.js') }}"></script>
    <script src="{{ asset('js/2.2.9-datatables.responsive.min.js') }}"></script>
    <script src="{{ asset('js/datatables.bootstrap4.min.js') }}"></script>

    <!-- Other Scripts -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.10.2/fullcalendar.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

    @stack('scripts')
</body>
    
</html>

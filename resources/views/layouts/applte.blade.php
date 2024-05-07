<!DOCTYPE html>
<!--
This is a starter template page. Use this page to start your new project from
scratch. This page gets rid of all links and provides the needed markup only.
-->
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="x-ua-compatible" content="ie=edge">

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.title', 'TL Delivery Tracking') }}</title>

    <script src="{{ asset('js/app.js') }}?version={{ $version }}" defer></script>

    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="/template/plugins/fontawesome-free/css/all.min.css">
    <!-- Theme style -->
    <link rel="stylesheet" href="/template/css/adminlte.min.css">
    <!-- Google Font: Source Sans Pro -->
    <link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700" rel="stylesheet">
    <link rel="stylesheet" href="plugins/toastr/toastr.min.css">

    <script>
        try {
            window.Laravel = {!! json_encode([
                'csrfToken' => csrf_token(),
                'access_token' => $access_token,
                'routes' => $routes,
                'current_user' => Auth::user(),
                'expand_menu_value' => $expand_menu_value,
            ]) !!};
        } catch (err) {

        }
    </script>
</head>

<body class="{{ $expand_menu }}" >
    <div class="wrapper" id="app">

        <!-- Navbar -->
        <nav class="main-header navbar navbar-expand navbar-white navbar-light">
            <a class="nav-link" data-widget="pushmenu" onclick="showHideLeftMenu()" href="#" role="button"><i
                    class="fas fa-bars text-secondary"></i></a>

            <router-link to="/dashboard" class="navbar-brand">
                <strong>DELIVERY TRACKING</strong>
            </router-link>

            <button class="navbar-toggler order-1" type="button" data-toggle="collapse" data-target="#navbarCollapse"
                aria-controls="navbarCollapse" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse order-3" id="navbarCollapse">
                <!-- Right navbar links -->
                <ul class="navbar-nav ml-auto">
                    <li class="nav-item dropdown user-menu">
                        <a href="#" class="nav-link dropdown-toggle" data-toggle="dropdown"
                            data-target="#user-profile">
                            <img src="{{ Auth::user()->avatar }}" class="user-image img-circle elevation-2"
                                alt="User a">
                            <strong class="d-none d-md-inline">{{ Auth::user()->name }}</strong>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-lg dropdown-menu-right" id="user-profile"
                            style="left: inherit; right: -8px; border: 0; margin-top: 8px">
                            <!-- User image -->
                            <li class="user-header">
                                <img src="{{ Auth::user()->avatar }}" class="img-circle elevation-2" alt="User Image">

                                <p class="nav-link">
                                    <strong> {{ Auth::user()->name }} </strong>
                                    <small>Thành viên từ {{ Auth::user()->created_at }}</small>
                                </p>
                            </li>
                            <!-- Menu Footer-->
                            <li class="user-footer">
                                <a href="/profile" class="btn btn-default btn-flat"
                                    style="border-width: 1px; border-color: #000">
                                    Hồ sơ cá nhân</a>
                                <a href="{{ route('logout') }}"
                                    onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
                                    class="btn btn-default btn-flat float-right"
                                    style="border-width: 1px; border-color: #000">Đăng xuất</a>
                                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                    @csrf
                                </form>
                            </li>
                        </ul>
                    </li>
                </ul>
            </div>
        </nav>
        <!-- /.navbar -->
        <!-- Main Sidebar Container -->
        <aside class="main-sidebar sidebar-dark-primary elevation-4">
            <!-- Brand Logo -->
            <a href="/" class="brand-link">
                <img src="/icon_tl.png" alt="TL Logo" class="brand-image m-auto" style="opacity: .8">
                <i><strong class="brand-text">Control Panel</strong></i>
            </a>

            <!-- Sidebar -->
            <div class="sidebar">
                <!-- Sidebar Menu -->
                <nav class="mt-2">
                    <menu-router v-bind:menus="{{ $menus }}">
                    </menu-router>
                </nav>
                <!-- /.sidebar-menu -->
            </div>
            <!-- /.sidebar -->
        </aside>
        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">
            <!-- Main content -->
            <div class="content">
                <div class="container-fluid">
                    @yield('content')
                    <!-- /.col-md-6 -->
                    <!-- /.row -->
                </div><!-- /.container-fluid -->
            </div>
            <!-- /.content -->
        </div>
        <!-- /.content-wrapper -->

        <!-- Main Footer -->
        <footer class="main-footer">
            <!-- To the right -->
            <div class="float-right d-none d-sm-inline">
                Thien Long Corporation - Version {{ $version }}
            </div>
            <!-- Default to the left -->
            <strong>Liên hệ hỗ trợ: </strong> Phòng CNTT - Tập đoàn Thiên Long
        </footer>
    </div>
    <!-- ./wrapper -->

    <!-- REQUIRED SCRIPTS -->


    <!-- jQuery -->
    <script src="/template/plugins/jquery/jquery.min.js"></script>
    <!-- Bootstrap 4 -->
    <script src="/template/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
    <!-- AdminLTE App -->
    <script src="/template/js/adminlte.min.js"></script>
    <script>
        function showHideLeftMenu() {
            // alert(window.Laravel.expand_menu);
            if (window.Laravel.expand_menu_value == 1) {
                $('body').addClass('sidebar-collapse');
                //$('#menu_hotline').removeClass("show-hotline")
                $('#menu_hotline').addClass("show-hotline");;
                $('#menu_hotline').removeClass("hide-hotline")
                window.Laravel.expand_menu_value = 0;

            } else {
                $('body').removeClass('sidebar-collapse');
                $('#menu_hotline').removeClass("show-hotline");
                $('#menu_hotline').addClass("hide-hotline");
                window.Laravel.expand_menu_value = 1;

            }
            var data = {
                'code': 'expand_menu',
                'value': window.Laravel.expand_menu_value
            };
            var page_url = '/api/expand-left-menu';

            $.ajax({
                type: 'post',
                url: page_url,
                data: data,
                dataType: 'json',
                headers: {
                    "Authorization": 'Bearer ' + window.Laravel.access_token,
                   'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                },
                success: callback,
            });


        }
        function callback(data, status) {
            //Chưa xử lý
        }
    </script>
</body>

</html>

<!DOCTYPE html>
<html lang="en">

<head>
    @include('layout.meta')

    <title>{{ _site_title() }} | @yield('title')</title>
    
    @include('layout.styles')

    <style>
        .page-footer{
            position: fixed;
            top: 550px;
            left: 222px;
        }
    </style>

</head>

<body class="fixed-navbar">
    <div class="page-wrapper">
        @include('layout.header')

        @include('layout.sidebar')

        <div class="content-wrapper">
            @yield('content')
            
            @include('layout.footer')
        </div>
    </div>
    
    @include('layout.theme-config')

    <div class="sidenav-backdrop backdrop"></div>
    <div class="preloader-backdrop">
        <div class="page-preloader">Loading</div>
    </div>
    
    @include('layout.scripts')
    <script>
        // Bootstrap datepicker
            $('#date_1 .input-group.date').datepicker({
                todayBtn: "linked",
                keyboardNavigation: false,
                forceParse: false,
                calendarWeeks: true,
                autoclose: true,
                format: 'yyyy-mm-dd'
            });
    </script>
</body>

</html>
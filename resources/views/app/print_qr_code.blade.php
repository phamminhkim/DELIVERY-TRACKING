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

    {{-- <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.title', 'TL Delivery Tracking') }}</title>

    <script src="{{ asset('js/app.js') }}" defer></script> --}}
    <style>
        * {
            padding: 0;
            margin: 0;
        }

        .container {
            width: 5.2cm;
            height: 3.5cm;
            margin-top: 0.15cm;
            margin-left: 0.2cm;
            margin-bottom: 0.15cm;
            border: 1px solid #000;
        }

        #svg-container {
            position: relative;
            width: 100%;
            height: 100%;
        }

        div>svg {
            position: absolute;
            right: 1cm;
            bottom: 0.2cm;
        }

        .so-number {
            position: absolute;
            top: 0.1cm;
            right: 1cm;
        }

        .so-number>p {
            font-size: 0.4cm
        }

        .do-number {
            position: absolute;
            left: -0.2cm;
            top: 1.7cm;
            transform: rotate(-90deg);
        }

        .do-number>p {
            font-size: 0.4cm
        }
    </style>
</head>

<body>
    <div class="container">
        <div id="svg-container" style="position: relative">
            <div class="so-number">
                <p>SO: 3080313038</p>
            </div>
            <div class="do-number">
                <p>DO: 3080313038</p>
            </div>
        </div>
    </div>


    <script>
        // const doc = window.document;
        const qr_code = "{!! $qr_code !!}";
        const parser = new DOMParser();
        const svgDoc = parser.parseFromString(qr_code, "image/svg+xml");
        var svgElement = svgDoc.documentElement;
        var container = document.getElementById("svg-container");
        container.appendChild(svgElement);
    </script>
</body>

</html>

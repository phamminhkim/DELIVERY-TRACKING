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

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.title', 'TL Delivery Tracking') }}</title>

    <style>
        * {
            padding: 0;
            margin: 0;
        }

        .container {
            margin-left: 0cm;
            padding-top: 0cm;
        }

        .barcode-container {
            /* width: 5.0cm;
            height: 3.0cm; */
            width: 1cm;
            height: 1cm;
            box-sizing: border-box;
            border: 0;
        }

        .svg-container {
            position: relative;
            width: 100%;
            height: 100%;
        }

        svg {
            position: absolute;
            /* right: 1cm;
            bottom: 0.2cm; */
            top: 1cm;
            left: 1cm;
        }

        .do-number {
            position: absolute;
            /* top: 0.1cm;
            right: 1cm; */
            top: 1cm;
            left: 1cm;
        }

        .do-number>p {
            font-size: 0.39cm;
            font-family: monospace;
        }

        .so-number {
            position: absolute;
            /* left: -0.7cm;
            top: 1.3cm; */
            top: 1cm;
            left: 1cm;
            transform: rotate(-90deg);
        }

        .so-number>p {
            font-size: 0.39cm;
            font-family: monospace;

        }

        @media print {
            @page {
                margin-top: 0;
                margin-bottom: 0;
            }
        }
    </style>
</head>

<body>
    <div class="container">
        @for ($i = 0; $i < count($qr_datas); $i++)
            <div class="barcode-container">
                <div id="svg-container-{{ $i }}" class="svg-container">
                    <div class="do-number">
                        <p>DO:{{ !empty($qr_datas[$i]['sap_do_number']) ? $qr_datas[$i]['sap_do_number'] : '0000000000' }}
                        </p>
                    </div>
                    <div class="so-number">
                        <p>
                            {{ !empty($qr_datas[$i]['delivery_code']) ? $qr_datas[$i]['delivery_code'] : 'Z000000ZZZ0ZZ' }}
                        </p>
                    </div>
                </div>
            </div>
        @endfor
    </div>


    <script>
        const qr_datas = @json($qr_datas);
        const print_config = @json($print_config);
        const parser = new DOMParser();
        qr_datas.forEach((qr_data, index) => {
            let svgDoc = parser.parseFromString(qr_data.qr_code, "image/svg+xml");
            let svgElement = svgDoc.documentElement;
            let container = document.getElementById(`svg-container-${index}`);
            container.appendChild(svgElement);
        });

        let card_container_class = document.querySelectorAll('.barcode-container');
        card_container_class.forEach((card_container) => {
            card_container.style.width = print_config.dimension.width;
            card_container.style.height = print_config.dimension.height;
        });
        const height = Number(print_config.dimension.height.split('cm')[0]);
        card_container_class[card_container_class.length - 1].style.height = `${height - 0.1}cm`;

        let do_class = document.querySelectorAll('.do-number');
        do_class.forEach(do_class => {
            do_class.style.top = print_config.DO.top;
            do_class.style.left = print_config.DO.left
        })


        let so_class = document.querySelectorAll('.so-number');
        so_class.forEach(so_class => {
            so_class.style.top = print_config.SO.top;
            so_class.style.left = print_config.SO.left
        })

        let qr_code_class = document.querySelectorAll('svg');
        qr_code_class.forEach(qr_code => {
            qr_code.style.top = print_config.qr_code.top;
            qr_code.style.left = print_config.qr_code.left;
        })


        window.self.onload = function() {
            window.self.print();
        };
    </script>
</body>

</html>

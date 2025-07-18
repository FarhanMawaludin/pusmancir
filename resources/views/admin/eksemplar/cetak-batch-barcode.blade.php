<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Cetak Label Barcode - TJ121</title>
    <style>
        @page {
            size: 17.5cm 21.5cm;
            margin: 0;
        }

        body {
            margin: 0;
            padding: 0;
        }

        .sheet {
            width: 17.5cm;
            height: 21.5cm;
            padding: 0.2cm 0.5cm;
            box-sizing: border-box;
            display: grid;
            grid-template-columns: repeat(2, 8cm);
            grid-template-rows: repeat(5, 4cm);
            column-gap: 0.5cm;
            row-gap: 0.2cm;
        }

        .label {
            width: 8cm;
            height: 4cm;
            box-sizing: border-box;
            border: 0.5px dashed #ccc;
            padding: 0.25cm;
            font-family: Arial, sans-serif;
            font-size: 8pt;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .label-columns {
            display: flex;
            flex-direction: row;
            height: 100%;
        }

        .left-column {
            width: 75%;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .right-column {
            width: 25%;
            display: flex;
            justify-content: center;
            align-items: center;
            font-size: 7pt;
            font-weight: bold;
            text-align: center;
            padding-left: 0.2cm;
            box-sizing: border-box;
            word-break: break-word;
        }

        .no-panggil {
            writing-mode: vertical-rl;
            transform: rotate(180deg);
        }

        .label-top {
            display: flex;
            flex-direction: row;
            align-items: center;
            height: auto;
        }

        .top-left {
            display: flex;
            align-items: center;
            gap: 2mm;
        }

        .top-left img.logo {
            width: 0.8cm;
            height: auto;
        }

        .header-text {
            font-size: 5.5pt;
            line-height: 1.2;
            word-break: break-word;
        }

        .header-text strong {
            font-size: 6pt;
        }

        .label-content {
            display: flex;
            flex-direction: column;
            align-items: center;
            padding-top: 1mm;
        }

        .label-content p {
            margin: 0.5mm 0;
            font-size: 6.5pt;
            line-height: 1.1;
            text-align: center;
        }

        .label-content img.barcode {
            max-width: 85%;
            max-height: 1cm;
            object-fit: contain;
            margin: 1mm 0;
        }

        @media print {
            .sheet {
                page-break-inside: avoid;
            }
        }
    </style>
</head>
<body>
    <div class="sheet">
        {{-- Kosong awal jika diperlukan --}}
        @for ($i = 0; $i < $kosongAwal; $i++)
            <div class="label"></div>
        @endfor

        {{-- Isi label --}}
        @foreach ($eksemplarList as $eksemplar)
            @php
                $generator = new \Picqer\Barcode\BarcodeGeneratorPNG();
                $barcode = base64_encode(
                    $generator->getBarcode($eksemplar->no_rfid, $generator::TYPE_CODE_128)
                );
            @endphp

            <div class="label">
                <div class="label-columns">
                    {{-- Kolom Kiri --}}
                    <div class="left-column">
                        <div class="label-top">
                            <div class="top-left">
                                <img class="logo" src="{{ asset('/logo-banten.png') }}" alt="Logo Banten">
                                <img class="logo" src="{{ asset('/logo-smancir.png') }}" alt="Logo Smancir">
                                <div class="header-text">
                                    <strong>PUSMANCIR</strong><br>
                                    Perpustakaan SMAN 1 Ciruas<br>
                                    NPP: <strong>3604091E1000002</strong>
                                </div>
                            </div>
                        </div>

                        <div class="label-content">
                            <p><strong>{{ Str::limit($eksemplar->inventori->judul_buku, 25) }}</strong></p>
                            <p>{{ $eksemplar->no_induk }}</p>
                            <img class="barcode" src="data:image/png;base64,{{ $barcode }}" alt="Barcode">
                            <p>{{ $eksemplar->no_rfid }}</p>
                        </div>
                    </div>

                    {{-- Kolom Kanan --}}
                    <div class="right-column">
                        <div class="no-panggil">
                            {{ optional($eksemplar->inventori->katalog)->no_panggil ?? '-' }}
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <script>
        window.print();
    </script>
</body>
</html>

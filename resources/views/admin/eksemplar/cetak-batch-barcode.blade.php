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

            page-break-after: always; /* agar tiap sheet cetak di halaman baru */
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

            .bg-pink-dark,
            .bg-light,
            .bg-orange-dark,
            .bg-green-light,
            .bg-white,
            .bg-navy,
            .bg-yellow-light,
            .bg-orange-light,
            .bg-pink-light,
            .bg-default {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
        }

        .bg-pink-dark {
            background-color: #c53074;
            color: white;
        }

        .bg-light {
            background-color: #f3f4f6;
            color: #111;
        }

        .bg-orange-dark {
            background-color: #c05621;
            color: white;
        }

        .bg-green-light {
            background-color: #9ae6b4;
            color: #1a202c;
        }

        .bg-white {
            background-color: #ffffff;
            color: #000;
        }

        .bg-navy {
            background-color: #2c3e50;
            color: white;
        }

        .bg-yellow-light {
            background-color: #faf089;
            color: #1a202c;
        }

        .bg-orange-light {
            background-color: #f6ad55;
            color: #1a202c;
        }

        .bg-pink-light {
            background-color: #fbb6ce;
            color: #1a202c;
        }

        .bg-default {
            background-color: #e2e8f0;
            color: #1a202c;
        }
    </style>
</head>

<body>
    @php
        // Memecah eksemplarList jadi chunks 10 per halaman
        $chunkedEksemplar = $eksemplarList->chunk(10);
    @endphp

    @foreach ($chunkedEksemplar as $chunkIndex => $chunk)
        <div class="sheet">
            {{-- Kosong awal hanya untuk halaman pertama --}}
            @if ($chunkIndex === 0)
                @for ($i = 0; $i < $kosongAwal; $i++)
                    <div class="label"></div>
                @endfor
            @endif

            @foreach ($chunk as $eksemplar)
                @php
                    $generator = new \Picqer\Barcode\BarcodeGeneratorPNG();
                    $barcode = base64_encode($generator->getBarcode($eksemplar->no_rfid, $generator::TYPE_CODE_128));

                    $noPanggil = optional($eksemplar->inventori->katalog->first())->no_panggil ?? '-';
                    $firstDigit = is_numeric($noPanggil[0]) ? $noPanggil[0] : null;
                    $colorClass = match ($firstDigit) {
                        '0' => 'bg-pink-dark',
                        '1' => 'bg-light',
                        '2' => 'bg-orange-dark',
                        '3' => 'bg-green-light',
                        '4' => 'bg-white',
                        '5' => 'bg-navy',
                        '6' => 'bg-yellow-light',
                        '7' => 'bg-orange-light',
                        '8' => 'bg-pink-light',
                        '9' => 'bg-green-light',
                        default => 'bg-default',
                    };
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
                        <div class="right-column {{ $colorClass }}">
                            <div class="no-panggil">
                                {{ $noPanggil }}
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endforeach

    <script>
        window.print();
    </script>
</body>

</html>

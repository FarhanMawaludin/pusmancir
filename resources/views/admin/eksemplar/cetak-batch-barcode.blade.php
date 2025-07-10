<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Cetak Barcode Eksemplar</title>
    <style>
        @page {
            size: A4;
            margin: 10mm;
        }

        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }

        .page {
            display: grid;
            grid-template-columns: repeat(3, 66mm); /* 3 kolom */
            grid-auto-rows: 40mm; /* tinggi per label */
            gap: 0;
            justify-content: center;
        }

        .label {
            box-sizing: border-box;
            width: 66mm;
            height: 40mm;
            padding: 2mm;
            text-align: center;
            font-size: 10px;
            border: 0.2px dashed #ccc;
            overflow: hidden;
        }

        .label img {
            width: 100%;
            height: auto;
            margin-top: 2mm;
        }

        .label p {
            margin: 0;
            padding: 0;
            line-height: 1.1;
            word-wrap: break-word;
        }

        @media print {
            .page {
                page-break-inside: avoid;
            }
        }
    </style>
</head>
<body>
    <div class="page">
        {{-- Label kosong di awal --}}
        @for ($i = 0; $i < $kosongAwal; $i++)
            <div class="label"></div>
        @endfor

        {{-- Cetak setiap label --}}
        @foreach ($eksemplarList as $eksemplar)
            @php
                $generator = new \Picqer\Barcode\BarcodeGeneratorPNG();
                $barcode = base64_encode($generator->getBarcode($eksemplar->no_rfid, $generator::TYPE_CODE_128));
            @endphp

            <div class="label">
                <p><strong>{{ Str::limit($eksemplar->inventori->judul_buku, 25) }}</strong></p>
                <p>{{ $eksemplar->no_induk }}</p>
                <img src="data:image/png;base64,{{ $barcode }}" alt="Barcode">
                <p>{{ $eksemplar->no_rfid }}</p>
            </div>
        @endforeach
    </div>

    <script>
        window.print();
    </script>
</body>
</html>

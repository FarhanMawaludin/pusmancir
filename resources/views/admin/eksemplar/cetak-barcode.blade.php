@php
    use Picqer\Barcode\BarcodeGeneratorPNG;

    $generator = new BarcodeGeneratorPNG();
    $barcode = base64_encode($generator->getBarcode($eksemplar->no_rfid, $generator::TYPE_CODE_128));
@endphp

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Cetak Label Barcode - Satuan (TJ121)</title>
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

        .label-top {
            display: flex;
            flex-direction: row;
            align-items: center;
        }

        .top-left {
            width: 70%;
            display: flex;
            align-items: center;
            gap: 2mm;
        }

        .top-left img.logo {
            width: 9mm;
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

        .top-right {
            width: 30%;
            display: flex;
            justify-content: center;
            align-items: center;
            font-size: 7pt;
            font-weight: bold;
            text-align: center;
            word-break: break-word;
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

        .empty {
            width: 8cm;
            height: 4cm;
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
        {{-- Label pertama aktif --}}
        <div class="label">
            {{-- Header --}}
            <div class="label-top">
                <div class="top-left">
                    <img class="logo" src="{{ asset('/logo-banten.png') }}" alt="Logo Banten">
                    <img class="logo" src="{{ asset('/logo-smancir.png') }}" alt="Logo SMANCIR">
                    <div class="header-text">
                        <strong>PUSMANCIR</strong><br>
                        Perpustakaan SMAN 1 Ciruas<br>
                        NPP: <strong>3604091E1000002</strong>
                    </div>
                </div>
                <div class="top-right">
                    {{ optional($eksemplar->inventori->katalog)->no_panggil ?? '-' }}
                </div>
            </div>

            {{-- Konten --}}
            <div class="label-content">
                <p><strong>{{ Str::limit($eksemplar->inventori->judul_buku, 25) }}</strong></p>
                <p>{{ $eksemplar->no_induk }}</p>
                <img class="barcode" src="data:image/png;base64,{{ $barcode }}" alt="Barcode">
                <p>{{ $eksemplar->no_rfid }}</p>
            </div>
        </div>

        {{-- Kosongkan 9 slot sisanya (2 kolom x 5 baris = 10 slot total) --}}
        @for ($i = 0; $i < 9; $i++)
            <div class="empty"></div>
        @endfor
    </div>

    <script>
        window.print();
    </script>
</body>
</html>

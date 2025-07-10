@php
    use Picqer\Barcode\BarcodeGeneratorPNG;

    $generator = new BarcodeGeneratorPNG();
    $barcode = base64_encode($generator->getBarcode($eksemplar->no_rfid, $generator::TYPE_CODE_128));
@endphp

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Cetak Barcode</title>
    <style>
        body {
            font-family: Arial, sans-serif;
        }

        .barcode-wrapper {
            width: 300px;
            padding: 10px;
            border: 1px dashed #aaa;
            margin: 20px auto;
            text-align: center;
        }

        .barcode-wrapper p {
            margin: 5px 0;
        }

        @media print {
            body {
                margin: 0;
            }
        }
    </style>
</head>
<body>
    <div class="barcode-wrapper">
        <p><strong>{{ $eksemplar->inventori->judul_buku }}</strong></p>
        <p>{{ $eksemplar->no_induk }}</p>

        <img src="data:image/png;base64,{{ $barcode }}" alt="Barcode" />

        <p>{{ $eksemplar->no_rfid }}</p>
    </div>

    <script>
        window.print(); // Otomatis buka dialog cetak
    </script>
</body>
</html>

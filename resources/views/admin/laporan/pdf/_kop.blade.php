{{-- Kop surat laporan (dipakai semua PDF laporan). --}}
<table style="width:100%; border-collapse:collapse; border-bottom:3px double #1a1a1a; margin-bottom:10px;">
    <tr>
        <td style="width:70px; text-align:left; vertical-align:middle; padding-bottom:6px;">
            @if (file_exists(public_path('logo-banten.png')))
                <img src="{{ public_path('logo-banten.png') }}" style="height:62px;" alt="Logo Banten">
            @endif
        </td>
        <td style="text-align:center; vertical-align:middle; padding-bottom:6px; font-family:'Times New Roman', Times, serif;">
            <div style="font-size:12pt; font-weight:bold; letter-spacing:1px;">PEMERINTAH PROVINSI BANTEN</div>
            <div style="font-size:12pt; font-weight:bold; letter-spacing:1px;">DINAS PENDIDIKAN DAN KEBUDAYAAN</div>
            <div style="font-size:15pt; font-weight:bold; letter-spacing:2px; margin:1px 0;">UPT SMA NEGERI 1 CIRUAS</div>
            <div style="font-size:8.5pt;">Jalan Raya Jakarta Km 9,5 Serang Telp. 280043</div>
            <div style="font-size:8.5pt;">Situs web: www.smancir1.sch.id &nbsp;|&nbsp; Surel: ciruas@smancir1.sch.id</div>
        </td>
        <td style="width:70px; text-align:right; vertical-align:middle; padding-bottom:6px;">
            @if (file_exists(public_path('logo-smancir.png')))
                <img src="{{ public_path('logo-smancir.png') }}" style="height:62px;" alt="Logo SMAN 1 Ciruas">
            @endif
        </td>
    </tr>
</table>

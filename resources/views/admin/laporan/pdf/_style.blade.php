{{-- Gaya bersama laporan PDF A4 --}}
<style>
    @page { margin: 1.3cm 1.4cm 1.6cm 1.4cm; }
    body { font-family: 'Times New Roman', Times, serif; font-size: 10pt; color: #1a1a1a; margin: 0; }
    .judul-laporan { text-align: center; font-size: 13pt; font-weight: bold; text-transform: uppercase; letter-spacing: 1px; margin: 6px 0 2px; }
    .sub-judul { text-align: center; font-size: 10pt; margin: 0 0 2px; }
    .sub-kecil { text-align: center; font-size: 8.5pt; color: #555; margin: 0 0 10px; }
    h3.bagian { font-size: 10.5pt; margin: 14px 0 5px; text-transform: uppercase; }
    table.data { width: 100%; border-collapse: collapse; }
    table.data th { background: #e5e7eb; color: #111; border: 1px solid #333; padding: 4px 4px; font-size: 8.5pt; text-align: center; }
    table.data td { border: 1px solid #555; padding: 3px 4px; font-size: 8.5pt; vertical-align: top; }
    table.data tr { page-break-inside: avoid; }
    table.data tfoot td, table.data tr.total td { font-weight: bold; background: #f3f4f6; }
    .c { text-align: center; }
    .r { text-align: right; }
    .muted { color: #666; }
    .footer { position: fixed; bottom: -1.0cm; left: 0; right: 0; font-size: 7.5pt; color: #777; border-top: 1px solid #ccc; padding-top: 3px; }
    .footer .kanan { float: right; }
    .pagenum:after { content: counter(page) " / " counter(pages); }
</style>

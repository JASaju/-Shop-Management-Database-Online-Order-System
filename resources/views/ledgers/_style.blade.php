<style>
    @page { margin: 14mm 12mm 16mm 12mm; }
    @if (is_file(storage_path('fonts/SolaimanLipi.ttf')))
    @font-face { font-family: 'SolaimanLipi'; font-style: normal; font-weight: 400;
        src: url("{{ str_replace('\\', '/', storage_path('fonts/SolaimanLipi.ttf')) }}") format('truetype'); }
    @endif
    @if (is_file(storage_path('fonts/SolaimanLipi_Bold.ttf')))
    @font-face { font-family: 'SolaimanLipi'; font-style: normal; font-weight: 700;
        src: url("{{ str_replace('\\', '/', storage_path('fonts/SolaimanLipi_Bold.ttf')) }}") format('truetype'); }
    @endif
    body { font-family: 'SolaimanLipi', 'DejaVu Sans', sans-serif; font-size: 11px; color: #222; }
    h1 { font-size: 18px; margin: 0; text-align: center; }
    .sub { text-align: center; color: #555; margin: 2px 0 10px; }
    h2 { font-size: 14px; margin: 0; color: #1b5e20; }
    .addr { color: #555; margin-bottom: 4px; }
    table { width: 100%; border-collapse: collapse; margin-bottom: 4px; }
    th, td { border: 1px solid #999; padding: 3px 5px; }
    th { background: #e8f5e9; text-align: left; }
    .r { text-align: right; } .c { text-align: center; }
    .tot td { font-weight: bold; background: #f1f8e9; }
    .meta { font-size: 9px; color: #777; margin-top: 8px; }
    .neg { color: #b71c1c; }
    .section + .section { page-break-before: always; }
</style>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Laporan Penyebaran Warga</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 11px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { border: 1px solid #444; padding: 6px; text-align: center; }
        th { background-color: #f2f2f2; }
        h3 { text-align: center; margin-bottom: 10px; }
    </style>
</head>
<body>
<h3>Laporan Penyebaran Warga Berdasarkan {{ ucfirst(str_replace('_',' ', $filter)) }}</h3>

@foreach($dataRW as $rw => $rtGroup)
<h4>RW {{ $rw }}</h4>
<table>
    <thead>
        <tr>
            <th>RT</th>
            @php $columns = collect($rtGroup)->flatMap(fn($r)=>$r->keys())->unique(); @endphp
            @foreach($columns as $col)
                <th>{{ $col }}</th>
            @endforeach
            <th>Total</th>
        </tr>
    </thead>
    <tbody>
        @foreach($rtGroup as $rt => $counts)
        <tr>
            <td><strong>RT {{ $rt }}</strong></td>
            @foreach($columns as $col)
                <td>{{ $counts[$col] ?? 0 }}</td>
            @endforeach
            <td>{{ $counts->sum() }}</td>
        </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr>
            <th>Total RW {{ $rw }}</th>
            @foreach($columns as $col)
                <th>{{ collect($rtGroup)->sum(fn($x)=>$x[$col] ?? 0) }}</th>
            @endforeach
            <th>{{ collect($rtGroup)->flatten()->count() }}</th>
        </tr>
    </tfoot>
</table>
@endforeach
</body>
</html>

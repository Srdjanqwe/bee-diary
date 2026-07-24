<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<style>
* { margin: 0; padding: 0; box-sizing: border-box; }
body { font-family: 'DejaVu Sans', sans-serif; font-size: 10px; color: #222; }

.header {
    text-align: center;
    border-bottom: 1.5px solid #222;
    padding-bottom: 10px;
    margin-bottom: 16px;
}
.header h1 {
    font-size: 15px;
    font-weight: bold;
    text-transform: uppercase;
    letter-spacing: 1px;
}
.header .sub {
    font-size: 8px;
    color: #888;
    margin-top: 3px;
}

/* Stats */
.stats { width: 100%; border-collapse: collapse; margin-bottom: 18px; }
.stats td {
    text-align: center;
    padding: 8px 0;
    border-right: 1px solid #ddd;
    background: #f5f5f5;
}
.stats td:last-child { border-right: none; }
.stat-val { font-size: 14px; font-weight: bold; color: #111; }
.stat-lbl { font-size: 7.5px; text-transform: uppercase; letter-spacing: 0.5px; color: #999; margin-top: 2px; }

/* Hive block */
.hive-block { margin-bottom: 16px; page-break-inside: avoid; }

.hive-title {
    background: #222;
    color: #fff;
    font-size: 11px;
    font-weight: bold;
    padding: 5px 8px;
}

.hive-sub {
    background: #f0f0f0;
    font-size: 8px;
    color: #777;
    padding: 3px 8px 4px 8px;
    border-bottom: 1px solid #ddd;
}

/* Table */
.htbl { width: 100%; border-collapse: collapse; }
.htbl thead tr { background: #e8e8e8; }
.htbl thead th {
    padding: 4px 7px;
    font-size: 7.5px;
    text-transform: uppercase;
    letter-spacing: 0.4px;
    color: #666;
    font-weight: bold;
    text-align: left;
    border-bottom: 1px solid #ccc;
}
.htbl tbody tr { border-bottom: 1px solid #ececec; }
.htbl tbody tr:last-child { border-bottom: 2px solid #ccc; }
.htbl tbody tr:nth-child(even) { background: #fafafa; }
.htbl tbody td {
    padding: 5px 7px;
    font-size: 9.5px;
    vertical-align: top;
    color: #333;
    line-height: 1.4;
}

.td-num  { width: 22px; color: #aaa; font-size: 8px; }
.td-date { width: 76px; color: #777; white-space: nowrap; }
.td-cont { color: #333; }

.no-hist {
    padding: 8px;
    font-size: 8.5px;
    color: #bbb;
    font-style: italic;
}

.footer {
    margin-top: 16px;
    padding-top: 6px;
    border-top: 1px solid #ddd;
    font-size: 7.5px;
    color: #aaa;
    text-align: right;
}
</style>
</head>
<body>

<div class="header">
    <h1>Hive Inspection History</h1>
    <div class="sub">Generated: {{ $generatedAt }}</div>
</div>

<table class="stats">
    <tr>
        <td>
            <div class="stat-val">{{ $totalInspections }}</div>
            <div class="stat-lbl">Total Inspections</div>
        </td>
        <td>
            <div class="stat-val">{{ $totalHives }}</div>
            <div class="stat-lbl">Hives</div>
        </td>
        <td>
            <div class="stat-val">{{ $firstDate ? \Carbon\Carbon::parse($firstDate)->format('Y-m-d') : '—' }}</div>
            <div class="stat-lbl">First Inspection</div>
        </td>
        <td>
            <div class="stat-val">{{ $lastDate ? \Carbon\Carbon::parse($lastDate)->format('Y-m-d') : '—' }}</div>
            <div class="stat-lbl">Last Inspection</div>
        </td>
    </tr>
</table>

@forelse($posts as $post)
<div class="hive-block">

    <div class="hive-title">
        Hive #{{ $post->no }} &mdash; {{ $post->division }}
    </div>

    <div class="hive-sub">
        Total inspections: {{ $post->histories->count() }}
        @if($post->histories->count())
            &nbsp;|&nbsp; First: {{ \Carbon\Carbon::parse($post->histories->min('inspection_date'))->format('Y-m-d') }}
            &nbsp;|&nbsp; Last: {{ \Carbon\Carbon::parse($post->histories->max('inspection_date'))->format('Y-m-d') }}
        @endif
    </div>

    @if($post->histories->isEmpty())
        <div class="no-hist">No inspection records.</div>
    @else
        <table class="htbl">
            <thead>
                <tr>
                    <th class="td-num">#</th>
                    <th class="td-date">Date</th>
                    <th class="td-cont">Content</th>
                </tr>
            </thead>
            <tbody>
                @foreach($post->histories as $i => $history)
                <tr>
                    <td class="td-num">{{ $i + 1 }}</td>
                    <td class="td-date">{{ \Carbon\Carbon::parse($history->inspection_date)->format('Y-m-d') }}</td>
                    <td class="td-cont">{{ $history->content }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    @endif

</div>
@empty
    <p style="text-align:center;color:#bbb;margin-top:40px;font-style:italic;">No records found.</p>
@endforelse

<div class="footer">Hive Inspection History &mdash; {{ $generatedAt }}</div>

</body>
</html>

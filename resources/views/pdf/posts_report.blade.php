<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<style>
* { margin: 0; padding: 0; box-sizing: border-box; }
body { font-family: 'DejaVu Sans', sans-serif; font-size: 9px; color: #222; }

.division-label {
    text-align: center;
    font-size: 10px;
    letter-spacing: 2px;
    text-transform: uppercase;
    color: #aaa;
    margin-bottom: 4px;
}

/* Glavna tabela */
.layout {
    width: 100%;
    border-collapse: collapse;
}

/* THEAD - ponavlja se na svakoj stranici u DomPDF */
.layout thead td {
    font-size: 11px;
    font-weight: bold;
    font-style: italic;
    text-decoration: underline;
    text-align: center;
    padding: 0 0 6px 0;
    color: #111;
}
.layout thead .sep {
    border-left: 1px solid #bbb;
    width: 4%;
    padding: 0;
}

/* TBODY redovi */
.layout tbody tr td {
    vertical-align: top;
    padding: 1px 3px 1px 3px;
    width: 48%;
}
.layout tbody tr .sep {
    border-left: 1px solid #bbb;
    width: 4%;
    padding: 0;
}
.layout tbody tr {
    border-bottom: 1px solid #e8e8e8;
}

/* Post glavni red */
.post-main { width: 100%; border-collapse: collapse; }
.post-main td {
    padding: 1px 2px;
    vertical-align: top;
    font-size: 9px;
    line-height: 1.3;
}
.td-id   { width: 20px; font-weight: bold; color: #222; }
.td-div  { width: 38px; color: #777; }
.td-cont { color: #333; }
.td-date {
    width: 62px;
    color: #999;
    white-space: nowrap;
    font-size: 8px;
    vertical-align: top;
}

/* Istorije */
.histories-wrap { padding-left: 20px; }
.history-tbl { width: 100%; border-collapse: collapse; }
.history-tbl td {
    padding: 0px 2px;
    font-size: 8.5px;
    color: #aaa;
    font-style: italic;
    border-top: 1px dotted #ececec;
    vertical-align: top;
    line-height: 1.25;
}
.h-date {
    width: 62px;
    white-space: nowrap;
    vertical-align: top;
}

.empty { font-size: 9px; color: #bbb; font-style: italic; text-align: center; padding: 8px; }
</style>
</head>
<body>

<div class="division-label">DIVISION</div>

@php
    $maxRows = max($postsA->count(), $postsB->count());
@endphp

<table class="layout">
    <thead>
        <tr>
            <td>{{ $divisionA ?: '—' }}</td>
            <td class="sep"></td>
            <td>{{ $divisionB ?: '—' }}</td>
        </tr>
    </thead>
    <tbody>
        @for($i = 0; $i < $maxRows; $i++)
        @php
            $postA = $postsA->get($i);
            $postB = $postsB->get($i);
        @endphp
        <tr>
            {{-- LEVA CELIJA --}}
            <td>
                @if($postA)
                    <table class="post-main">
                        <tr>
                            <td class="td-id">{{ $postA->no }}</td>
                            <td class="td-div">{{ $postA->division }}</td>
                            <td class="td-cont">{{ \Illuminate\Support\Str::limit($postA->histories->first()->content ?? '', 75) }}</td>
                            <td class="td-date">{{ $postA->histories->first() ? \Carbon\Carbon::parse($postA->histories->first()->inspection_date)->format('Y-m-d') : '—' }}</td>
                        </tr>
                    </table>
                    @if($postA->histories->count() > 1)
                    <div class="histories-wrap">
                        @foreach($postA->histories->skip(1) as $history)
                        <table class="history-tbl">
                            <tr>
                                <td>{{ \Illuminate\Support\Str::limit($history->content, 75) }}</td>
                                <td class="h-date" style="vertical-align: top; white-space: nowrap;">
                                    {{ \Carbon\Carbon::parse($history->inspection_date)->format('Y-m-d') }}
                                </td>
                            </tr>
                        </table>
                        @endforeach
                    </div>
                    @endif
                @endif
            </td>

            {{-- SEPARATOR --}}
            <td class="sep"></td>

            {{-- DESNA CELIJA --}}
            <td>
                @if($postB)
                    <table class="post-main">
                        <tr>
                            <td class="td-id">{{ $postB->no }}</td>
                            <td class="td-div">{{ $postB->division }}</td>
                            <td class="td-cont">{{ \Illuminate\Support\Str::limit($postB->histories->first()->content ?? '', 75) }}</td>
                            <td class="td-date">{{ $postB->histories->first() ? \Carbon\Carbon::parse($postB->histories->first()->inspection_date)->format('Y-m-d') : '—' }}</td>
                        </tr>
                    </table>
                    @if($postB->histories->count() > 1)
                    <div class="histories-wrap">
                        @foreach($postB->histories->skip(1) as $history)
                        <table class="history-tbl">
                            <tr>
                                <td>{{ \Illuminate\Support\Str::limit($history->content, 75) }}</td>
                                <td class="h-date">{{ \Carbon\Carbon::parse($history->inspection_date)->format('Y-m-d') }}</td>
                            </tr>
                        </table>
                        @endforeach
                    </div>
                    @endif
                @endif
            </td>
        </tr>
        @endfor
    </tbody>
</table>

</body>
</html>

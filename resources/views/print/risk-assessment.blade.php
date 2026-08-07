<!doctype html>
<html lang="de">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Gefährdungsbeurteilung — {{ $a->title }}</title>
    <style>
        * { box-sizing: border-box; }
        body { font-family: -apple-system, Segoe UI, Roboto, Helvetica, Arial, sans-serif; color: #111; margin: 32px; font-size: 12px; }
        h1 { font-size: 18px; margin: 0 0 2px; }
        .muted { color: #666; }
        .meta { margin: 8px 0 16px; }
        .meta span { margin-right: 16px; }
        .badge { display: inline-block; padding: 1px 6px; border: 1px solid #ccc; border-radius: 4px; font-size: 11px; }
        table { width: 100%; border-collapse: collapse; margin-top: 8px; }
        th, td { border: 1px solid #ccc; padding: 6px 8px; text-align: left; vertical-align: top; }
        th { background: #f3f4f6; font-size: 11px; text-transform: uppercase; letter-spacing: .03em; }
        .dot { display: inline-block; width: 9px; height: 9px; border-radius: 50%; margin-right: 4px; vertical-align: middle; }
        .foot { margin-top: 24px; color: #666; font-size: 10px; }
        .legal { margin-top: 4px; }
        @media print { body { margin: 12mm; } .noprint { display: none; } }
    </style>
</head>
<body onload="window.print()">
    <div class="noprint" style="text-align:right; margin-bottom:12px;">
        <button onclick="window.print()">Drucken / Als PDF speichern</button>
    </div>

    <h1>Gefährdungsbeurteilung</h1>
    <div class="muted">{{ $a->title }}@if($a->work_area) · {{ $a->work_area }}@endif</div>

    <div class="meta">
        <span><strong>Betrieb:</strong> {{ $a->organizationEntity?->name ?? '—' }}</span>
        <span><strong>Stand:</strong> {{ optional($a->assessed_on)->format('d.m.Y') ?? '—' }}</span>
        <span><strong>Version:</strong> {{ $a->version ?? 1 }}</span>
        <span><strong>Status:</strong> {{ $a->status?->label() }}</span>
        @if($a->closed_at)<span class="badge">Abgeschlossen {{ $a->closed_at->format('d.m.Y') }} · revisionssicher</span>@endif
    </div>

    <table>
        <thead>
            <tr>
                <th>Faktor</th>
                <th>Gefährdung</th>
                <th>Risiko</th>
                <th>Maßnahme (STOP)</th>
                <th>Verantwortl.</th>
                <th>Frist</th>
                <th>Status</th>
                <th>Vorsorge</th>
            </tr>
        </thead>
        <tbody>
            @forelse($a->hazards as $h)
                @php $meta = $h->riskMeta(); @endphp
                <tr>
                    <td>{{ $h->category?->label() }}</td>
                    <td>{{ $h->description }}</td>
                    <td>@if($meta)<span class="dot" style="background: {{ $meta['color'] }}"></span>{{ $meta['label'] }}@if($meta['rpz']) (RPZ {{ $meta['rpz'] }})@endif @else — @endif</td>
                    <td>@if($h->measure_type){{ $h->measure_type->short() }} · @endif{{ $h->measures ?? '—' }}</td>
                    <td>{{ $h->responsible ?? '—' }}</td>
                    <td>{{ optional($h->deadline)->format('d.m.Y') ?? '—' }}</td>
                    <td>{{ $h->status?->label() }}</td>
                    <td>@if($h->catalog){{ $h->catalog->title }}@if($h->careTypeLabel()) ({{ $h->careTypeLabel() }})@endif @else — @endif</td>
                </tr>
            @empty
                <tr><td colspan="8">Keine Gefährdungen erfasst.</td></tr>
            @endforelse
        </tbody>
    </table>

    <div class="foot">
        <div class="legal">Dokumentation nach § 6 ArbSchG. Verantwortliche Stelle: Arbeitgeber ({{ $a->organizationEntity?->name ?? '—' }}). Erstellt/beraten durch die Praxis.</div>
        <div>Gedruckt am {{ now()->format('d.m.Y H:i') }}.</div>
    </div>
</body>
</html>

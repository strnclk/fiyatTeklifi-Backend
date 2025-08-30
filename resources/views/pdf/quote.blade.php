<!doctype html>
<html lang="tr">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Teklif #{{ $quote->id }}</title>
  <style>
    @page { margin: 24mm 18mm; }
    body { font-family: DejaVu Sans, Arial, Helvetica, sans-serif; color: #222; font-size: 12px; }
    .header { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 16px; }
    .title { font-size: 20px; font-weight: 700; }
    .muted { color: #666; }
    .meta { margin-top: 4px; }
    .section { margin-top: 18px; }
    table { width: 100%; border-collapse: collapse; }
    th, td { border: 1px solid #ddd; padding: 8px; }
    th { background: #f5f5f5; text-align: left; }
    .text-right { text-align: right; }
    .totals { width: 40%; margin-left: auto; margin-top: 12px; }
    .footer { position: fixed; bottom: -10mm; left: 0; right: 0; text-align: center; font-size: 10px; color: #888; }
  </style>
</head>
<body>
  @php
    $logoFile = public_path('logo.png');
    $logoBase64 = file_exists($logoFile) ? ('data:image/png;base64,' . base64_encode(file_get_contents($logoFile))) : null;
    $companyName = env('COMPANY_NAME', config('app.name'));
    $companyAddress = env('COMPANY_ADDRESS');
    $companyPhone = env('COMPANY_PHONE');
    $companyEmail = env('COMPANY_EMAIL');
    $companyWebsite = env('COMPANY_WEBSITE', config('app.url'));
  @endphp

  <div class="header">
    <div style="display:flex; align-items:center; gap:10px;">
      @if($logoBase64)
        <img src="{{ $logoBase64 }}" alt="Logo" style="height:48px;">
      @endif
      <div>
        <div class="title">Teklif</div>
        <div class="meta">Teklif No: {{ $quote->quote_number ?? ('T-' . str_pad($quote->id, 6, '0', STR_PAD_LEFT)) }}</div>
        <div class="meta">Tarih: {{ optional($quote->created_at)->format('d.m.Y') }}</div>
        @if($quote->valid_until)
          <div class="meta">Geçerlilik: {{ optional($quote->valid_until)->format('d.m.Y') }}</div>
        @endif
      </div>
    </div>
    <div style="text-align:right">
      <div style="font-weight:600">{{ $companyName }}</div>
      @if($companyAddress)
        <div class="muted">{{ $companyAddress }}</div>
      @endif
      @if($companyPhone)
        <div class="muted">Tel: {{ $companyPhone }}</div>
      @endif
      @if($companyEmail)
        <div class="muted">E-posta: {{ $companyEmail }}</div>
      @endif
      @if($companyWebsite)
        <div class="muted">{{ $companyWebsite }}</div>
      @endif
    </div>
  </div>

  <div class="section">
    <strong>Müşteri</strong>
    <div>{{ optional($quote->customer)->name }}</div>
    @if(optional($quote->customer)->email)
      <div class="muted">{{ $quote->customer->email }}</div>
    @endif
    @if(optional($quote->customer)->phone)
      <div class="muted">{{ $quote->customer->phone }}</div>
    @endif
  </div>

  <div class="section">
    <strong>Başlık</strong>
    <div>{{ $quote->title }}</div>
    @if($quote->description)
      <div class="muted" style="margin-top:6px; white-space:pre-line">{{ $quote->description }}</div>
    @endif
  </div>

  <div class="section">
    <table>
      <thead>
        <tr>
          <th style="width:5%">#</th>
          <th>Hizmet</th>
          <th style="width:12%" class="text-right">Miktar</th>
          <th style="width:12%" class="text-right">Birim</th>
          <th style="width:16%" class="text-right">Birim Fiyat (₺)</th>
          <th style="width:18%" class="text-right">Tutar (₺)</th>
        </tr>
      </thead>
      <tbody>
        @php($i = 1)
        @forelse($quote->items as $item)
          <tr>
            <td>{{ $i++ }}</td>
            <td>{{ $item->name }}</td>
            <td class="text-right">{{ number_format($item->quantity ?? 1, 2, ',', '.') }}</td>
            <td class="text-right">{{ $item->unit ?? '-' }}</td>
            <td class="text-right">{{ number_format($item->unit_price ?? 0, 2, ',', '.') }}</td>
            <td class="text-right">{{ number_format(($item->quantity ?? 1) * ($item->unit_price ?? 0), 2, ',', '.') }}</td>
          </tr>
        @empty
          <tr>
            <td colspan="6" class="muted">Kalem bulunmuyor.</td>
          </tr>
        @endforelse
      </tbody>
    </table>

    @php
      $subtotal = $quote->subtotal ?? $quote->items->sum(function($it){ return ($it->quantity ?? 1) * ($it->unit_price ?? 0); });
      $taxRate = $quote->tax_rate ?? 0;
      $taxAmount = $quote->tax_amount ?? ($subtotal * ($taxRate/100));
      $total = $quote->total_amount ?? ($subtotal + $taxAmount);
    @endphp

    <table class="totals">
      <tbody>
        <tr>
          <th>Ara Toplam</th>
          <td class="text-right">₺{{ number_format($subtotal, 2, ',', '.') }}</td>
        </tr>
        <tr>
          <th>KDV ({{ number_format($taxRate, 2, ',', '.') }}%)</th>
          <td class="text-right">₺{{ number_format($taxAmount, 2, ',', '.') }}</td>
        </tr>
        <tr>
          <th>Genel Toplam</th>
          <td class="text-right"><strong>₺{{ number_format($total, 2, ',', '.') }}</strong></td>
        </tr>
      </tbody>
    </table>
  </div>

  @if($quote->notes)
    <div class="section">
      <strong>Notlar</strong>
      <div class="muted" style="white-space:pre-line">{{ $quote->notes }}</div>
    </div>
  @endif

  <div class="section" style="margin-top:40px;">
    <table style="width:100%; border:none;">
      <tr>
        <td style="width:50%; border:none; vertical-align:top;">
          <div><strong>Yetkili</strong></div>
          <div class="muted">Ad Soyad / İmza</div>
          <div style="height:60px; border:1px dashed #ccc; margin-top:8px;"></div>
        </td>
        <td style="width:50%; border:none; vertical-align:top; text-align:right;">
          <div><strong>Müşteri</strong></div>
          <div class="muted">Ad Soyad / Kaşe & İmza</div>
          <div style="height:60px; border:1px dashed #ccc; margin-top:8px;"></div>
        </td>
      </tr>
    </table>
  </div>

  <div class="footer">Bu teklif {{ config('app.name') }} tarafından oluşturulmuştur.</div>
</body>
</html>

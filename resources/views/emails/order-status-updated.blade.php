<!doctype html>
<html lang="id">
  <body style="margin:0;padding:24px;background:#f7f2e9;font-family:Arial,sans-serif;color:#201916;">
    <div style="max-width:640px;margin:0 auto;background:#ffffff;border:1px solid #e5d8cb;border-radius:8px;padding:28px;">
      <p style="margin:0 0 8px;font-size:12px;letter-spacing:0.18em;text-transform:uppercase;color:#83513d;font-weight:700;">Auraquina</p>
      <h1 style="margin:0 0 14px;font-size:28px;line-height:1.2;">Status pesanan Anda diperbarui</h1>
      <p style="margin:0 0 18px;font-size:14px;line-height:1.7;color:#6d635a;">Pesanan <strong>#{{ $pesanan->kode_pesanan }}</strong> sekarang berstatus <strong>{{ ucfirst(str_replace('_', ' ', $pesanan->status)) }}</strong>.</p>

      @if ($pesanan->after_sales_status)
        <p style="margin:0 0 18px;font-size:14px;line-height:1.7;color:#6d635a;">Status after-sales saat ini: <strong>{{ ucfirst(str_replace('_', ' ', $pesanan->after_sales_status)) }}</strong>.</p>
      @endif

      <p style="margin:0 0 10px;"><a href="{{ $orderUrl }}" style="display:inline-block;background:#83513d;color:#ffffff;text-decoration:none;padding:12px 18px;border-radius:6px;font-size:13px;font-weight:700;">Lihat Detail Pesanan</a></p>
      <p style="margin:0 0 22px;"><a href="{{ $invoiceUrl }}" style="display:inline-block;color:#83513d;text-decoration:underline;font-size:13px;font-weight:700;">Buka Invoice Digital</a></p>

      <p style="margin:0;font-size:13px;line-height:1.7;color:#6d635a;">Terima kasih telah berbelanja di Auraquina.</p>
    </div>
  </body>
</html>

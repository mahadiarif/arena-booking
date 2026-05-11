<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Invoice {{ $booking->booking_ref }}</title>
<style>
* { margin:0; padding:0; box-sizing:border-box; }
body { font-family: DejaVu Sans, Arial, sans-serif; font-size:12px; color:#1e293b; background:#fff; padding:30px; }
.header { display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:30px; padding-bottom:20px; border-bottom:2px solid #e2e8f0; }
.logo { font-size:22px; font-weight:800; color:#1e3a8a; }
.logo span { color:#3b82f6; }
.company-info { font-size:10px; color:#64748b; margin-top:4px; line-height:1.6; }
.invoice-meta { text-align:right; }
.invoice-title { font-size:20px; font-weight:700; color:#1e293b; }
.invoice-meta p { font-size:11px; color:#64748b; margin-top:2px; }
.section { margin-bottom:22px; }
.section-title { font-size:10px; font-weight:700; color:#64748b; text-transform:uppercase; letter-spacing:.08em; margin-bottom:8px; padding-bottom:4px; border-bottom:1px solid #f1f5f9; }
.info-box { background:#f8fafc; border:1px solid #e2e8f0; border-radius:6px; padding:12px; }
.info-box p { margin-bottom:3px; color:#334155; }
.info-box .label { color:#94a3b8; font-size:10px; display:inline-block; width:80px; }
table { width:100%; border-collapse:collapse; }
th { background:#1e3a8a; color:#fff; text-align:left; padding:8px 10px; font-size:10px; font-weight:700; text-transform:uppercase; letter-spacing:.05em; }
th:last-child, td:last-child { text-align:right; }
td { padding:8px 10px; border-bottom:1px solid #f1f5f9; color:#334155; }
tr:last-child td { border-bottom:none; }
tr:nth-child(even) td { background:#f8fafc; }
.summary-table { width:280px; margin-left:auto; }
.summary-table td { padding:5px 10px; border:none; }
.summary-table .label { color:#64748b; font-size:11px; }
.summary-table .total-row td { font-size:14px; font-weight:800; color:#1e293b; border-top:2px solid #e2e8f0; padding-top:8px; }
.due-row td { color:#dc2626; font-weight:800; background:#fef2f2; border-radius:4px; }
.badge { display:inline-block; padding:2px 8px; border-radius:20px; font-size:10px; font-weight:700; }
.badge-pending { background:#fef3c7; color:#92400e; }
.badge-confirmed { background:#dbeafe; color:#1e40af; }
.badge-completed { background:#dcfce7; color:#166534; }
.badge-cancelled { background:#fee2e2; color:#991b1b; }
.footer { margin-top:30px; padding-top:15px; border-top:1px solid #e2e8f0; text-align:center; color:#94a3b8; font-size:10px; }
.divider { border:none; border-top:1px solid #e2e8f0; margin:15px 0; }
</style>
</head>
<body>

{{-- Header --}}
<div class="header">
  <div>
    <div class="logo">Metro Arena<span>Book</span></div>
    <div class="company-info">
      {{ config('arenabook.address', 'Dhaka, Bangladesh') }}<br>
      {{ config('arenabook.phone', '') }}<br>
      {{ config('arenabook.email', '') }}
    </div>
  </div>
  <div class="invoice-meta">
    <div class="invoice-title">INVOICE</div>
    <p><strong>#{{ $booking->booking_ref }}</strong></p>
    <p>Date: {{ now()->format('d M Y') }}</p>
    <p>Status: <span class="badge badge-{{ $booking->status->value }}">{{ $booking->status->label() }}</span></p>
  </div>
</div>

{{-- Customer Info --}}
<div class="section">
  <div class="section-title">Bill To</div>
  <div class="info-box">
    <p><strong>{{ $booking->customer?->name }}</strong></p>
    <p><span class="label">Phone:</span> {{ $booking->customer?->phone }}</p>
    @if($booking->customer?->email)<p><span class="label">Email:</span> {{ $booking->customer->email }}</p>@endif
    @if($booking->customer?->organization)<p><span class="label">Org:</span> {{ $booking->customer->organization }}</p>@endif
  </div>
</div>

{{-- Booking Details --}}
<div class="section">
  <div class="section-title">Booking Details</div>
  <table>
    <thead><tr>
      <th>Venue</th><th>Date</th><th>Time</th><th>Duration</th><th>Rate</th><th>Amount</th>
    </tr></thead>
    <tbody>
      <tr>
        <td>{{ $booking->venue?->name }}</td>
        <td>{{ $booking->slot?->date?->format('d M Y, l') }}</td>
        <td>{{ $booking->slot ? \Carbon\Carbon::createFromTimeString($booking->slot->start_time)->format('g:i A').' – '.\Carbon\Carbon::createFromTimeString($booking->slot->end_time)->format('g:i A') : '—' }}</td>
        <td>{{ $booking->slot?->duration_minutes ?? '—' }} min</td>
        <td>৳{{ number_format($booking->venue?->hourly_rate ?? 0, 0) }}/hr</td>
        <td><strong>৳{{ number_format($booking->total_amount, 2) }}</strong></td>
      </tr>
    </tbody>
  </table>
</div>

{{-- Payments --}}
@if($booking->payments->count())
<div class="section">
  <div class="section-title">Payment History</div>
  <table>
    <thead><tr><th>Date</th><th>Method</th><th>Reference</th><th>Received By</th><th>Amount</th></tr></thead>
    <tbody>
      @foreach($booking->payments as $payment)
      <tr>
        <td>{{ $payment->paid_at?->format('d M Y') }}</td>
        <td>{{ $payment->method->label() }}</td>
        <td>{{ $payment->reference_no ?? '—' }}</td>
        <td>{{ $payment->receivedBy?->name ?? '—' }}</td>
        <td>৳{{ number_format($payment->amount, 2) }}</td>
      </tr>
      @endforeach
    </tbody>
  </table>
</div>
@endif

{{-- Summary --}}
<div class="section">
  <table class="summary-table">
    <tr>
        <td class="label">Subtotal</td>
        <td align="right">৳{{ number_format($booking->total_amount, 2) }}</td>
    </tr>
    <tr>
        <td class="label">Total Amount</td>
        <td align="right" style="font-weight: 700;">৳{{ number_format($booking->total_amount, 2) }}</td>
    </tr>
    <tr>
        <td class="label">Amount Paid</td>
        <td align="right" style="color:#16a34a; font-weight:600">৳{{ number_format($booking->paid_amount, 2) }}</td>
    </tr>
    <tr class="total-row {{ $booking->due_amount > 0 ? 'due-row' : '' }}">
        <td>Balance Due</td>
        <td align="right" style="{{ $booking->due_amount <= 0 ? 'color:#16a34a;' : '' }}">
            ৳{{ number_format($booking->due_amount, 2) }}
        </td>
    </tr>
  </table>
</div>

<div class="footer">
  <p>Thank you for choosing Metro ArenaBook! For queries contact us at {{ config('arenabook.phone','') }} · {{ config('arenabook.email','') }}</p>
  <p style="margin-top:4px">This is a computer-generated invoice. No signature required. | &copy; {{ date('Y') }} MetroNet Bangladesh Ltd.</p>
</div>

</body>
</html>

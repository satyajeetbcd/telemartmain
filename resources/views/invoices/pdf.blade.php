<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Invoice - INV-{{ $appointment->appointment_number }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 12px;
            color: #222;
            line-height: 1.5;
        }
        .page {
            padding: 30px;
        }

        /* Header */
        .header {
            border-bottom: 3px solid #1a5276;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }
        .header-table {
            width: 100%;
        }
        .header-table td {
            vertical-align: top;
        }
        .company-name {
            font-size: 20px;
            font-weight: bold;
            color: #1a5276;
        }
        .company-sub {
            font-size: 10px;
            color: #666;
            margin-top: 2px;
        }
        .invoice-title {
            font-size: 28px;
            font-weight: bold;
            color: #333;
            text-align: right;
            letter-spacing: 2px;
        }
        .invoice-meta {
            text-align: right;
            font-size: 11px;
            color: #555;
            margin-top: 5px;
        }
        .invoice-meta strong {
            color: #333;
        }
        .status-badge {
            display: inline-block;
            padding: 3px 12px;
            font-size: 10px;
            font-weight: bold;
            border-radius: 10px;
            margin-top: 5px;
        }
        .status-paid {
            background-color: #d4edda;
            color: #155724;
        }
        .status-pending {
            background-color: #fff3cd;
            color: #856404;
        }

        /* Bill To / Service By */
        .info-grid {
            width: 100%;
            margin-bottom: 20px;
        }
        .info-grid td {
            width: 50%;
            vertical-align: top;
            padding: 0;
        }
        .info-box {
            background-color: #f8f9fa;
            border-radius: 6px;
            padding: 12px 15px;
            margin-right: 8px;
        }
        .info-box-right {
            background-color: #f8f9fa;
            border-radius: 6px;
            padding: 12px 15px;
            margin-left: 8px;
        }
        .info-label {
            font-size: 9px;
            font-weight: bold;
            color: #999;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 8px;
        }
        .info-name {
            font-size: 13px;
            font-weight: bold;
            color: #222;
        }
        .info-detail {
            font-size: 10px;
            color: #555;
            margin-top: 2px;
        }

        /* Appointment Details */
        .section-label {
            font-size: 9px;
            font-weight: bold;
            color: #999;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 8px;
        }
        .details-grid {
            width: 100%;
            background-color: #f8f9fa;
            border-radius: 6px;
            padding: 12px 15px;
            margin-bottom: 20px;
        }
        .details-grid td {
            padding: 4px 10px 4px 0;
            font-size: 11px;
            width: 50%;
        }
        .detail-label {
            color: #888;
        }
        .detail-value {
            font-weight: 600;
            color: #222;
        }

        /* Services Table */
        .services-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .services-table thead th {
            background-color: #2d3748;
            color: #fff;
            padding: 10px 12px;
            font-size: 11px;
            font-weight: 600;
            text-align: left;
        }
        .services-table thead th:last-child {
            text-align: right;
        }
        .services-table tbody td {
            padding: 10px 12px;
            border-bottom: 1px solid #e2e8f0;
            font-size: 11px;
        }
        .services-table tbody td:last-child {
            text-align: right;
            font-weight: bold;
        }
        .service-name {
            font-weight: 600;
            color: #222;
        }
        .service-sub {
            font-size: 9px;
            color: #888;
            margin-top: 2px;
        }

        /* Totals */
        .totals-wrapper {
            width: 100%;
            margin-bottom: 25px;
        }
        .totals-wrapper td {
            vertical-align: top;
        }
        .totals-box {
            width: 220px;
            float: right;
        }
        .total-row {
            display: block;
            padding: 6px 0;
            border-bottom: 1px solid #e2e8f0;
            font-size: 11px;
        }
        .total-row-final {
            display: block;
            padding: 8px 0;
            border-bottom: 2px solid #333;
            font-size: 14px;
            font-weight: bold;
        }
        .total-label {
            color: #666;
        }
        .total-value {
            float: right;
            font-weight: 600;
            color: #222;
        }
        .total-value-green {
            float: right;
            font-weight: bold;
            color: #1a5276;
        }
        .payment-row {
            display: block;
            padding: 6px 0;
            font-size: 11px;
        }

        /* Footer */
        .footer {
            border-top: 1px solid #e2e8f0;
            padding-top: 15px;
            text-align: center;
        }
        .footer p {
            font-size: 10px;
            color: #888;
            margin-bottom: 3px;
        }
    </style>
</head>
<body>
    <div class="page">

        {{-- Header --}}
        <div class="header">
            <table class="header-table">
                <tr>
                    <td style="width: 50%;">
                        <div class="company-name">Tele Health Mart</div>
                        <div class="company-sub">Telemedicine Platform</div>
                    </td>
                    <td style="width: 50%;">
                        <div class="invoice-title">INVOICE</div>
                        <div class="invoice-meta">
                            <strong>Invoice #:</strong> INV-{{ $appointment->appointment_number }}<br>
                            <strong>Date:</strong> {{ now()->format('F d, Y') }}
                        </div>
                        <div style="text-align: right; margin-top: 6px;">
                            <span class="status-badge {{ $appointment->payment_status === 'paid' ? 'status-paid' : 'status-pending' }}">
                                {{ $appointment->payment_status === 'paid' ? 'PAID' : 'PAYMENT PENDING' }}
                            </span>
                        </div>
                    </td>
                </tr>
            </table>
        </div>

        {{-- Bill To / Service By --}}
        <table class="info-grid">
            <tr>
                <td>
                    <div class="info-box">
                        <div class="info-label">Bill To</div>
                        <div class="info-name">{{ $appointment->patient->full_name }}</div>
                        <div class="info-detail">Patient ID: {{ $appointment->patient->patient_id }}</div>
                        @if($appointment->patient->email)
                            <div class="info-detail">{{ $appointment->patient->email }}</div>
                        @endif
                        @if($appointment->patient->phone)
                            <div class="info-detail">{{ $appointment->patient->phone }}</div>
                        @endif
                        @if($appointment->patient->address)
                            <div class="info-detail">{{ $appointment->patient->address }}</div>
                        @endif
                        @php
                            $location = array_filter([
                                $appointment->patient->city,
                                $appointment->patient->state,
                                $appointment->patient->postal_code,
                            ]);
                        @endphp
                        @if($location)
                            <div class="info-detail">{{ implode(', ', $location) }}</div>
                        @endif
                    </div>
                </td>
                <td>
                    <div class="info-box-right">
                        <div class="info-label">Service By</div>
                        <div class="info-name">Dr. {{ $appointment->doctor->name }}</div>
                        @if($appointment->doctor->specialization)
                            <div class="info-detail">{{ $appointment->doctor->specialization }}</div>
                        @endif
                        @if($appointment->doctor->qualifications)
                            <div class="info-detail">{{ $appointment->doctor->qualifications }}</div>
                        @endif
                        @if($appointment->doctor->license_number)
                            <div class="info-detail">License: {{ $appointment->doctor->license_number }}</div>
                        @endif
                        @if($appointment->doctor->phone)
                            <div class="info-detail">{{ $appointment->doctor->phone }}</div>
                        @endif
                    </div>
                </td>
            </tr>
        </table>

        {{-- Appointment Details --}}
        <div class="section-label">Appointment Details</div>
        <table class="details-grid">
            <tr>
                <td>
                    <span class="detail-label">Appointment #:</span>
                    <span class="detail-value">{{ $appointment->appointment_number }}</span>
                </td>
                <td>
                    <span class="detail-label">Date:</span>
                    <span class="detail-value">{{ $appointment->appointment_date->format('F d, Y') }}</span>
                </td>
            </tr>
            <tr>
                <td>
                    <span class="detail-label">Time:</span>
                    <span class="detail-value">{{ date('h:i A', strtotime($appointment->appointment_time)) }}</span>
                </td>
                <td>
                    <span class="detail-label">Status:</span>
                    <span class="detail-value">{{ ucfirst($appointment->status) }}</span>
                </td>
            </tr>
            @if($appointment->reason)
            <tr>
                <td colspan="2">
                    <span class="detail-label">Reason for visit:</span>
                    <span class="detail-value">{{ $appointment->reason }}</span>
                </td>
            </tr>
            @endif
        </table>

        {{-- Services Table --}}
        <div class="section-label">Services</div>
        <table class="services-table">
            <thead>
                <tr>
                    <th style="width: 30px;">#</th>
                    <th>Description</th>
                    <th>Date of Service</th>
                    <th style="text-align: right;">Amount</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>1</td>
                    <td>
                        <div class="service-name">Online Medical Consultation</div>
                        <div class="service-sub">Dr. {{ $appointment->doctor->name }} — {{ $appointment->doctor->specialization ?? 'General Practitioner' }}</div>
                    </td>
                    <td>{{ $appointment->appointment_date->format('M d, Y') }}</td>
                    <td style="font-weight: bold;">₹{{ number_format($appointment->consultation_fee ?? 0, 2) }}</td>
                </tr>
            </tbody>
        </table>

        {{-- Totals --}}
        <table style="width: 100%; margin-bottom: 25px;">
            <tr>
                <td style="width: 60%;"></td>
                <td style="width: 40%;">
                    <div class="total-row">
                        <span class="total-label">Subtotal</span>
                        <span class="total-value">₹{{ number_format($appointment->consultation_fee ?? 0, 2) }}</span>
                        <div style="clear: both;"></div>
                    </div>
                    <div class="total-row-final">
                        <span>Total</span>
                        <span class="total-value-green">₹{{ number_format($appointment->consultation_fee ?? 0, 2) }}</span>
                        <div style="clear: both;"></div>
                    </div>
                    <div class="payment-row">
                        <span class="total-label">Payment Status</span>
                        <span style="float: right; font-weight: 600; color: {{ $appointment->payment_status === 'paid' ? '#155724' : '#856404' }};">
                            {{ $appointment->payment_status === 'paid' ? 'Paid' : 'Pending' }}
                        </span>
                        <div style="clear: both;"></div>
                    </div>
                </td>
            </tr>
        </table>

        {{-- Footer --}}
        <div class="footer">
            <p>Thank you for choosing Tele Health Mart for your healthcare needs.</p>
            <p>This is a computer-generated invoice. No signature required.</p>
            <p>Generated on {{ now()->format('F d, Y \a\t h:i A') }}</p>
        </div>
    </div>
</body>
</html>

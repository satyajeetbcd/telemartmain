<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Prescription - {{ $prescription->prescription_number }}</title>
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
            padding: 20px 30px;
            border: 2px solid #333;
            min-height: 95vh;
            position: relative;
        }

        /* Header - Doctor Info */
        .doctor-header {
            text-align: center;
            border-bottom: 2px solid #333;
            padding-bottom: 12px;
            margin-bottom: 12px;
        }
        .doctor-name {
            font-size: 18px;
            font-weight: bold;
            text-transform: uppercase;
            color: #1a5276;
        }
        .doctor-qualification {
            font-size: 11px;
            color: #555;
            margin-top: 2px;
        }
        .doctor-reg {
            font-size: 11px;
            color: #555;
        }
        .doctor-address {
            font-size: 10px;
            color: #666;
            margin-top: 2px;
        }
        .doctor-contact {
            font-size: 10px;
            color: #666;
        }

        /* Patient Info Row */
        .info-grid {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }
        .info-grid td {
            padding: 4px 6px;
            font-size: 11px;
            vertical-align: top;
        }
        .info-label {
            font-weight: bold;
            color: #444;
            white-space: nowrap;
            width: 130px;
        }
        .info-value {
            border-bottom: 1px dotted #999;
            min-width: 120px;
        }

        /* Two Column Layout */
        .two-col {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 8px;
        }
        .two-col td {
            width: 50%;
            vertical-align: top;
            padding: 0;
        }

        /* Section Headers */
        .section-header {
            font-size: 11px;
            font-weight: bold;
            text-transform: uppercase;
            color: #1a5276;
            border-bottom: 1px solid #1a5276;
            padding-bottom: 3px;
            margin-bottom: 6px;
            margin-top: 10px;
        }
        .section-content {
            font-size: 11px;
            color: #333;
            min-height: 40px;
            padding: 4px 0;
        }

        /* Main Content - Two Column */
        .main-content {
            width: 100%;
            border-collapse: collapse;
        }
        .main-content td {
            vertical-align: top;
            padding: 0;
        }
        .left-col {
            width: 45%;
            padding-right: 15px;
            border-right: 1px solid #ccc;
        }
        .right-col {
            width: 55%;
            padding-left: 15px;
        }

        /* Rx Symbol */
        .rx-symbol {
            font-size: 22px;
            font-weight: bold;
            color: #1a5276;
            margin-bottom: 8px;
            display: block;
        }

        /* Medicine List */
        .medicine-item {
            margin-bottom: 10px;
            padding-bottom: 6px;
            border-bottom: 1px dotted #ddd;
        }
        .medicine-name {
            font-size: 12px;
            font-weight: bold;
            text-transform: uppercase;
        }
        .medicine-details {
            font-size: 10px;
            color: #555;
            margin-top: 2px;
        }

        /* Footer */
        .footer-section {
            margin-top: 15px;
            padding-top: 8px;
            border-top: 1px solid #ccc;
        }
        .special-instructions {
            font-size: 11px;
            min-height: 30px;
        }
        .signature-area {
            text-align: right;
            margin-top: 30px;
            padding-top: 5px;
        }
        .signature-line {
            display: inline-block;
            width: 200px;
            border-top: 1px solid #333;
            padding-top: 4px;
            text-align: center;
            font-size: 11px;
        }
        .teleconsultation-note {
            font-size: 9px;
            color: #888;
            font-style: italic;
            margin-top: 15px;
            text-align: left;
        }
        .platform-name {
            font-size: 10px;
            color: #1a5276;
            text-align: center;
            margin-top: 8px;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="page">
        {{-- Doctor Header --}}
        <div class="doctor-header">
            <div class="doctor-name">Dr. {{ $prescription->doctor->name }}</div>
            @if($prescription->doctor->qualifications)
                <div class="doctor-qualification">{{ $prescription->doctor->qualifications }}</div>
            @endif
            @if($prescription->doctor->specialization)
                <div class="doctor-qualification">{{ $prescription->doctor->specialization }}</div>
            @endif
            @if($prescription->doctor->license_number)
                <div class="doctor-reg">Registration No: {{ $prescription->doctor->license_number }}</div>
            @endif
            @if($prescription->doctor->address)
                <div class="doctor-address">{{ $prescription->doctor->address }}</div>
            @endif
            <div class="doctor-contact">
                @if($prescription->doctor->email)Email: {{ $prescription->doctor->email }}@endif
                @if($prescription->doctor->email && $prescription->doctor->phone) | @endif
                @if($prescription->doctor->phone)Phone: {{ $prescription->doctor->phone }}@endif
            </div>
        </div>

        {{-- Date of Consultation --}}
        <table class="info-grid">
            <tr>
                <td class="info-label">Date Of Consultation</td>
                <td class="info-value">{{ $prescription->prescription_date->format('d/m/Y') }}</td>
                <td style="width: 20px;"></td>
                <td class="info-label">Prescription No.</td>
                <td class="info-value">{{ $prescription->prescription_number }}</td>
            </tr>
        </table>

        {{-- Patient Info --}}
        <table class="two-col">
            <tr>
                <td>
                    <table class="info-grid">
                        <tr>
                            <td class="info-label">Name of Patient</td>
                            <td class="info-value">{{ $prescription->patient->full_name }}</td>
                        </tr>
                        <tr>
                            <td class="info-label">Address</td>
                            <td class="info-value">
                                {{ $prescription->patient->address ?? '' }}
                                @if($prescription->patient->city), {{ $prescription->patient->city }}@endif
                                @if($prescription->patient->state), {{ $prescription->patient->state }}@endif
                            </td>
                        </tr>
                    </table>
                </td>
                <td>
                    <table class="info-grid">
                        <tr>
                            <td class="info-label" style="width:60px;">Age</td>
                            <td class="info-value">
                                @if($prescription->patient->date_of_birth)
                                    {{ $prescription->patient->date_of_birth->age }} years
                                @else
                                    -
                                @endif
                            </td>
                            <td class="info-label" style="width:60px;">Gender</td>
                            <td class="info-value">{{ ucfirst($prescription->patient->gender ?? '-') }}</td>
                        </tr>
                        <tr>
                            <td class="info-label" style="width:60px;">Blood Group</td>
                            <td class="info-value" colspan="3">{{ $prescription->patient->blood_group ?? '-' }}</td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>

        {{-- Main Content: Left (complaints/history/findings) + Right (diagnosis/medicines) --}}
        <table class="main-content">
            <tr>
                <td class="left-col">
                    {{-- Chief Complaints --}}
                    <div class="section-header">Chief Complaints</div>
                    <div class="section-content">
                        @if($consultation && !empty($consultation->chief_complaints))
                            @if(is_array($consultation->chief_complaints))
                                {{ implode(', ', $consultation->chief_complaints) }}
                            @else
                                {{ $consultation->chief_complaints }}
                            @endif
                        @else
                            {{ $prescription->appointment->reason ?? '-' }}
                        @endif
                    </div>

                    {{-- Relevant Points from History --}}
                    <div class="section-header">Relevant Points From History</div>
                    <div class="section-content">
                        @if($consultation && !empty($consultation->patient_history))
                            @if(is_array($consultation->patient_history))
                                {{ implode(', ', $consultation->patient_history) }}
                            @else
                                {{ $consultation->patient_history }}
                            @endif
                        @elseif($prescription->patient->medical_history)
                            {{ $prescription->patient->medical_history }}
                        @else
                            -
                        @endif
                    </div>

                    {{-- Examination / Lab Findings --}}
                    <div class="section-header">Examination / Lab Findings</div>
                    <div class="section-content">
                        @if($prescription->appointment && $prescription->appointment->notes)
                            {{ $prescription->appointment->notes }}
                        @else
                            -
                        @endif
                    </div>

                    {{-- Suggested Investigations --}}
                    <div class="section-header">Suggested Investigations</div>
                    <div class="section-content">-</div>
                </td>

                <td class="right-col">
                    {{-- Diagnosis --}}
                    <div class="section-header">Diagnosis or Provisional Diagnosis</div>
                    <div class="section-content">
                        {{ $prescription->diagnosis ?? '-' }}
                    </div>

                    {{-- Rx - Medicines --}}
                    <span class="rx-symbol">&#8478;</span>

                    @foreach($prescription->items as $index => $item)
                        <div class="medicine-item">
                            <div class="medicine-name">
                                {{ $index + 1 }}. {{ strtoupper($item->medicine_name) }}
                                @if($item->dosage) ({{ $item->dosage }}) @endif
                            </div>
                            <div class="medicine-details">
                                @php
                                    $details = [];
                                    $routeLabel = \App\Models\Prescription::getRouteOptions()[$item->route] ?? ucfirst($item->route ?? '');
                                    if ($routeLabel) $details[] = $routeLabel;
                                    if ($item->frequency) $details[] = $item->frequency;
                                    if ($item->duration) $details[] = $item->duration;
                                    if ($item->quantity) $details[] = 'Qty: ' . $item->quantity;
                                @endphp
                                {{ implode(' | ', $details) }}
                                @if($item->instructions)
                                    <br><em>{{ $item->instructions }}</em>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </td>
            </tr>
        </table>

        {{-- Special Instructions / Notes --}}
        <div class="footer-section">
            <div class="section-header">Special Instructions</div>
            <div class="special-instructions">
                {{ $prescription->notes ?? '-' }}
                @if($prescription->patient->allergies)
                    <br><strong>Known Allergies:</strong> {{ $prescription->patient->allergies }}
                @endif
            </div>
        </div>

        {{-- Signature --}}
        <div class="signature-area">
            <div class="signature-line">
                <strong>Dr. {{ $prescription->doctor->name }}</strong><br>
                {{ $prescription->doctor->specialization ?? '' }}<br>
                @if($prescription->doctor->license_number)
                    Reg. No: {{ $prescription->doctor->license_number }}
                @endif
            </div>
        </div>

        {{-- Teleconsultation Note --}}
        <div class="teleconsultation-note">
            Note: This prescription is generated on a teleconsultation.
        </div>
        <div class="platform-name">
            Tele Health Mart - Telemedicine Consultation Platform
        </div>
    </div>
</body>
</html>

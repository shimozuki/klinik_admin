<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 11px;
            line-height: 1.5;
        }

        .kop {
            width: 100%;
            border-bottom: 2px solid #000;
            margin-bottom: 15px;
            padding-bottom: 10px;
        }

        .kop table {
            width: 100%;
        }

        .kop img {
            width: 70px;
        }

        .title {
            text-align: center;
            font-weight: bold;
            font-size: 14px;
            margin: 15px 0;
            text-transform: uppercase;
        }

        table.report {
            width: 100%;
            border-collapse: collapse;
        }

        table.report th,
        table.report td {
            border: 1px solid #000;
            padding: 6px;
            font-size: 10px;
        }

        table.report th {
            background: #f0f0f0;
            text-align: center;
        }

        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        .footer {
            margin-top: 30px;
            width: 100%;
        }

        .footer td {
            text-align: center;
        }
    </style>
</head>

<body>

    {{-- HEADER --}}
    <div class="kop">
        <table>
            <tr>
                <td width="15%">
                    <img src="{{ public_path('logo-klinik.png') }}">
                </td>
                <td align="center">
                    <h2>KLINIK SEHAT SENTOSA</h2>
                    <p>Jl. Kesehatan No. 123, Telp: 08123456789</p>
                </td>
                <td width="15%"></td>
            </tr>
        </table>
    </div>

    {{-- TITLE --}}
    <div class="title">
        LAPORAN BOOKING<br>
        <small>
            Periode:
            {{ $startDate ? \Carbon\Carbon::parse($startDate)->format('d-m-Y') : '-' }}
            s/d
            {{ $endDate ? \Carbon\Carbon::parse($endDate)->format('d-m-Y') : '-' }}
        </small>
    </div>

    {{-- TABLE DATA --}}
    <table class="report">
        <thead>
            <tr>
                <th>No</th>
                <th>No. Reservasi</th>
                <th>Tanggal</th>
                <th>Pasien</th>
                <th>Dokter</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($data as $i => $row)
            <tr>
                <td class="text-center">{{ $i + 1 }}</td>
                <td>{{ $row->nomor_reservasi }}</td>
                <td class="text-center">
                    {{ \Carbon\Carbon::parse($row->tanggal_reservasi)->format('d-m-Y') }}
                </td>
                <td>{{ $row->pasien->user->name ?? '-' }}</td>
                <td>{{ $row->dokter->name ?? '-' }}</td>
                <td class="text-center">{{ ucfirst($row->status) }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="text-center">
                    Tidak ada data
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>

    {{-- FOOTER --}}
    <table class="footer">
        <tr>
            <td width="60%"></td>
            <td>
                {{ now()->format('d F Y') }}<br>
                Kepala Klinik<br><br><br>
                <b>( ____________________ )</b>
            </td>
        </tr>
    </table>

</body>

</html>
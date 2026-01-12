<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 11px;
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

        .total {
            font-weight: bold;
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
        LAPORAN PENDAPATAN<br>
        <small>
            PERIODE:
            {{ $startDate ? \Carbon\Carbon::parse($startDate)->format('d-m-Y') : '-' }}
            s/d
            {{ $endDate ? \Carbon\Carbon::parse($endDate)->format('d-m-Y') : '-' }}
        </small>
    </div>

    {{-- TABLE --}}
    <table class="report">
        <thead>
            <tr>
                <th>No</th>
                <th>Tanggal</th>
                <th>No. Reservasi</th>
                <th>Pasien</th>
                <th>Dokter</th>
                <th>Pendapatan</th>
            </tr>
        </thead>
        <tbody>
            @php($total = 0)
            @forelse ($data as $i => $row)
            @php($total += $row->biaya)
            <tr>
                <td class="text-center">{{ $i + 1 }}</td>
                <td class="text-center">
                    {{ \Carbon\Carbon::parse($row->tanggal_pemeriksaan)->format('d-m-Y') }}
                </td>
                <td>{{ $row->reservasi->nomor_reservasi ?? '-' }}</td>
                <td>{{ $row->pasien->user->name ?? '-' }}</td>
                <td>{{ $row->dokter->name ?? '-' }}</td>
                <td class="text-right">
                    Rp {{ number_format($row->biaya, 0, ',', '.') }}
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="text-center">
                    Tidak ada data
                </td>
            </tr>
            @endforelse
        </tbody>

        {{-- TOTAL --}}
        <tfoot>
            <tr>
                <td colspan="5" class="text-right total">TOTAL</td>
                <td class="text-right total">
                    Rp {{ number_format($total, 0, ',', '.') }}
                </td>
            </tr>
        </tfoot>
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
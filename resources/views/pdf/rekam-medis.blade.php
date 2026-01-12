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

        /* ===== HEADER ===== */
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

        .kop h2 {
            margin: 0;
            font-size: 16px;
        }

        .kop p {
            margin: 2px 0 0 0;
            font-size: 11px;
        }

        /* ===== TITLE ===== */
        .title {
            text-align: center;
            font-weight: bold;
            font-size: 14px;
            margin: 15px 0;
            text-transform: uppercase;
        }

        /* ===== SECTION ===== */
        .section {
            border: 1px solid #000;
            margin-bottom: 10px;
        }

        .section-title {
            background: #f0f0f0;
            padding: 6px;
            font-weight: bold;
            border-bottom: 1px solid #000;
        }

        .section-body {
            padding: 8px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        td {
            padding: 4px;
            vertical-align: top;
        }

        /* ===== RESEP ===== */
        .resep-table td {
            border-bottom: 1px dashed #999;
            padding: 6px 0;
        }

        /* ===== FOOTER ===== */
        .signature {
            margin-top: 30px;
            width: 100%;
        }

        .signature td {
            text-align: center;
        }
    </style>
</head>

<body>

    <!-- HEADER -->
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

    <div class="title">Rekam Medis Pasien</div>

    <!-- IDENTITAS -->
    <div class="section">
        <div class="section-body">
            <table>
                <tr>
                    <td width="25%">No. Rekam Medis</td>
                    <td width="2%">:</td>
                    <td>{{ $rm->nomor_rekam }}</td>
                </tr>
                <tr>
                    <td>Nama Pasien</td>
                    <td>:</td>
                    <td>{{ $rm->pasien->user->name ?? '-' }}</td>
                </tr>
                <tr>
                    <td>Dokter</td>
                    <td>:</td>
                    <td>{{ $rm->dokter->name ?? '-' }}</td>
                </tr>
                <tr>
                    <td>Tanggal</td>
                    <td>:</td>
                    <td>{{ $rm->tanggal_pemeriksaan?->format('d-m-Y') }}</td>
                </tr>
            </table>
        </div>
    </div>

    <!-- ANAMNESIS -->
    <div class="section">
        <div class="section-title">Anamnesis</div>
        <div class="section-body">
            {{ $rm->anamnesis }}
        </div>
    </div>

    <!-- DIAGNOSIS -->
    <div class="section">
        <div class="section-title">Diagnosis</div>
        <div class="section-body">
            {{ $rm->diagnosis }}
        </div>
    </div>

    <!-- TINDAKAN -->
    <div class="section">
        <div class="section-title">Tindakan</div>
        <div class="section-body">
            {{ $rm->tindakan ?? '-' }}
        </div>
    </div>

    <!-- RESEP -->
    <div class="section">
        <div class="section-title">Resep Obat</div>
        <div class="section-body">

            @if ($rm->resepDetail && $rm->resepDetail->count() > 0)
            <table class="resep-table">
                @foreach ($rm->resepDetail as $obat)
                <tr>
                    <td>
                        <b>{{ $obat->nama_obat }}</b><br>
                        Dosis: {{ $obat->dosis }} <br>
                        Frekuensi: {{ $obat->frekuensi }} <br>
                        Durasi: {{ $obat->durasi }} <br>
                        Catatan: {{ $obat->catatan }}
                    </td>
                </tr>
                @endforeach
            </table>
            @else
            <i>Tidak ada resep obat</i>
            @endif

        </div>
    </div>

    <!-- TANDA TANGAN -->
    <table class="signature">
        <tr>
            <td width="60%"></td>
            <td>
                Dokter Pemeriksa<br><br><br>
                <b>{{ $rm->dokter->name }}</b>
            </td>
        </tr>
    </table>

</body>

</html>
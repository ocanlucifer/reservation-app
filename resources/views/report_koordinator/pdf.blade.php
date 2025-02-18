<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Kunjungan</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            margin: 10px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 10px;
        }

        table, th, td {
            border: 1px solid black;
        }

        th, td {
            padding: 5px;
            text-align: left;
            vertical-align: middle;
        }

        th {
            background-color: #f2f2f2;
        }

        .badge {
            padding: 3px 5px;
            font-size: 9px;
            color: white;
            border-radius: 3px;
        }

        .bg-success {
            background-color: green;
        }

        .bg-danger {
            background-color: red;
        }

        /* Mode Cetak */
        @media print {
            body {
                margin: 10mm;
            }

            table {
                page-break-inside: auto;
            }

            tr {
                page-break-inside: avoid;
                page-break-after: auto;
            }

            th, td {
                font-size: 9px;
                padding: 4px;
            }
        }

        /* Tanda Tangan */
        .signature {
            position: absolute;
            bottom: 40px; /* Posisi tanda tangan dari bawah */
            right: 50px; /* Posisi tanda tangan dari sisi kanan */
            max-width: 150px;
        }
    </style>
</head>
<body>
    <div style="text-align: center;">
        <!-- Menambahkan gambar sebagai kop surat --><
        <img src="{{ $imageSrc }}" alt="Logo" style="max-width: 100%; height: auto; margin-bottom: 10px;">

        <h1>Laporan Koordinator</h1>
    </div>
    <table>
        <thead>
            <tr>
                <th>Gedung</th>
                <th>Tanggal</th>
                <th>Jam</th>
                <th>Instansi</th>
                <th>Nama</th>
                <th>Orang</th>
                <th>Kegiatan</th>
                <th>Penanggung Jawab</th>
                <th>Anggota</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($visitSchedules as $visitSchedule)
            <tr>
                <td>{{ $visitSchedule->schedule->building->name }}</td>
                <td>{{ $visitSchedule->schedule->tanggal }}</td>
                <td>
                    {{ \Carbon\Carbon::parse($visitSchedule->schedule->start_time)->format('H:i') }} -
                    {{ \Carbon\Carbon::parse($visitSchedule->schedule->end_time)->format('H:i') }}
                </td>
                <td>{{ $visitSchedule->visitor_company }}</td>
                <td>{{ $visitSchedule->visitor_name }}</td>
                <td>{{ $visitSchedule->visitor_person }}</td>
                <td>{{ $visitSchedule->visitor_purphose }}</td>
                <td>{{ $visitSchedule->tourguide_name }}</td>
                <td>{!! nl2br(e($visitSchedule->TourGuideMemo)) !!}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Tanda tangan Kepala Unit Komunikasi Publik -->
    {{-- <div class="signature"> --}}
        {{-- <img src="{{ public_path('images/tanda_tangan_riki.png') }}" alt="Tanda Tangan Riki Rahdiwansyah"> --}}
        {{-- <p style="text-align: center;">Riki Rahdiwansyah</p> --}}
        {{-- <p style="text-align: center;">Kepala Unit Komunikasi Publik</p> --}}
    {{-- </div> --}}
</body>
</html>

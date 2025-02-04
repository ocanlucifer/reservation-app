<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Jadwal Gedung</title>
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

        <h1>Laporan Jadwal Gedung</h1>
    </div>
    <table>
        <thead">
            <tr>
                <th>Gedung</th>
                <th>Tanggal</th>
                <th>Jam</th>
                <th>Pengunjung / Pengguna</th>
                <th>Kegiatan</th>
                <th>Status</th>
                <th>Schedule No.</th>
                <th>Entry User</th>
                <th>Tanggal Entry</th>
                <th>Update User</th>
                <th>Tanggal Edit</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($buildingSchedules as $buildingSchedule)
            <tr>
                <td>{{ $buildingSchedule->building->name }}</td>
                <td>{{ $buildingSchedule->tanggal }}</td>
                <td>
                    {{ \Carbon\Carbon::parse($buildingSchedule->start_time)->format('H:i') }} -
                    {{ \Carbon\Carbon::parse($buildingSchedule->end_time)->format('H:i') }}
                </td>
                <td>{{ $buildingSchedule->visitReservation->visitor_name }} ( {{ $buildingSchedule->visitReservation->visitor_company }} )</td>
                <td>{{ $buildingSchedule->visitReservation->visitor_purphose }}</td>
                <td>
                    @if ($buildingSchedule->is_booked)
                        Sudah di pesan
                    @else
                        Belum di pesan
                    @endif
                </td>
                <td>{{ $buildingSchedule->transaction_number }}</td>
                <td>{{ $buildingSchedule->creator->name }}</td>
                <td>{{ $buildingSchedule->created_at->format('d M Y H:i') }}</td>
                <td>
                    {{ $buildingSchedule->update_by ? $buildingSchedule->updater->name : 'Belum Diperbarui' }}
                </td>
                <td>
                    {{ $buildingSchedule->update_by ? $buildingSchedule->updated_at->format('d M Y H:i') : 'Belum Diperbarui' }}
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>

<!DOCTYPE html>
<html>
<head>
    <title>Laporan Kunjungan</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }
        table, th, td {
            border: 1px solid black;
        }
        th, td {
            padding: 8px;
            text-align: left;
        }
    </style>
</head>
<body>
    <h1>Laporan Kunjungan</h1>
    <table>
        <thead>
            <tr>
                <th>Gedung</th>
                <th>Tanggal</th>
                <th>Jam</th>
                <th>Status</th>
                <th>Pengunjung</th>
                <th>Jumlah Orang</th>
                <th>Tujuan</th>
                <th>Alamat</th>
                <th>Kontak</th>
                <th>Catatan</th>
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
                <td>
                    @if ($visitSchedule->is_booked)
                        Sudah di pasan
                    @else
                        Belum di pesan
                    @endif
                </td>
                <td>{{ $visitSchedule->visitor_company }}</td>
                <td>{{ $visitSchedule->visitor_person }}</td>
                <td>{{ $visitSchedule->visitor_purphose }}</td>
                <td>{{ $visitSchedule->visitor_address }}</td>
                <td>{{ $visitSchedule->visitor_contact }}</td>
                <td>{{ $visitSchedule->visitor_note }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>

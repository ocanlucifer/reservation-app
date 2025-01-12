<!DOCTYPE html>
<html>
<head>
    <title>Laporan Jadwal Gedung</title>
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
    <h1>Laporan Jadwal Gedung</h1>
    <table>
        <thead">
            <tr>
                <th>Gedung</th>
                <th>Tanggal</th>
                <th>Jam</th>
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

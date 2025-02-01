<table class="table table-bordered table-striped">
    <thead class="thead-dark">
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
                    <span class="badge bg-success">
                        Sudah di pesan
                    </span>
                @else
                    <span class="badge bg-danger">Belum di pesan</span>
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


<!-- Pagination Links -->
<div class="d-flex justify-content-between align-items-center mt-3">
    <span class="text-muted">
        Menampilkan {{ $buildingSchedules->firstItem() }} sampai {{ $buildingSchedules->lastItem() }} dari {{ $buildingSchedules->total() }} Jadwal Gedung
    </span>
    <div>
        {!! $buildingSchedules->links('pagination::bootstrap-5') !!}
    </div>
</div>

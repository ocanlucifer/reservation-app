<table class="table table-bordered table-striped">
    <thead class="thead-dark">
        <tr>
            <th>Gedung</th>
            <th>Tanggal</th>
            <th>Jam</th>
            <th>Status</th>
            <th>Instansi</th>
            <th>Nama Pengunjung</th>
            <th>Jumlah Orang</th>
            <th>Jumlah Kendaraan</th>
            <th>Kegiatan</th>
            <th>Kontak</th>
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
                    <span class="badge bg-success">Sudah di pasan</span>
                @else
                    <span class="badge bg-danger">Belum di pesan</span>
                @endif
            </td>
            <td>{{ $visitSchedule->visitor_company }}</td>
            <td>{{ $visitSchedule->visitor_name }}</td>
            <td>{{ $visitSchedule->visitor_person }}</td>
            <td>{{ $visitSchedule->visitor_jumlah_kendaraan }}</td>
            <td>{{ $visitSchedule->visitor_purphose }}</td>
            <td>{{ $visitSchedule->visitor_contact }}</td>
        </tr>
        @endforeach
    </tbody>
</table>


<!-- Pagination Links -->
<div class="d-flex justify-content-between align-items-center mt-3">
    <span class="text-muted">
        Menampilkan {{ $visitSchedules->firstItem() }} sampai {{ $visitSchedules->lastItem() }} dari {{ $visitSchedules->total() }} Jadwal Kunjungan
    </span>
    <div>
        {!! $visitSchedules->links('pagination::bootstrap-5') !!}
    </div>
</div>

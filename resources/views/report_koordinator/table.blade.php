<table class="table table-bordered table-striped">
    <thead class="thead-dark">
        <tr>
            <th>Gedung</th>
            <th>Tanggal</th>
            <th>Jam</th>
            <th>Instansi</th>
            <th>Jumlah Orang</th>
            <th>Kegiatan</th>
            <th>Penanggung Jawab</th>
            <th>Angggota</th>
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
            <td>{{ $visitSchedule->visitor_person }}</td>
            <td>{{ $visitSchedule->visitor_purphose }}</td>
            <td>{{ $visitSchedule->tourguide_name }}</td>
            <td>{!! nl2br(e($visitSchedule->TourGuideMemo)) !!}</td>
        </tr>
        @endforeach
    </tbody>
</table>


<!-- Pagination Links -->
<div class="d-flex justify-content-between align-items-center mt-3">
    <span class="text-muted">
        Menampilkan {{ $visitSchedules->firstItem() }} sampai {{ $visitSchedules->lastItem() }} dari {{ $visitSchedules->total() }} Data Koordinator
    </span>
    <div>
        {!! $visitSchedules->links('pagination::bootstrap-5') !!}
    </div>
</div>

<div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-3">
    @foreach ($buildingSchedules as $buildingSchedule)
    <div class="col">
        <div class="card shadow-sm">
            <div class="card-header bg-dark text-white p-3 d-flex justify-content-between align-items-center">
                <h6 class="card-title mb-0">{{ $buildingSchedule->building_id ? $buildingSchedule->building->name : 'Belum di pilihkan Gedung' }}</h6>
                <div>
                    <button class="btn btn-primary btn-sm booking"
                            data-id="{{ $buildingSchedule->id }}"
                            data-status="{{ $buildingSchedule->is_available ? 'Tidak' : 'Ya' }}"
                            data-booked="{{ $buildingSchedule->is_booked ? 'Tidak' : 'Ya' }}"
                            data-bs-toggle="tooltip" title="Reservasi"
                            {{ !$buildingSchedule->is_available ? 'disabled' : '' }}>
                        <i class="bi bi-calendar-check"></i>
                    </button>
                    @if (auth()->user()->role === 'admin' or auth()->user()->role === 'building')
                        <button class="btn btn-warning btn-sm edit-buildingSchedule"
                                data-id="{{ $buildingSchedule->id }}"
                                data-building_id="{{ $buildingSchedule->building_id }}"
                                data-tanggal="{{ $buildingSchedule->tanggal }}"
                                data-start_time="{{ $buildingSchedule->start_time }}"
                                data-end_time="{{ $buildingSchedule->end_time }}"
                                data-status="{{ $buildingSchedule->is_available }}"
                                data-bs-toggle="tooltip" title="Ubah"
                                {{ $buildingSchedule->is_booked ? 'disabled' : '' }}>
                            <i class="bi bi-pencil-square"></i>
                        </button>
                        <button class="btn btn-danger btn-sm delete-buildingSchedule"
                                data-id="{{ $buildingSchedule->id }}"
                                data-name="{{ $buildingSchedule->transaction_number }}"
                                data-bs-toggle="tooltip" title="Hapus"
                                {{ $buildingSchedule->is_booked ? 'disabled' : '' }}>
                            <i class="bi bi-trash"></i>
                        </button>
                        <button type="submit" class="btn btn-sm {{ $buildingSchedule->is_available ? 'btn-secondary' : 'btn-success' }} toggleStatus"
                                data-id="{{ $buildingSchedule->id }}"
                                data-status="{{ $buildingSchedule->is_available ? 'Tidak' : 'Ya' }}"
                                data-bs-toggle="tooltip" title="{{ $buildingSchedule->is_available ? 'Tidak tersedia' : 'Tersedia' }}"
                                {{ $buildingSchedule->is_booked ? 'disabled' : '' }}>
                            <i class="bi {{ $buildingSchedule->is_available ? 'bi-toggle-on' : 'bi-toggle-off' }}"></i>
                        </button>
                    @endif

                </div>
            </div>
            <div class="card-body p-3">
                <div class="mb-3">
                    <p class="card-text mb-1">
                        <strong>Tanggal:</strong> {{ $buildingSchedule->tanggal }}
                    </p>
                    <p class="card-text mb-1">
                        <strong>Jam:</strong>
                        {{ \Carbon\Carbon::parse($buildingSchedule->start_time)->format('H:i') }} -
                        {{ \Carbon\Carbon::parse($buildingSchedule->end_time)->format('H:i') }}
                    </p>
                    <p class="card-text mb-1">
                        <strong>Status:</strong>
                        @if ($buildingSchedule->is_available)
                            <span class="badge bg-success">Tersedia</span>
                        @else
                            <span class="badge bg-danger">Tidak Tersedia</span>
                        @endif
                    </p>
                    <p class="card-text mb-1">
                        @if ($buildingSchedule->is_booked)
                            <span class="badge bg-success">
                                di pesan oleh: {{ $buildingSchedule->humas_id ? $buildingSchedule->humas->name: '' }} ( {{ $buildingSchedule->humas_id ? $buildingSchedule->booked_date : ''}})
                            </span>
                        @else
                            <span class="badge bg-danger">Belum di pesan</span>
                        @endif
                    </p>
                </div>

                <hr class="my-2">

                <div class="mb-3">
                    <p class="card-text mb-1">
                        <strong>Schedule No.:</strong> {{ $buildingSchedule->transaction_number}}<br>
                    </p>
                    <p class="card-text mb-1">
                        <strong>Entry User:</strong> {{ $buildingSchedule->creator->name }}<br>
                        <strong>Tanggal Entry:</strong> {{ $buildingSchedule->created_at->format('d M Y H:i') }}
                    </p>
                    <p class="card-text mb-1">
                        <strong>Update User:</strong>
                        {{ $buildingSchedule->update_by ? $buildingSchedule->updater->name : 'Belum Diperbarui' }}<br>
                        <strong>Tanggal Edit:</strong>
                        {{ $buildingSchedule->update_by ? $buildingSchedule->updated_at->format('d M Y H:i') : 'Belum Diperbarui' }}
                    </p>
                </div>
            </div>
        </div>
    </div>
    @endforeach
</div>

<!-- Pagination Links -->
<div class="d-flex justify-content-between align-items-center mt-3">
    <span class="text-muted">
        Menampilkan {{ $buildingSchedules->firstItem() }} sampai {{ $buildingSchedules->lastItem() }} dari {{ $buildingSchedules->total() }} Jadwal Gedung
    </span>
    <div>
        {!! $buildingSchedules->links('pagination::bootstrap-5') !!}
    </div>
</div>

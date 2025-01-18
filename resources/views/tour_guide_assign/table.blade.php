<div class="row g-3">
    @foreach ($visitSchedules as $visitSchedule)
    <div class="col-12">
        <div class="card shadow-sm">
            <div class="card-header bg-dark text-white p-3 d-flex justify-content-between align-items-center">
                <h6 class="card-title mb-0">{{ $visitSchedule->schedule->building->name }}</h6>
                <div>
                    <button class="btn btn-primary btn-sm add-TourGuideAssign"
                        data-id="{{ $visitSchedule->id }}"
                        data-status="{{ $visitSchedule->tour_guide_requested ? 'Batalkan Pengajuan' : 'Ajukan' }}"
                        data-bs-toggle="tooltip"
                        title="Pilih tour Guide"
                        {{ $visitSchedule->tour_guide_assign ? 'hidden' : '' }}
                        {{ $visitSchedule->is_confirm ? 'hidden' : '' }}>
                        <i class="bi bi-person-lines-fill"></i>
                    </button>
                    <button class="btn btn-warning btn-sm edit-TourGuideAssign"
                            data-id="{{ $visitSchedule->id }}"
                            data-status="{{ $visitSchedule->tour_guide_assign }}"
                            data-tourguideid="{{ $visitSchedule->tour_guide_id }}"
                            data-bs-toggle="tooltip" title="Ubah Tour Guide"
                            {{ !$visitSchedule->tour_guide_assign ? 'hidden' : '' }}
                            {{ $visitSchedule->is_confirm ? 'hidden' : '' }}>
                        <i class="bi bi-pencil-square"></i>
                    </button>
                    <button class="btn btn-danger btn-sm delete-TourGuideAssign"
                            data-id="{{ $visitSchedule->id }}"
                            data-bs-toggle="tooltip" title="Batalkan Tour guide"
                            {{ !$visitSchedule->tour_guide_assign ? 'hidden' : '' }}
                            {{ $visitSchedule->is_confirm ? 'hidden' : '' }}>
                        <i class="bi bi-x-circle"></i>
                    </button>
                </div>
            </div>
            <div class="card-body p-3">
                <div class="row">
                    <div class="col-md-6">
                        <p class="card-text mb-1">
                            <strong>Tanggal:</strong> {{ $visitSchedule->schedule->tanggal }}
                        </p>
                        <p class="card-text mb-1">
                            <strong>Jam:</strong>
                            {{ \Carbon\Carbon::parse($visitSchedule->schedule->start_time)->format('H:i') }} -
                            {{ \Carbon\Carbon::parse($visitSchedule->schedule->end_time)->format('H:i') }}
                        </p>
                        <p class="card-text mb-1">
                            <strong>Status:</strong>
                            @if ($visitSchedule->is_available)
                                <span class="badge bg-success">Tersedia</span>
                            @else
                                <span class="badge bg-danger">Tidak Tersedia</span>
                            @endif
                        </p>
                        <p class="card-text mb-1">
                            @if ($visitSchedule->is_booked)
                                <span class="badge bg-success">
                                    di pesan oleh: {{ $visitSchedule->visitor->name }} ( {{ $visitSchedule->booked_date }})
                                </span>
                            @else
                                <span class="badge bg-danger">Belum di pesan</span>
                            @endif
                        </p>
                        <p class="card-text mb-1">
                            @if ($visitSchedule->tour_guide_requested)
                                <span class="badge bg-success">
                                    Pengajuan Tour Guide oleh: {{ $visitSchedule->humas->name }}  ({{ $visitSchedule->tour_guide_req_date }})
                                </span>
                            @else
                                <span class="badge bg-danger">Tour Guide Belum di ajukan</span>
                            @endif
                        </p>
                        <p class="card-text mb-1">
                            @if ($visitSchedule->tour_guide_assign)
                                <span class="badge bg-success">
                                    Tour Guide: {{ $visitSchedule->tour_guide_id ? $visitSchedule->tourGuide->name : '' }} ditugaskan oleh: {{ $visitSchedule->koordinator_id ? $visitSchedule->koordinator->name : '' }} ({{ $visitSchedule->tour_guide_assign_date }})
                                </span>
                            @else
                                <span class="badge bg-danger">Belum Mendapatkan Tour Guide</span>
                            @endif
                        </p>
                        <p class="card-text mb-1">
                            @if ($visitSchedule->is_confirm)
                                <span class="badge bg-success">
                                    Telah di konfirmasi oleh: {{ $visitSchedule->humas->name }} ({{ $visitSchedule->confirm_date }})
                                </span>
                            @else
                                <span class="badge bg-danger">Belum di Konfirmasi</span>
                            @endif
                        </p>
                    </div><div class="col-md-6">
                        <div class="row">
                            <div class="col-3"><strong>Pengunjung</strong></div>
                            <div class="col-8">: {{ $visitSchedule->visitor_company }}</div>

                            <div class="col-3"><strong>Jumlah orang</strong></div>
                            <div class="col-8">: {{ $visitSchedule->visitor_person }}</div>

                            <div class="col-3"><strong>Tujuan</strong></div>
                            <div class="col-8">: {{ $visitSchedule->visitor_purphose }}</div>

                            <div class="col-3"><strong>Alamat</strong></div>
                            <div class="col-8">: {{ $visitSchedule->visitor_address }}</div>

                            <div class="col-3"><strong>Kontak</strong></div>
                            <div class="col-8">: {{ $visitSchedule->visitor_contact }}</div>

                            <div class="col-3"><strong>Catatan</strong></div>
                            <div class="col-8">: {{ $visitSchedule->visitor_note }}</div>
                        </div>
                    </div>
                </div>
                <hr class="my-2">
                <div class="row">
                    <div class="col-md-4">
                        <div class="row">
                            <div class="col-4"><strong>Schedule No.</strong></div>
                            <div class="col-8">: {{ $visitSchedule->transaction_number }}</div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="row">
                            <div class="col-4"><strong>Entry User</strong></div>
                            <div class="col-8">: {{ $visitSchedule->creator->name }}</div>
                        </div>
                        <div class="row">
                            <div class="col-4"><strong>Tanggal</strong></div>
                            <div class="col-8">: {{ $visitSchedule->created_at->format('d M Y H:i') }}</div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="row">
                            <div class="col-4"><strong>Update User</strong></div>
                            <div class="col-8">: {{ $visitSchedule->update_by ? $visitSchedule->updater->name : 'Belum Diperbarui' }}</div>
                        </div>
                        <div class="row">
                            <div class="col-4"><strong>Tanggal</strong></div>
                            <div class="col-8">: {{ $visitSchedule->update_by ? $visitSchedule->updated_at->format('d M Y H:i') : 'Belum Diperbarui' }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endforeach
</div>

<!-- Pagination Links -->
<div class="d-flex justify-content-between align-items-center mt-3">
    <span class="text-muted">
        Menampilkan {{ $visitSchedules->firstItem() }} sampai {{ $visitSchedules->lastItem() }} dari {{ $visitSchedules->total() }} Jadwal Kunjungan
    </span>
    <div>
        {!! $visitSchedules->links('pagination::bootstrap-5') !!}
    </div>
</div>

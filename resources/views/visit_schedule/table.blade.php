<div class="row g-3">
    @foreach ($visitSchedules as $visitSchedule)
    <div class="col-12">
        <div class="card shadow-sm">
            <div class="card-header bg-dark text-white p-3 d-flex justify-content-between align-items-center">
                <h6 class="card-title mb-0">{{ $visitSchedule->schedule->building_id ? $visitSchedule->schedule->building->name : 'Belum di pilihkan Gedung' }}</h6>
                <div>
                    @if (auth()->user()->role === 'admin' or auth()->user()->role === 'humas')
                        <button class="btn {{ $visitSchedule->is_confirm ? 'btn-danger' : 'btn-success' }} btn-sm confirm-visit"
                            data-id="{{ $visitSchedule->id }}"
                            data-status="{{ $visitSchedule->is_confirm ? 'Batalkan Konfirmasi' : 'Konfirmasi' }}"
                            data-bs-toggle="tooltip"
                            title="{{ $visitSchedule->is_confirm ? 'Batalkan Konfirmasi' : 'Konfirmasi' }}"
                            {{ !$visitSchedule->tour_guide_assign ? 'hidden' : '' }}>
                            <i class="bi {{ $visitSchedule->is_confirm ? 'bi-x-circle' : 'bi-check-circle' }}"></i>
                        </button>
                        <button class="btn btn-info btn-sm request-tour-guide"
                            data-id="{{ $visitSchedule->id }}"
                            data-status="{{ $visitSchedule->tour_guide_requested ? 'Batalkan Pengajuan' : 'Ajukan' }}"
                            data-bs-toggle="tooltip"
                            title="{{ $visitSchedule->tour_guide_requested ? 'Batalkan Pengajuan Tour Guide' : 'Ajukan Tour Guide' }}"
                            {{ $visitSchedule->tour_guide_assign ? 'hidden' : '' }}
                            {{ !$visitSchedule->is_booked ? 'hidden' : '' }}>
                            <i class="bi bi-person-lines-fill"></i>
                        </button>
                        <button class="btn btn-danger btn-sm delete-buildingSchedule"
                                data-id="{{ $visitSchedule->id }}"
                                data-name="{{ $visitSchedule->transaction_number }}"
                                data-bs-toggle="tooltip" title="Hapus"
                                {{ $visitSchedule->is_booked ? 'hidden' : '' }}>
                            <i class="bi bi-trash"></i>
                        </button>
                        <button type="submit" class="btn btn-sm {{ $visitSchedule->is_available ? 'btn-secondary' : 'btn-success' }} toggleStatus"
                                data-id="{{ $visitSchedule->id }}"
                                data-status="{{ $visitSchedule->is_available ? 'Tidak' : 'Ya' }}"
                                data-bs-toggle="tooltip" title="{{ $visitSchedule->is_available ? 'Tidak tersedia' : 'Tersedia' }}"
                                {{ $visitSchedule->is_booked ? 'hidden' : '' }}>
                            <i class="bi {{ $visitSchedule->is_available ? 'bi-toggle-on' : 'bi-toggle-off' }}"></i>
                        </button>
                    @endif

                    <button class="btn {{ $visitSchedule->is_booked ? 'btn-warning' : 'btn-primary' }} btn-sm booking_form"
                            data-id="{{ $visitSchedule->id }}"
                            data-company="{{ $visitSchedule->visitor_company }}"
                            data-visitor_name="{{ $visitSchedule->visitor_name }}"
                            data-purphose="{{ $visitSchedule->visitor_purphose }}"
                            data-contact="{{ $visitSchedule->visitor_contact }}"
                            data-person="{{ $visitSchedule->visitor_person }}"
                            data-jumlah_kendaraan="{{ $visitSchedule->visitor_jumlah_kendaraan }}"
                            data-bs-toggle="tooltip" title="{{ $visitSchedule->is_booked ? 'Ubah Reservasi' : 'Buat Reservasi' }}"
                            {{-- {{ $visitSchedule->is_booked ? 'hidden' : '' }} --}}
                            {{ $visitSchedule->tour_guide_assign ? 'hidden' : '' }}
                            {{ $visitSchedule->tour_guide_requested ? 'disabled' : '' }}>
                        <i class="bi {{ $visitSchedule->is_booked ? 'bi-pencil-square' : 'bi-calendar-check' }}"></i>
                    </button>
                    <button class="btn btn-danger btn-sm cancel_booking"
                            data-id="{{ $visitSchedule->id }}"
                            data-status="{{ $visitSchedule->is_available ? 'Tidak' : 'Ya' }}"
                            data-booked="{{ $visitSchedule->is_booked ? 'Batalkan Reservasi' : 'Lakukan Reservasi' }}"
                            data-bs-toggle="tooltip" title="Batalkan Reservasi"
                            {{ !$visitSchedule->is_booked ? 'hidden' : '' }}
                            {{ $visitSchedule->tour_guide_assign ? 'hidden' : '' }}
                            {{ $visitSchedule->tour_guide_requested ? 'disabled' : '' }}>
                        <i class="bi bi-calendar-x"></i>
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
                                <span class="badge bg-success d-block text-start">
                                    Tour Guide: {{ $visitSchedule->tourguide_name }}
                                    <br> NIM: {{ $visitSchedule->tourguide_nim }}
                                    <br> Semester: {{ $visitSchedule->tourguide_semester }}
                                    <br> Kontak: {{ $visitSchedule->tourguide_contact }}
                                    <br> ditugaskan oleh: {{ $visitSchedule->koordinator_id ? $visitSchedule->koordinator->name : '' }} ({{ $visitSchedule->tour_guide_assign_date }})
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
                    </div>
                    <div class="col-md-6">
                        <div class="row">
                            <div class="col-3"><strong>Instansi Pengunjung</strong></div>
                            <div class="col-8">: {{ $visitSchedule->visitor_company }}</div>

                            <div class="col-3"><strong>Nama Pengunjung</strong></div>
                            <div class="col-8">: {{ $visitSchedule->visitor_name }}</div>

                            <div class="col-3"><strong>Jumlah orang</strong></div>
                            <div class="col-8">: {{ $visitSchedule->visitor_person }}</div>

                            <div class="col-3"><strong>Jumlah Kendaraan</strong></div>
                            <div class="col-8">: {{ $visitSchedule->visitor_jumlah_kendaraan }}</div>

                            <div class="col-3"><strong>Tujuan</strong></div>
                            <div class="col-8">: {{ $visitSchedule->visitor_purphose }}</div>

                            <div class="col-3"><strong>Kontak</strong></div>
                            <div class="col-8">: {{ $visitSchedule->visitor_contact }}</div>
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

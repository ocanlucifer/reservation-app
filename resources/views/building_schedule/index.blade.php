@extends('layouts.app')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="mb-0">Jadwal Gedung</h1>
    </div>

    <!-- Filter, Sort, and Search Form -->
    <form id="filter-form" class="mb-4">
        <div class="row g-2 justify-content-end">
            <!-- Search Input -->
            <div class="col-md-12 position-relative">
                <input
                    type="text"
                    name="search"
                    class="form-control form-control-sm ps-5"
                    placeholder="Cari Jadwal Gedung berdasarkan nama gedung dan tanggal"
                    id="search"
                    value="{{ $search }}"
                />
                <i class="fas fa-search position-absolute top-50 start-0 translate-middle-y ms-3"></i> <!-- Icon inside the input -->
            </div>
        </div>
        <div class="col-md-12 mt-3 justify-content-end">
            <div class="row align-items-center g-2">
                <!-- Group: Sort By -->
                <div class="col-auto">
                    <label for="sort_by" class="form-label mb-0"><strong>Urutkan</strong></label>
                </div>
                <div class="col-auto">
                    <select name="sort_by" id="sort_by" class="form-select form-select-sm">
                        <option value="tanggal" {{ $sortBy == 'tanggal' ? 'selected' : '' }}>Tanggal</option>
                        <option value="is_available" {{ $sortBy == 'is_available' ? 'selected' : '' }}>Ketersediaan</option>
                    </select>
                </div>
                <div class="col-auto">
                    <select name="order" id="order" class="form-select form-select-sm">
                        <option value="asc" {{ $order == 'asc' ? 'selected' : '' }}>Ter Awal</option>
                        <option value="desc" {{ $order == 'desc' ? 'selected' : '' }}>Terakhir</option>
                    </select>
                </div>

                <!-- Divider -->
                <div class="col-auto">
                    <span class="text-muted mx-2">|</span>
                </div>

                <!-- Group: Status -->
                <div class="col-auto">
                    <label for="is_available" class="form-label mb-0"><strong>Status</strong></label>
                </div>
                <div class="col-auto">
                    <select name="is_available" id="is_available" class="form-select form-select-sm">
                        <option value="" selected>Semua</option>
                        <option value="1">Tersedia</option>
                        <option value="0">Tidak Tersedia</option>
                    </select>
                </div>

                <!-- Divider -->
                <div class="col-auto">
                    <span class="text-muted mx-2">|</span>
                </div>

                <!-- Group: Booked -->
                <div class="col-auto">
                    <label for="is_booked" class="form-label mb-0"><strong>Booked</strong></label>
                </div>
                <div class="col-auto">
                    <select name="is_booked" id="is_booked" class="form-select form-select-sm">
                        <option value="" selected>Semua</option>
                        <option value="1">Ya</option>
                        <option value="0">Tidak</option>
                    </select>
                </div>

                <!-- Show Per Page: Aligned Right -->
                <div class="col-auto ms-auto position-relative">
                    <select name="per_page" id="per_page" class="form-select form-select-sm ps-5">
                        <option value="5" {{ $perPage == 5 ? 'selected' : '' }}>5 per page</option>
                        <option value="10" {{ $perPage == 10 ? 'selected' : '' }}>10 per page</option>
                        <option value="15" {{ $perPage == 15 ? 'selected' : '' }}>15 per page</option>
                    </select>
                    <i class="fas fa-th-list position-absolute top-50 start-0 translate-middle-y ms-3" title="Tampilkan List"></i> <!-- Icon inside the input -->
                </div>
            </div>
        </div>
    </form>

    <!-- Success Message -->
    <div id="success-message" class="alert alert-success d-none" role="alert">
        Data Jadwal Gedung berhasil di simpan!
    </div>

    <!-- Table Container for AJAX -->
    <div id="table-container">
        @include('building_schedule.table')
    </div>

    <!-- Modal for Create/Edit User Form -->
    <div class="modal fade" id="buildingScheduleModal" tabindex="-1" aria-labelledby="buildingScheduleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="buildingScheduleModalLabel">Form Jadwal Gedung</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="buildingSchedule-form">
                        @csrf
                        <input type="hidden" name="id" id="buildingSchedule-id">
                        <div class="mb-3">
                            <label for="buildingSchedule-building_id" class="form-label">Gedung</label>
                            <select id="buildingSchedule-building_id" name="building_id" class="form-select" required>
                                <option selected>Pilih Gedung</option>
                                @foreach($buildings as $building)
                                    <option value="{{ $building->id }}">{{ $building->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="tanggal" class="form-label">Tanggal</label>
                            <input type="date" id="buildingSchedule-tanggal" name ="tanggal" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label for="start_time" class="form-label">Waktu Mulai</label>
                            <input type="time" id="buildingSchedule-start_time" name="start_time" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label for="end_time" class="form-label">Waktu Selesai</label>
                            <input type="time" id="buildingSchedule-end_time" name="end_time" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label for="buildingSchedule-status" class="form-label">Tersedia</label>
                            <select id="buildingSchedule-status" name="is_available" class="form-select" required>
                                <option value="1">Ya</option>
                                <option value="0">Tidak</option>
                            </select>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="bi bi-x-circle"></i>
                    </button>
                    <button type="button" class="btn btn-primary" id="save-user">
                        <i class="bi bi-save"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {

        // Function to fetch users data
        function fetchbuildingSchedules() {
            const formData = $('#filter-form').serialize();

            $.ajax({
                url: "{{ route('buildingSchedules.index') }}",
                method: "GET",
                data: formData,
                success: function(response) {
                    $('#table-container').html(response);
                }
            });
        }

        //Event handler for sort links
        $(document).on('click', '.sortable', function(e) {
            e.preventDefault();
            $('#sort_by').val($(this).data('sort-by'));
            $('#order').val($(this).data('order'));
            fetchbuildingSchedules();
        });

        // Event listener for changes in form inputs
        $('#filter-form').on('change', 'select', function() {
            fetchbuildingSchedules();
        });

        // Trigger search when typing in search input
        $('#filter-form').on('keyup', '#search', function() {
            fetchbuildingSchedules();
        });

        // Event handler for pagination links
        $(document).on('click', '.pagination a', function(e) {
            e.preventDefault();
            const url = $(this).attr('href');
            const formData = $('#filter-form').serialize();

            $.ajax({
                url: url,
                method: "GET",
                data: formData,
                success: function(response) {
                    $('#table-container').html(response);
                }
            });
        });

        // Open the Create user form
        $('#open-create-form').on('click', function() {
            $('#buildingScheduleModalLabel').text('Buat Jadwal Gedung');
            $('#buildingSchedule-form')[0].reset(); // Clear form
            $('#buildingSchedule-id').val(''); // Clear hidden id field
            $('#buildingScheduleModal').modal('show');
        });

        // Open the Edit user form
        $(document).on('click', '.edit-buildingSchedule', function() {
            const buildingScheduleId = $(this).data('id');
            const building_id = $(this).data('building_id');
            const tanggal = $(this).data('tanggal');
            const start_time = $(this).data('start_time');
            const end_time = $(this).data('end_time');
            const buildingScheduleStatus = $(this).data('status');

            $('#buildingScheduleModalLabel').text('Ubah Jadwal Gedung');
            $('#buildingSchedule-id').val(buildingScheduleId);
            $('#buildingSchedule-building_id').val(building_id);
            $('#buildingSchedule-tanggal').val(tanggal);
            $('#buildingSchedule-start_time').val(start_time);
            $('#buildingSchedule-end_time').val(end_time);
            $('#buildingSchedule-status').val(buildingScheduleStatus);

            $('#buildingScheduleModal').modal('show');
        });

        // Save or update the user
        $('#save-user').on('click', function() {
            const formData = $('#buildingSchedule-form').serialize();
            const buildingScheduleId = $('#buildingSchedule-id').val();

            const url = buildingScheduleId ? `/buildingSchedules/${buildingScheduleId}` : '/buildingSchedules';
            const method = buildingScheduleId ? 'PUT' : 'POST';

            $.ajax({
                url: url,
                method: method,
                data: formData,
                success: function(response) {
                    // Show success message
                    // $('#success-message').removeClass('d-none').text('Data Jadwal Gedung Berhasil Di Simpan!');
                    $('#success-message').removeClass('d-none').text(response.success);

                    // Hide success message after 3 seconds
                    setTimeout(function() {
                        $('#success-message').addClass('d-none');
                    }, 3000);

                    $('#buildingScheduleModal').modal('hide'); // Close the modal
                    fetchbuildingSchedules();  // Reload the table after saving
                },
                error: function(xhr) {
                    alert(xhr.responseJSON.error || 'Terjadi kesalahan');
                }
            });
        });

        // Event listener for the delete button
        $(document).on('click', '.delete-buildingSchedule', function() {
            const buildingScheduleId = $(this).data('id');
            const buildingScheduleTrxNo = $(this).data('name');

            // Confirm before deletion
            if (confirm(`anda yakin ingin menghapus Jadwal Gedung "${buildingScheduleTrxNo}"?`)) {
                $.ajax({
                    url: `/buildingSchedules/${buildingScheduleId}`,
                    method: 'DELETE',
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content') // Include CSRF token
                    },
                    success: function(response) {
                        // Show success message
                        // $('#success-message').removeClass('d-none').text('Data Jadwal Gedung berhasil di hapus!');
                        $('#success-message').removeClass('d-none').text(response.success);


                        // Hide success message after 3 seconds
                        setTimeout(function() {
                            $('#success-message').addClass('d-none');
                        }, 3000);

                        fetchbuildingSchedules(); // Reload the table after deletion
                    },
                    error: function(xhr) {
                        alert(xhr.responseJSON.error || 'Terjadi kesalahan');
                    }
                });
            }
        });

        // Event listener for the toggleStatus button
        $(document).on('click', '.toggleStatus', function() {
            const buildingScheduleId = $(this).data('id');
            const buildingScheduleStatus = $(this).data('status');

            // Confirm before deletion
            if (confirm(`Ubah Ketersediaan Menjadi "${buildingScheduleStatus}" ?`)) {
                $.ajax({
                    url: `/buildingSchedules/${buildingScheduleId}/toggleStatus`,
                    method: 'POST',
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content') // Include CSRF token
                    },
                    success: function(response) {
                        // Show success message
                        // $('#success-message').removeClass('d-none').text(`Jadwal Gedung "${buildingScheduleStatus}" Berhasil!`);
                        $('#success-message').removeClass('d-none').text(response.success);

                        // Hide success message after 3 seconds
                        setTimeout(function() {
                            $('#success-message').addClass('d-none');
                        }, 3000);

                        fetchbuildingSchedules(); // Reload the table after deletion
                    },
                    error: function(xhr) {
                        alert(xhr.responseJSON.error || 'Terjadi kesalahan');
                    }
                });
            }
        });

        // Event listener for the booking button
        $(document).on('click', '.booking', function() {
            const buildingScheduleId = $(this).data('id');
            const buildingScheduleStatus = $(this).data('status');
            const buildingScheduleBooked = $(this).data('booked');

            // Confirm before deletion
            if (confirm(`Pesan Gedung?`)) {
                $.ajax({
                    url: `/buildingSchedules/${buildingScheduleId}/bookingGedung`,
                    method: 'POST',
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content') // Include CSRF token
                    },
                    success: function(response) {
                        // Show success message
                        // $('#success-message').removeClass('d-none').text(`Jadwal Gedung "${buildingScheduleStatus}" Berhasil!`);
                        $('#success-message').removeClass('d-none').text(response.success);

                        // Hide success message after 3 seconds
                        setTimeout(function() {
                            $('#success-message').addClass('d-none');
                        }, 3000);

                        fetchbuildingSchedules(); // Reload the table after deletion
                    },
                    error: function(xhr) {
                        alert(xhr.responseJSON.error || 'Terjadi kesalahan');
                    }
                });
            }
        });

    });
    document.addEventListener('DOMContentLoaded', function () {
        const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        const tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });

        const isBooked = document.getElementById('is_booked');
        const isAvailable = document.getElementById('is_available');

        isBooked.addEventListener('change', function () {
            if (this.value === "1") { // Jika Booked = Ya
                isAvailable.value = "0"; // Ubah Status menjadi Tidak Tersedia
            } else if (this.value === "0") { // Jika Booked = Tidak
                isAvailable.value = "1"; // Ubah Status menjadi Tersedia
            } else {
                isAvailable.value = ""; // Jika Semua, reset Status
            }
        });

        isAvailable.addEventListener('change', function () {
            if (this.value === "0") { // Jika Status = Tidak Tersedia
                isBooked.value = ""; // Ubah Booked menjadi Ya
            } else if (this.value === "1") { // Jika Status = Tersedia
                isBooked.value = ""; // Ubah Booked menjadi Tidak
            } else {
                isBooked.value = ""; // Jika Semua, reset Booked
            }
        });
    });
</script>
@endpush

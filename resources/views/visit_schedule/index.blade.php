@extends('layouts.app')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="mb-0">Jadwal Kunjungan</h1>
    </div>

    <!-- Filter, Sort, and Search Form -->
    <form id="filter-form" class="mb-4">
        <div class="row g-2 justify-content-end">
            <!-- Search Input -->
            <div class="col-md-8 position-relative">
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
            <div class="col-auto ms-auto">
                <input type="date" name="from_date" class="form-control form-control-sm" value="{{ $fromDate }}">
            </div>
            <div class="col-auto">
                -
            </div>
            <div class="col-auto">
                <input type="date" name="to_date" class="form-control form-control-sm" value="{{ $toDate }}">
            </div>
        </div>

        <!-- Sorting and Pagination -->
        <div class="col-md-12 mt-3 justify-content-end">
            <div class="row align-items-center g-2">
                <!-- Sort By -->
                <div class="col-auto">
                    <label for="sort_by" class="form-label mb-0" title="Urutkan Berdasarkan">
                        <i class="fas fa-sort-amount-down"></i> Urutkan
                    </label>
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

        <!-- Additional Filters -->
        <div class="col-md-12 mt-3 text-justify">
            <div class="row align-items-center g-2">
                <!-- Status Filter -->
                <div class="col-auto ms-auto">
                    <label for="is_available" class="form-label mb-0" title="Status Ketersesiaan">
                        {{-- <i class="fas fa-toggle-on"></i> --}}
                        Status
                    </label>
                </div>
                <div class="col-auto">
                    <select name="is_available" id="is_available" class="form-select form-select-sm">
                        <option value="" selected>Semua</option>
                        <option value="1">Tersedia</option>
                        <option value="0">Tidak Tersedia</option>
                    </select>
                </div>

                <!-- Loop for Other Filters -->
                @foreach ([
                    ['is_booked', 'fas fa-bookmark', 'Booked'],
                    ['t_g_req', 'fas fa-users', 'Request'],
                    ['t_g_assign', 'fas fa-user-check', 'Assign'],
                    ['is_confirm', 'fas fa-check-double', 'Confirm']
                ] as $filter)
                    <div class="col-auto">
                        <span class="text-muted mx-2">|</span>
                    </div>

                    <div class="col-auto">
                        <label for="{{ $filter[0] }}" class="form-label mb-0" title="{{ $filter[2] }}">
                            {{-- <i class="{{ $filter[1] }}"></i> --}}
                            {{ $filter[2] }}
                        </label>
                    </div>
                    <div class="col-auto">
                        <select name="{{ $filter[0] }}" id="{{ $filter[0] }}" class="form-select form-select-sm">
                            <option value="" selected>Semua</option>
                            <option value="1">Ya</option>
                            <option value="0">Tidak</option>
                        </select>
                    </div>
                @endforeach
            </div>
        </div>
    </form>


    <!-- Success Message -->
    <div id="success-message" class="alert alert-success d-none" role="alert">
        Data Jadwal Kunjungan berhasil di simpan!
    </div>

    <!-- Table Container for AJAX -->
    <div id="table-container">
        @include('visit_schedule.table')
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
                            <label for="visitor_company" class="form-label">Nama Instansi Pengunjung</label>
                            <input type="text" id="buildingSchedule-company" name ="visitor_company" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label for="visitor_name" class="form-label">Name Pengunjung</label>
                            <input type="text" id="buildingSchedule-visitor_name" name ="visitor_name" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label for="visitor_purphose" class="form-label">Tujuan Kunjungan</label>
                            <input type="text" id="buildingSchedule-purphose" name ="visitor_purphose" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label for="visitor_contact" class="form-label">Kontak</label>
                            <input type="text" id="buildingSchedule-contact" name ="visitor_contact" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label for="visitor_person" class="form-label">Jumlah Pengunjung</label>
                            <input type="number" id="buildingSchedule-person" name ="visitor_person" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label for="visitor_jumlah_kendaraan" class="form-label">Catatan</label>
                            <input type="text" id="buildingSchedule-jumlah_kendaraan" name ="visitor_jumlah_kendaraan" class="form-control" required>
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
        function fetchvisitSchedules() {
            const formData = $('#filter-form').serialize();

            $.ajax({
                url: "{{ route('visitSchedules.index') }}",
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
            fetchvisitSchedules();
        });

        // Event listener for changes in form inputs
        $('#filter-form').on('change', 'select', function() {
            fetchvisitSchedules();
        });

        // Trigger search when typing in search input
        $('#filter-form').on('keyup', '#search', function() {
            fetchvisitSchedules();
        });

        // Event listener for changes in date inputs
        $('#filter-form').on('change', 'input[type="date"]', function() {
            fetchvisitSchedules();  // Fetch data on any form input change
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

        // Open the Edit user form
        $(document).on('click', '.booking_form', function() {
            const buildingScheduleId = $(this).data('id');
            const company = $(this).data('company');
            const visitor_name = $(this).data('visitor_name');
            const purphose = $(this).data('purphose');
            const contact = $(this).data('contact');
            const person = $(this).data('person');
            const jumlah_kendaraan = $(this).data('jumlah_kendaraan');

            $('#buildingScheduleModalLabel').text('Form Reservasi Kunjungan');
            $('#buildingSchedule-id').val(buildingScheduleId);
            $('#buildingSchedule-company').val(company);
            $('#buildingSchedule-visitor_name').val(visitor_name);
            $('#buildingSchedule-purphose').val(purphose);
            $('#buildingSchedule-contact').val(contact);
            $('#buildingSchedule-person').val(person);
            $('#buildingSchedule-jumlah_kendaraan').val(jumlah_kendaraan);

            $('#buildingScheduleModal').modal('show');
        });

        // Save or update the user
        $('#save-user').on('click', function() {
            const formData = $('#buildingSchedule-form').serialize();
            const visitScheduleId = $('#buildingSchedule-id').val();

            const url = `/visitSchedules/${visitScheduleId}`;
            const method = 'PUT';

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
                    fetchvisitSchedules();  // Reload the table after saving
                },
                error: function(xhr) {
                    alert(xhr.responseJSON.error || 'Terjadi kesalahan');
                }
            });
        });

        // Event listener for the delete button
        $(document).on('click', '.delete-buildingSchedule', function() {
            const visitScheduleId = $(this).data('id');
            const buildingScheduleTrxNo = $(this).data('name');

            // Confirm before deletion
            if (confirm(`anda yakin ingin menghapus Jadwal Kunjungan "${buildingScheduleTrxNo}"?`)) {
                $.ajax({
                    url: `/visitSchedules/${visitScheduleId}`,
                    method: 'DELETE',
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content') // Include CSRF token
                    },
                    success: function(response) {
                        // Show success message
                        // $('#success-message').removeClass('d-none').text('Data Jadwal Kunjungan berhasil di hapus!');
                        $('#success-message').removeClass('d-none').text(response.success);


                        // Hide success message after 3 seconds
                        setTimeout(function() {
                            $('#success-message').addClass('d-none');
                        }, 3000);

                        fetchvisitSchedules(); // Reload the table after deletion
                    },
                    error: function(xhr) {
                        alert(xhr.responseJSON.error || 'Terjadi kesalahan');
                    }
                });
            }
        });

        // Event listener for the toggleStatus button
        $(document).on('click', '.toggleStatus', function() {
            const visitScheduleId = $(this).data('id');
            const visitSchedulestatus = $(this).data('status');

            // Confirm before deletion
            if (confirm(`Ubah Ketersediaan Menjadi "${visitSchedulestatus}" ?`)) {
                $.ajax({
                    url: `/visitSchedules/${visitScheduleId}/toggleStatus`,
                    method: 'POST',
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content') // Include CSRF token
                    },
                    success: function(response) {
                        // Show success message
                        // $('#success-message').removeClass('d-none').text(`Jadwal Kunjungan "${visitSchedulestatus}" Berhasil!`);
                        $('#success-message').removeClass('d-none').text(response.success);

                        // Hide success message after 3 seconds
                        setTimeout(function() {
                            $('#success-message').addClass('d-none');
                        }, 3000);

                        fetchvisitSchedules(); // Reload the table after deletion
                    },
                    error: function(xhr) {
                        alert(xhr.responseJSON.error || 'Terjadi kesalahan');
                    }
                });
            }
        });

        // Event listener for the booking button
        $(document).on('click', '.cancel_booking', function() {
            const visitScheduleId = $(this).data('id');
            const visitSchedulestatus = $(this).data('status');
            const buildingScheduleBooked = $(this).data('booked');

            // Confirm before deletion
            if (confirm(`"${buildingScheduleBooked}"?`)) {
                $.ajax({
                    url: `/visitSchedules/${visitScheduleId}/cancelBooking`,
                    method: 'POST',
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content') // Include CSRF token
                    },
                    success: function(response) {
                        // Show success message
                        // $('#success-message').removeClass('d-none').text(`Jadwal Kunjungan "${visitSchedulestatus}" Berhasil!`);
                        $('#success-message').removeClass('d-none').text(response.success);

                        // Hide success message after 3 seconds
                        setTimeout(function() {
                            $('#success-message').addClass('d-none');
                        }, 3000);

                        fetchvisitSchedules(); // Reload the table after deletion
                    },
                    error: function(xhr) {
                        alert(xhr.responseJSON.error || 'Terjadi kesalahan');
                    }
                });
            }
        });

        // Event listener for the request-tour-guide button
        $(document).on('click', '.request-tour-guide', function() {
            const visitScheduleId = $(this).data('id');
            const visitSchedulereqTourGuide = $(this).data('status');

            // Confirm before deletion
            if (confirm(`"${visitSchedulereqTourGuide}" Tour Guide ?`)) {
                $.ajax({
                    url: `/visitSchedules/${visitScheduleId}/ReqTourGuide`,
                    method: 'POST',
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content') // Include CSRF token
                    },
                    success: function(response) {
                        // Show success message
                        // $('#success-message').removeClass('d-none').text(`Jadwal Kunjungan "${visitSchedulestatus}" Berhasil!`);
                        $('#success-message').removeClass('d-none').text(response.success);

                        // Hide success message after 3 seconds
                        setTimeout(function() {
                            $('#success-message').addClass('d-none');
                        }, 3000);

                        fetchvisitSchedules(); // Reload the table after deletion
                    },
                    error: function(xhr) {
                        alert(xhr.responseJSON.error || 'Terjadi kesalahan');
                    }
                });
            }
        });

        // Event listener for the confirm-visit button
        $(document).on('click', '.confirm-visit', function() {
            const visitScheduleId = $(this).data('id');
            const visitScheduleConfirm = $(this).data('status');

            // Confirm before deletion
            if (confirm(`"${visitScheduleConfirm}" Kunjungan ?`)) {
                $.ajax({
                    url: `/visitSchedules/${visitScheduleId}/ConfirmVisit`,
                    method: 'POST',
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content') // Include CSRF token
                    },
                    success: function(response) {
                        // Show success message
                        // $('#success-message').removeClass('d-none').text(`Jadwal Kunjungan "${visitSchedulestatus}" Berhasil!`);
                        $('#success-message').removeClass('d-none').text(response.success);

                        // Hide success message after 3 seconds
                        setTimeout(function() {
                            $('#success-message').addClass('d-none');
                        }, 3000);

                        fetchvisitSchedules(); // Reload the table after deletion
                    },
                    error: function(xhr) {
                        alert(xhr.responseJSON.error || 'Terjadi kesalahan');
                    }
                });
            }
        });

    });
    document.addEventListener('DOMContentLoaded', function () {
         // Tooltip initialization
        const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        const tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });

        const isBooked = document.getElementById('is_booked');
        const isAvailable = document.getElementById('is_available');
        const t_g_req = document.getElementById('t_g_req');
        const t_g_assign = document.getElementById('t_g_assign');
        const is_confirm = document.getElementById('is_confirm');

        isAvailable.addEventListener('change', function () {
            if (this.value === "0") { // Jika Status = Tidak Tersedia
                isBooked.value = ""; // Ubah Booked menjadi Ya
                t_g_req.value = "";
                t_g_assign.value = "";
                is_confirm.value = "";
            } else if (this.value === "1") { // Jika Status = Tersedia
                isBooked.value = ""; // Ubah Booked menjadi Tidak
                t_g_req.value = "";
                t_g_assign.value = "";
                is_confirm.value = "";
            } else {
                isBooked.value = ""; // Jika Semua, reset Booked
                t_g_req.value = "";
                t_g_assign.value = "";
                is_confirm.value = "";
            }
        });

        isBooked.addEventListener('change', function () {
            if (this.value === "1") { // Jika Booked = Ya
                isAvailable.value = "0"; // Ubah Status menjadi Tidak Tersedia
                t_g_req.value = "";
                t_g_assign.value = "";
                is_confirm.value = "";
            } else if (this.value === "0") { // Jika Booked = Tidak
                isAvailable.value = "1"; // Ubah Status menjadi Tersedia
                t_g_req.value = "";
                t_g_assign.value = "";
                is_confirm.value = "";
            } else {
                isAvailable.value = ""; // Jika Semua, reset Status
                t_g_req.value = "";
                t_g_assign.value = "";
                is_confirm.value = "";
            }
        });

        t_g_req.addEventListener('change', function () {
            if (this.value === "1") { // Jika Booked = Ya
                isBooked.value = "1"; // Ubah Status menjadi Tidak Tersedia
                t_g_assign.value = "";
                is_confirm.value = "";
            } else if (this.value === "0") { // Jika Booked = Tidak
                isBooked.value = "1"; // Ubah Status menjadi Tersedia
                t_g_assign.value = "";
                is_confirm.value = "";
            } else {
                t_g_assign.value = "";
                is_confirm.value = "";
            }
        });

        t_g_assign.addEventListener('change', function () {
            if (this.value === "1") { // Jika Booked = Ya
                isBooked.value = "1"; // Ubah Status menjadi Tidak Tersedia
                t_g_req.value = "1";
                is_confirm.value = "";
            } else if (this.value === "0") { // Jika Booked = Tidak
                isBooked.value = "1"; // Ubah Status menjadi Tersedia
                t_g_req.value = "1";
                is_confirm.value = "";
            } else {
                is_confirm.value = "";
            }
        });

        is_confirm.addEventListener('change', function () {
            if (this.value === "1") { // Jika Booked = Ya
                isBooked.value = "1"; // Ubah Status menjadi Tidak Tersedia
                t_g_req.value = "1";
                t_g_assign.value = "1";
            } else if (this.value === "0") { // Jika Booked = Tidak
                isBooked.value = "1"; // Ubah Status menjadi Tersedia
                t_g_req.value = "1";
                t_g_assign.value = "1";
            } else {

            }
        });
    });

</script>
@endpush

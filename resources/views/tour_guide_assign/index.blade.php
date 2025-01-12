@extends('layouts.app')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="mb-0">Penunjukan Tour Guide</h1>
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
                    <label for="t_g_assign" class="form-label mb-0" title="Assign">
                        {{-- <i class="fas fa-toggle-on"></i> --}}
                        Assign
                    </label>
                </div>
                <div class="col-auto">
                    <select name="t_g_assign" id="t_g_assign" class="form-select form-select-sm">
                        <option value="" selected>Semua</option>
                        <option value="1">Ya</option>
                        <option value="0">Tidak</option>
                    </select>
                </div>

                <div class="col-auto">
                    <label for="is_confirm" class="form-label mb-0" title="Confirm">
                        {{-- <i class="fas fa-toggle-on"></i> --}}
                        Confirm
                    </label>
                </div>
                <div class="col-auto">
                    <select name="is_confirm" id="is_confirm" class="form-select form-select-sm">
                        <option value="" selected>Semua</option>
                        <option value="1">Ya</option>
                        <option value="0">Tidak</option>
                    </select>
                </div>
            </div>
        </div>
    </form>

    <!-- Success Message -->
    <div id="success-message" class="alert alert-success d-none" role="alert">
        Data Penunjukan Tour Guide berhasil di simpan!
    </div>

    <!-- Table Container for AJAX -->
    <div id="table-container">
        @include('tour_guide_assign.table')
    </div>

    <!-- Modal for Create/Edit User Form -->
    <div class="modal fade" id="TourGuideAssignModal" tabindex="-1" aria-labelledby="TourGuideAssignModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="TourGuideAssignModalLabel">Form Pilih Tour Guide</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="TourGuideAssign-form">
                        @csrf
                        <input type="hidden" name="id" id="TourGuideAssign-id">
                        <div class="mb-3">
                            <label for="TourGuideAssign-tour_guide_id" class="form-label">Nama Tour Guide</label>
                            <select id="TourGuideAssign-tour_guide_id" name="tour_guide_id" class="form-select" required>
                                <option selected>Pilih Tour Guide</option>
                                @foreach($tourguides as $tourguide)
                                    <option value="{{ $tourguide->id }}">{{ $tourguide->name }}</option>
                                @endforeach
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
        function fetchTourGuideAssign() {
            const formData = $('#filter-form').serialize();

            $.ajax({
                url: "{{ route('assignTourGuides.index') }}",
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
            fetchTourGuideAssign();
        });

        // Event listener for changes in form inputs
        $('#filter-form').on('change', 'select', function() {
            fetchTourGuideAssign();
        });

        // Trigger search when typing in search input
        $('#filter-form').on('keyup', '#search', function() {
            fetchTourGuideAssign();
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
        $(document).on('click', '.add-TourGuideAssign', function() {
            const TourGuideAssignId = $(this).data('id');
            $('#TourGuideAssignModalLabel').text('Pilih Tour Guide');
            $('#TourGuideAssign-form')[0].reset(); // Clear form
            $('#TourGuideAssign-id').val(TourGuideAssignId); // Clear hidden id field
            $('#TourGuideAssignModal').modal('show');
        });

        // Open the Edit user form
        $(document).on('click', '.edit-TourGuideAssign', function() {
            const TourGuideAssignId = $(this).data('id');
            const tour_guide_id = $(this).data('tourguideid');
            const TourGuideAssignStatus = $(this).data('status');

            $('#TourGuideAssignModalLabel').text('Ubah Pilih Tour Guide');
            $('#TourGuideAssign-id').val(TourGuideAssignId);
            $('#TourGuideAssign-tour_guide_id').val(tour_guide_id);

            $('#TourGuideAssignModal').modal('show');
        });

        // Save or update the user
        $('#save-user').on('click', function() {
            const formData = $('#TourGuideAssign-form').serialize();
            const TourGuideAssignId = $('#TourGuideAssign-id').val();

            const url = TourGuideAssignId ? `/assignTourGuides/${TourGuideAssignId}` : '/assignTourGuides';
            const method = TourGuideAssignId ? 'PUT' : 'POST';

            $.ajax({
                url: url,
                method: method,
                data: formData,
                success: function(response) {
                    // Show success message
                    // $('#success-message').removeClass('d-none').text('Data Pilih Tour Guide Berhasil Di Simpan!');
                    $('#success-message').removeClass('d-none').text(response.success);

                    // Hide success message after 3 seconds
                    setTimeout(function() {
                        $('#success-message').addClass('d-none');
                    }, 3000);

                    $('#TourGuideAssignModal').modal('hide'); // Close the modal
                    fetchTourGuideAssign();  // Reload the table after saving
                },
                error: function(xhr) {
                    alert(xhr.responseJSON.error || 'Terjadi kesalahan');
                }
            });
        });

        // Event listener for the delete button
        $(document).on('click', '.delete-TourGuideAssign', function() {
            const visitScheduleId = $(this).data('id');

            // Confirm before deletion
            if (confirm(`anda yakin ingin mebatalkan Penunjukan Tour Guide?`)) {
                $.ajax({
                    url: `/assignTourGuides/${visitScheduleId}`,
                    method: 'DELETE',
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content') // Include CSRF token
                    },
                    success: function(response) {
                        // Show success message
                        // $('#success-message').removeClass('d-none').text('Data Penunjukan Tour Guide berhasil di hapus!');
                        $('#success-message').removeClass('d-none').text(response.success);


                        // Hide success message after 3 seconds
                        setTimeout(function() {
                            $('#success-message').addClass('d-none');
                        }, 3000);

                        fetchTourGuideAssign(); // Reload the table after deletion
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

        const t_g_assign = document.getElementById('t_g_assign');
        const is_confirm = document.getElementById('is_confirm');

        t_g_assign.addEventListener('change', function () {
            if (this.value === "1") { // Jika Booked = Ya
                is_confirm.value = "";
            } else if (this.value === "0") { // Jika Booked = Tidak
                is_confirm.value = "";
            } else {
                is_confirm.value = "";
            }
        });

        is_confirm.addEventListener('change', function () {
            if (this.value === "1") { // Jika Booked = Ya
                t_g_assign.value = "1";
            } else if (this.value === "0") { // Jika Booked = Tidak
                t_g_assign.value = "1";
            } else {

            }
        });
    });
</script>
@endpush

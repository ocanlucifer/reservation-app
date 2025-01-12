@extends('layouts.app')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="mb-0">Laporan Kunjungan</h1>
        <div>
            <button class="btn btn-success btn-sm me-2" id="exportExcel">
                <i class="bi bi-file-earmark-excel"></i> Export Excel
            </button>
            <button class="btn btn-danger btn-sm" id="exportPDF">
                <i class="bi bi-file-earmark-pdf"></i> Export PDF
            </button>
        </div>
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
        @include('report.table')
    </div>


@endsection

@push('scripts')
<script>
    $(document).ready(function() {

        // Function to fetch users data
        function fetchvisitSchedules() {
            const formData = $('#filter-form').serialize();

            $.ajax({
                url: "{{ route('report.index') }}",
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

        $('#exportExcel').on('click', function () {
            const formData = $('#filter-form').serialize(); // Ambil filter
            const url = "{{ route('report.export.excel') }}?" + formData; // Tambahkan filter ke URL

            window.location.href = url; // Redirect ke rute ekspor
        });

        $('#exportPDF').on('click', function () {
            const formData = $('#filter-form').serialize(); // Ambil filter
            const url = "{{ route('report.export.pdf') }}?" + formData; // Tambahkan filter ke URL

            window.location.href = url; // Redirect ke rute ekspor
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

@extends('layouts.app')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="mb-0">Kelola Gedung</h1>
        <button class="btn btn-primary btn-sm" id="open-create-form">
            <i class="bi bi-plus-lg" style="font-size: 1rem;"></i> Tambah Gedung
        </button>
    </div>

    <!-- Filter, Sort, and Search Form -->
    <form id="filter-form" class="mb-4">
        <div class="row g-2 justify-content-end">
            <div class="col-md-3 col-sm-12">
                <input type="text" name="search" class="form-control form-control-sm" placeholder="Cari nama gedung" id="search" value="{{ $search }}">
            </div>
            {{-- <div class="col-md-2 col-sm-6"> --}}
                <select name="sort_by" id="sort_by" class="form-select form-select-sm" hidden>
                    <option value="name" {{ $sortBy == 'name' ? 'selected' : '' }}>Name</option>
                    <option value="is_active" {{ $sortBy == 'is_active' ? 'selected' : '' }}>Status</option>
                    <option value="created_at" {{ $sortBy == 'created_at' ? 'selected' : '' }}>Created Date</option>
                    <option value="updated_at" {{ $sortBy == 'updated_at' ? 'selected' : '' }}>Updated Date</option>
                </select>
            {{-- </div>
            <div class="col-md-2 col-sm-6"> --}}
                <select name="order" id="order" class="form-select form-select-sm" hidden>
                    <option value="asc" {{ $order == 'asc' ? 'selected' : '' }}>Ascending</option>
                    <option value="desc" {{ $order == 'desc' ? 'selected' : '' }}>Descending</option>
                </select>
            {{-- </div> --}}
            <div class="col-md-2 col-sm-6">
                <select name="per_page" id="per_page" class="form-select form-select-sm">
                    <option value="5" {{ $perPage == 5 ? 'selected' : '' }}>5 per page</option>
                    <option value="10" {{ $perPage == 10 ? 'selected' : '' }}>10 per page</option>
                    <option value="15" {{ $perPage == 15 ? 'selected' : '' }}>15 per page</option>
                </select>
            </div>
        </div>
    </form>

    <!-- Success Message -->
    <div id="success-message" class="alert alert-success d-none" role="alert">
        Data Gedung berhasil di simpan!
    </div>

    <!-- Table Container for AJAX -->
    <div id="table-container">
        @include('building.table')
    </div>

    <!-- Modal for Create/Edit User Form -->
    <div class="modal fade" id="buildingModal" tabindex="-1" aria-labelledby="buildingModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="buildingModalLabel">Form Gedung</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="building-form">
                        @csrf
                        <input type="hidden" name="id" id="building-id">
                        <div class="mb-3">
                            <label for="building-name" class="form-label">Nama</label>
                            <input type="text" class="form-control" id="building-name" name="name" required>
                        </div>
                        <div class="mb-3">
                            <label for="building-status" class="form-label">Status</label>
                            <select id="building-status" name="is_active" class="form-select" required>
                                <option value="1">Aktif</option>
                                <option value="0">Nonaktif</option>
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
        function fetchBuildings() {
            const formData = $('#filter-form').serialize();

            $.ajax({
                url: "{{ route('buildings.index') }}",
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
            fetchBuildings();
        });

        // Event listener for changes in form inputs
        $('#filter-form').on('change', 'select', function() {
            fetchBuildings();
        });

        // Trigger search when typing in search input
        $('#filter-form').on('keyup', '#search', function() {
            fetchBuildings();
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
            $('#buildingModalLabel').text('Buat Gedung');
            $('#building-form')[0].reset(); // Clear form
            $('#building-id').val(''); // Clear hidden id field
            $('#buildingModal').modal('show');
        });

        // Open the Edit user form
        $(document).on('click', '.edit-building', function() {
            const buildingId = $(this).data('id');
            const name = $(this).data('name');
            const buildingStatus = $(this).data('status');

            $('#buildingModalLabel').text('Ubah Gedung');
            $('#building-id').val(buildingId);
            $('#building-name').val(name);
            $('#building-status').val(buildingStatus);

            $('#buildingModal').modal('show');
        });

        // Save or update the user
        $('#save-user').on('click', function() {
            const formData = $('#building-form').serialize();
            const buildingId = $('#building-id').val();

            const url = buildingId ? `/buildings/${buildingId}` : '/buildings';
            const method = buildingId ? 'PUT' : 'POST';

            $.ajax({
                url: url,
                method: method,
                data: formData,
                success: function(response) {
                    // Show success message
                    $('#success-message').removeClass('d-none').text('Data Gedung Berhasil Di Simpan!');

                    // Hide success message after 3 seconds
                    setTimeout(function() {
                        $('#success-message').addClass('d-none');
                    }, 3000);

                    $('#buildingModal').modal('hide'); // Close the modal
                    fetchBuildings();  // Reload the table after saving
                },
                error: function(xhr) {
                    alert('Terjadi kesalahan ketika menyimpan data Gedung.');
                }
            });
        });

        // Event listener for the delete button
        $(document).on('click', '.delete-building', function() {
            const buildingId = $(this).data('id');
            const buildingName = $(this).data('name');

            // Confirm before deletion
            if (confirm(`anda yakin ingin menghapus Gedung "${buildingName}"?`)) {
                $.ajax({
                    url: `/buildings/${buildingId}`,
                    method: 'DELETE',
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content') // Include CSRF token
                    },
                    success: function(response) {
                        // Show success message
                        $('#success-message').removeClass('d-none').text('Data Gedung berhasil di hapus!');

                        // Hide success message after 3 seconds
                        setTimeout(function() {
                            $('#success-message').addClass('d-none');
                        }, 3000);

                        fetchBuildings(); // Reload the table after deletion
                    },
                    error: function(xhr) {
                        alert('Terjadi kesalahan ketika menghapus data Gedung.');
                    }
                });
            }
        });

        // Event listener for the toggleStatus button
        $(document).on('click', '.toggleStatus', function() {
            const buildingId = $(this).data('id');
            const buildingName = $(this).data('name');
            const buildingStatus = $(this).data('status');

            // Confirm before deletion
            if (confirm(`anda yakin ingin mengubah status "${buildingStatus}" dari Gedung "${buildingName}"?`)) {
                $.ajax({
                    url: `/buildings/${buildingId}/toggleStatus`,
                    method: 'POST',
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content') // Include CSRF token
                    },
                    success: function(response) {
                        // Show success message
                        $('#success-message').removeClass('d-none').text(`Gedung "${buildingStatus}" Berhasil!`);

                        // Hide success message after 3 seconds
                        setTimeout(function() {
                            $('#success-message').addClass('d-none');
                        }, 3000);

                        fetchBuildings(); // Reload the table after deletion
                    },
                    error: function(xhr) {
                        alert('Terjadi kesalahan ketika mengubah status Gedung.');
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
    });
</script>
@endpush

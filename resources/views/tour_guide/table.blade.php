<div class="table-responsive">
    <table class="table table-bordered table-striped table-hover table-sm">
        <thead class="table-dark">
            <tr>
                <th class="col-0">No.</th>
                <th class="col-2">
                    <a href="#" class="sortable nav-link" data-sort-by="name" data-order="{{ $order === 'asc' ? 'desc' : 'asc' }}">
                        Nama
                        @if ($sortBy === 'name')
                            <i class="fas fa-sort-{{ $order === 'asc' ? 'down' : 'up' }}"></i>
                        @endif
                    </a>
                </th>
                <th class="col-2">Entry User</th>
                <th class="col-2">
                    <a href="#" class="sortable nav-link" data-sort-by="created_at" data-order="{{ $order === 'asc' ? 'desc' : 'asc' }}">
                        Tanggal Entry
                        @if ($sortBy === 'created_at')
                            <i class="fas fa-sort-{{ $order === 'asc' ? 'down' : 'up' }}"></i>
                        @endif
                    </a>
                </th>
                <th class="col-1 text-center">Update User</th>
                <th class="col-2">
                    <a href="#" class="sortable nav-link" data-sort-by="updated_at" data-order="{{ $order === 'asc' ? 'desc' : 'asc' }}">
                        Tanggal Edit
                        @if ($sortBy === 'updated_at')
                            <i class="fas fa-sort-{{ $order === 'asc' ? 'down' : 'up' }}"></i>
                        @endif
                    </a>
                </th>
                <th class="col-1 text-center">
                    <a href="#" class="sortable nav-link" data-sort-by="is_active" data-order="{{ $order === 'asc' ? 'desc' : 'asc' }}">
                        Status
                        @if ($sortBy === 'is_active')
                            <i class="fas fa-sort-{{ $order === 'asc' ? 'down' : 'up' }}"></i>
                        @endif
                    </a>
                </th>
                <th class="col-2 text-center">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($tourGuides as $tourGuide)
            <tr>
                <td class="text-center">{{ $loop->iteration }}</td>
                <td>{{ $tourGuide->name }}</td>
                <td>{{ $tourGuide->creator->name }}</td>
                <td>{{ $tourGuide->created_at }}</td>
                <td>{{ $tourGuide->update_by ? $tourGuide->updater->name : '' }}</td>
                <td>{{ $tourGuide->update_by ? $tourGuide->updated_at : '' }}</td>
                <td class="text-center">
                    @if ($tourGuide->is_active)
                        <span class="badge bg-success">Aktif</span>
                    @else
                        <span class="badge bg-danger">Nonaktif</span>
                    @endif
                </td>
                <td class="text-center">
                    <!-- Edit Button -->
                    <button class="btn btn-warning btn-sm edit-tourGuide" data-id="{{ $tourGuide->id }}" data-name="{{ $tourGuide->name }}" data-status="{{ $tourGuide->is_active }}" data-bs-toggle="tooltip" title="Ubah">
                        <i class="bi bi-pencil-square"></i>
                    </button>

                    <!-- Delete Button -->
                    <button class="btn btn-danger btn-sm delete-tourGuide" data-id="{{ $tourGuide->id }}" data-name="{{ $tourGuide->name }}" data-bs-toggle="tooltip" title="Hapus">
                        <i class="bi bi-trash"></i>
                    </button>

                    <!-- Toggle Active/Deactivate Button -->
                    <button type="submit" class="btn btn-sm {{ $tourGuide->is_active ? 'btn-secondary' : 'btn-success' }} toggleStatus" data-id="{{ $tourGuide->id }}" data-name="{{ $tourGuide->name }}" data-status="{{ $tourGuide->is_active ? 'Deactivate' : 'Activate' }}" data-bs-toggle="tooltip" title="{{ $tourGuide->is_active ? 'Nonaktifkan' : 'Aktifkan' }}">
                        <i class="bi {{ $tourGuide->is_active ? 'bi-toggle-on' : 'bi-toggle-off' }}"></i>
                    </button>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

<!-- Pagination Links -->

<div class="d-flex justify-content-between align-items-center mt-3">
    <!-- Showing results text on the left -->
    <span class="text-muted">
        Menampilkan {{ $tourGuides->firstItem() }} sampai {{ $tourGuides->lastItem() }} dari {{ $tourGuides->total() }} Tour Guide
    </span>

    <!-- Pagination links on the right -->
    <div>
        {!! $tourGuides->links('pagination::bootstrap-5') !!}
    </div>
</div>


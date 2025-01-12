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
            @foreach ($buildings as $building)
            <tr>
                <td class="text-center">{{ $loop->iteration }}</td>
                <td>{{ $building->name }}</td>
                <td>{{ $building->creator->name }}</td>
                <td>{{ $building->created_at }}</td>
                <td>{{ $building->update_by ? $building->updater->name : '' }}</td>
                <td>{{ $building->update_by ? $building->updated_at : '' }}</td>
                <td class="text-center">
                    @if ($building->is_active)
                        <span class="badge bg-success">Aktif</span>
                    @else
                        <span class="badge bg-danger">Nonaktif</span>
                    @endif
                </td>
                <td class="text-center">
                    <!-- Edit Button -->
                    <button class="btn btn-warning btn-sm edit-building" data-id="{{ $building->id }}" data-name="{{ $building->name }}" data-status="{{ $building->is_active }}" data-bs-toggle="tooltip" title="Ubah">
                        <i class="bi bi-pencil-square"></i>
                    </button>

                    <!-- Delete Button -->
                    <button class="btn btn-danger btn-sm delete-building" data-id="{{ $building->id }}" data-name="{{ $building->name }}" data-bs-toggle="tooltip" title="Hapus">
                        <i class="bi bi-trash"></i>
                    </button>

                    <!-- Toggle Active/Deactivate Button -->
                    <button type="submit" class="btn btn-sm {{ $building->is_active ? 'btn-secondary' : 'btn-success' }} toggleStatus" data-id="{{ $building->id }}" data-name="{{ $building->name }}" data-status="{{ $building->is_active ? 'Deactivate' : 'Activate' }}" data-bs-toggle="tooltip" title="{{ $building->is_active ? 'Nonaktifkan' : 'Aktifkan' }}">
                        <i class="bi {{ $building->is_active ? 'bi-toggle-on' : 'bi-toggle-off' }}"></i>
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
        Menampilkan {{ $buildings->firstItem() }} sampai {{ $buildings->lastItem() }} dari {{ $buildings->total() }} Gedung
    </span>

    <!-- Pagination links on the right -->
    <div>
        {!! $buildings->links('pagination::bootstrap-5') !!}
    </div>
</div>


@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-6">
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-sitemap"></i> Daftar Bidang
            </h1>
            <p class="text-muted">Manajemen data bidang kerja</p>
        </div>
        <div class="col-md-6 text-end">
            <a href="{{ route('bidang.create') }}" class="btn btn-primary">
                <i class="fas fa-plus-circle"></i> Tambah Bidang
            </a>
            <a href="{{ route('bidang.trashed') }}" class="btn btn-secondary">
                <i class="fas fa-trash-restore"></i> Data Terhapus
            </a>
        </div>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle"></i> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-table"></i> Tabel Bidang
            </h6>
            <div class="dropdown">
                <button class="btn btn-sm btn-outline-primary dropdown-toggle" type="button" 
                        data-bs-toggle="dropdown">
                    <i class="fas fa-cog"></i> Aksi
                </button>
                <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="#" onclick="submitBulkAction('delete')">
                        <i class="fas fa-trash text-danger"></i> Hapus Terpilih
                    </a></li>
                    <li><a class="dropdown-item" href="#">
                        <i class="fas fa-download"></i> Export
                    </a></li>
                </ul>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover" id="dataTable" width="100%" cellspacing="0">
                    <thead class="table-dark">
                        <tr>
                            <th width="50">
                                <input type="checkbox" id="select-all" class="form-check-input">
                            </th>
                            <th width="60">No</th>
                            <th>Nama Bidang</th>
                            <th>Slug</th>
                            <th>Route Name</th>
                            <th>Detail</th>
                            <th width="120" class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($bidangs as $key => $bidang)
                        <tr>
                            <td>
                                <input type="checkbox" name="ids[]" value="{{ $bidang->id_bidang }}" 
                                       class="form-check-input select-item">
                            </td>
                            <td class="text-center">{{ $loop->iteration }}</td>
                            <td>
                                <strong>{{ $bidang->nama_bidang }}</strong>
                            </td>
                            <td>
                                <span class="badge bg-light text-dark border">
                                    <code>{{ $bidang->slug }}</code>
                                </span>
                            </td>
                            <td>
                                <span class="badge bg-info text-white">
                                    <i class="fas fa-route"></i> {{ $bidang->route_name }}
                                </span>
                            </td>
                            <td>
                                @if($bidang->detail_bidang)
                                    <span class="text-muted">
                                        <i class="fas fa-info-circle"></i> 
                                        {{ Str::limit($bidang->detail_bidang, 40) }}
                                    </span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm" role="group">
                                    <a href="{{ route('bidang.show', $bidang->id_bidang) }}" 
                                       class="btn btn-info" title="Detail" data-bs-toggle="tooltip">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('bidang.edit', $bidang->id_bidang) }}" 
                                       class="btn btn-warning" title="Edit" data-bs-toggle="tooltip">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('bidang.destroy', $bidang->id_bidang) }}" 
                                          method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger" 
                                                title="Hapus" data-bs-toggle="tooltip"
                                                onclick="return confirm('Hapus {{ $bidang->nama_bidang }}?')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-4">
                                <div class="text-muted">
                                    <i class="fas fa-inbox fa-2x mb-2"></i>
                                    <p>Tidak ada data bidang</p>
                                    <a href="{{ route('bidang.create') }}" class="btn btn-primary btn-sm">
                                        <i class="fas fa-plus"></i> Tambah Data Pertama
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($bidangs->count() > 0)
            <div class="d-flex justify-content-between align-items-center mt-3">
                <div class="text-muted">
                    <i class="fas fa-database"></i> Total: {{ $bidangs->count() }} data
                </div>
                <div>
                    <form action="{{ route('bidang.bulk-action') }}" method="POST" id="bulk-form">
                        @csrf
                        <input type="hidden" name="action" id="bulk-action">
                        <input type="hidden" name="ids" id="bulk-ids">
                        
                        <button type="button" class="btn btn-danger btn-sm" 
                                onclick="submitBulkAction('delete')">
                            <i class="fas fa-trash"></i> Hapus Data Terpilih
                        </button>
                    </form>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

<style>
    .table th {
        background-color: #f8f9fc;
        font-weight: 600;
    }
    .table-hover tbody tr:hover {
        background-color: #f8f9fa;
    }
    .badge {
        font-family: 'SFMono-Regular', Menlo, Monaco, Consolas, monospace;
        font-size: 0.85em;
    }
    .btn-group .btn {
        border-radius: 4px !important;
        margin: 0 2px;
    }
</style>

<script>
    // Select all checkboxes
    document.getElementById('select-all').addEventListener('change', function(e) {
        const checkboxes = document.querySelectorAll('.select-item');
        checkboxes.forEach(checkbox => {
            checkbox.checked = e.target.checked;
        });
    });

    // Bulk action
    function submitBulkAction(action) {
        const checkboxes = document.querySelectorAll('.select-item:checked');
        const ids = Array.from(checkboxes).map(cb => cb.value);
        
        if (ids.length === 0) {
            Swal.fire({
                icon: 'warning',
                title: 'Peringatan',
                text: 'Pilih minimal satu data',
                timer: 2000
            });
            return;
        }

        Swal.fire({
            title: 'Konfirmasi',
            text: `Apakah Anda yakin ingin menghapus ${ids.length} data terpilih?`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('bulk-action').value = action;
                document.getElementById('bulk-ids').value = JSON.stringify(ids);
                document.getElementById('bulk-form').submit();
            }
        });
    }

    // Initialize tooltips
    document.addEventListener('DOMContentLoaded', function() {
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    });
</script>

<!-- Include SweetAlert2 for better alerts -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@endsection
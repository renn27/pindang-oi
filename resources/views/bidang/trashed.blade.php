@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row mb-4">
        <div class="col">
            <h1>Bidang yang Dihapus</h1>
        </div>
        <div class="col text-end">
            <a href="{{ route('bidang.index') }}" class="btn btn-primary">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            @if($bidangs->count() > 0)
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Nama Bidang</th>
                            <th>Slug</th>
                            <th>Dihapus Pada</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($bidangs as $bidang)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $bidang->nama_bidang }}</td>
                                <td><code>{{ $bidang->slug }}</code></td>
                                <td>{{ $bidang->deleted_at->format('d/m/Y H:i') }}</td>
                                <td>
                                    <div class="btn-group">
                                        <form action="{{ route('bidang.restore', $bidang->id_bidang) }}" 
                                              method="POST" style="display: inline;">
                                            @csrf
                                            <button type="submit" class="btn btn-success btn-sm"
                                                    onclick="return confirm('Pulihkan data ini?')">
                                                <i class="fas fa-undo"></i> Pulihkan
                                            </button>
                                        </form>
                                        <form action="{{ route('bidang.force-delete', $bidang->id_bidang) }}" 
                                              method="POST" style="display: inline;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm"
                                                    onclick="return confirm('Hapus permanen? Data tidak dapat dipulihkan.')">
                                                <i class="fas fa-trash-alt"></i> Hapus Permanen
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <div class="alert alert-info">
                    Tidak ada data yang dihapus.
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
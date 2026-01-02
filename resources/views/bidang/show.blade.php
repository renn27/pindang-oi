@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h3>Detail Bidang</h3>
                        <div class="btn-group">
                            <a href="{{ route('bidang.edit', $bidang->id_bidang) }}" class="btn btn-warning">
                                <i class="fas fa-edit"></i> Edit
                            </a>
                            <form action="{{ route('bidang.destroy', $bidang->id_bidang) }}" 
                                  method="POST" style="display: inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger"
                                        onclick="return confirm('Apakah Anda yakin?')">
                                    <i class="fas fa-trash"></i> Hapus
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <table class="table table-bordered">
                        <tr>
                            <th width="200">Nama Bidang</th>
                            <td>{{ $bidang->nama_bidang }}</td>
                        </tr>
                        <tr>
                            <th>Slug</th>
                            <td>
                                <code>{{ $bidang->slug }}</code>
                            </td>
                        </tr>
                        <tr>
                            <th>Route Name</th>
                            <td>
                                <code>{{ $bidang->route_name }}</code>
                            </td>
                        </tr>
                        <tr>
                            <th>URL</th>
                            <td>
                                <a href="{{ $bidang->url }}" target="_blank">
                                    {{ $bidang->url }}
                                </a>
                            </td>
                        </tr>
                        <tr>
                            <th>Detail Bidang</th>
                            <td>{{ $bidang->detail_bidang ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th>Dibuat</th>
                            <td>{{ $bidang->created_at->format('d/m/Y H:i') }}</td>
                        </tr>
                        <tr>
                            <th>Diupdate</th>
                            <td>{{ $bidang->updated_at->format('d/m/Y H:i') }}</td>
                        </tr>
                    </table>

                    <div class="mt-3">
                        <a href="{{ route('bidang.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Kembali ke Daftar
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
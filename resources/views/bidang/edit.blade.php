@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h3>Edit Bidang</h3>
                </div>
                <div class="card-body">
                    <form action="{{ route('bidang.update', $bidang->id_bidang) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="mb-3">
                            <label for="nama_bidang" class="form-label">Nama Bidang *</label>
                            <input type="text" class="form-control @error('nama_bidang') is-invalid @enderror" 
                                   id="nama_bidang" name="nama_bidang" 
                                   value="{{ old('nama_bidang', $bidang->nama_bidang) }}" required>
                            @error('nama_bidang')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="slug" class="form-label">Slug</label>
                            <input type="text" class="form-control @error('slug') is-invalid @enderror" 
                                   id="slug" name="slug" 
                                   value="{{ old('slug', $bidang->slug) }}">
                            @error('slug')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="route_name" class="form-label">Route Name</label>
                            <input type="text" class="form-control @error('route_name') is-invalid @enderror" 
                                   id="route_name" name="route_name" 
                                   value="{{ old('route_name', $bidang->route_name) }}">
                            @error('route_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="detail_bidang" class="form-label">Detail Bidang</label>
                            <textarea class="form-control @error('detail_bidang') is-invalid @enderror" 
                                      id="detail_bidang" name="detail_bidang" rows="3">{{ old('detail_bidang', $bidang->detail_bidang) }}</textarea>
                            @error('detail_bidang')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('bidang.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Kembali
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Update
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
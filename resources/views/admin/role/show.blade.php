@extends('layouts.app')

@section('content')
<!-- NEW FORM FOR ITEM INPUT -->
<div class="card mb-4">
    <div class="card-body">
        <div class="mb-3">
            <label class="form-label">Nama Peran (Role)</label>
            {{-- Diperbarui ke $role->nama_role --}}
            <input type="text" class="form-control" value="{{ $role->nama_role }}" readonly>
        </div>

        <a href="{{ route('role.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-1"></i> Kembali
        </a>
    </div>
</div>
@endsection
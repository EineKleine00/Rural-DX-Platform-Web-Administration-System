@extends('layouts.app')

@section('title', 'Manajemen Akun')

@section('content')
<div class="container mt-4">
    <div class="d-flex justify-content-between mb-3">
        <h4><i class="fa-solid fa-users me-2"></i>Manajemen Akun</h4>
        <a href="{{ route('users.create') }}" class="btn btn-primary"><i class="fa fa-plus me-1"></i>Tambah Akun</a>
    </div>

    <table class="table table-bordered table-striped">
        <thead class="table-dark">
            <tr>
                <th>Nama</th>
                <th>Username</th>
                <th>Role</th>
                <th>Status Login</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach($users as $u)
            <tr>
                <td>{{ $u->name }}</td>
                <td>{{ $u->username }}</td>
                <td>{{ ucfirst($u->role) }}</td>
                <td>
                    @if($u->current_token)
                        <span class="badge bg-success">Aktif</span>
                    @else
                        <span class="badge bg-secondary">Offline</span>
                    @endif
                </td>
                <td>
                    <a href="{{ route('users.edit', $u->id) }}" class="btn btn-warning btn-sm">Edit</a>
                    <form action="{{ route('users.destroy', $u->id) }}" method="POST" style="display:inline-block;">
                        @csrf @method('DELETE')
                        <button onclick="return confirm('Yakin hapus akun ini?')" class="btn btn-danger btn-sm">Hapus</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection

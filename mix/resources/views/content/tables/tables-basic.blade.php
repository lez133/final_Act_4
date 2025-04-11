@extends('layouts/contentNavbarLayout')

@section('title', 'User List')

@section('content')
<h4 class="py-3 mb-4">
  <span class="text-muted fw-light">Users /</span> Registered Users
</h4>

@if(session('success'))
<div class="alert alert-success alert-dismissible" role="alert">
  {{ session('success') }}
  <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif

<div class="card">
  <h5 class="card-header">Registered Users</h5>
  <div class="table-responsive text-nowrap">
    <table class="table table-hover">
      <thead>
        <tr>
          <th>ID</th>
          <th>Name</th>
          <th>Email</th>
          <th>Google ID</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody class="table-border-bottom-0">
        @foreach ($users as $user)
        <tr>
          <td>{{ $user->id }}</td>
          <td>{{ Crypt::decryptString($user->name) }}</td>
          <td>{{ Crypt::decryptString($user->email) }}</td>
          <td>
            @if($user->google_id)
            <span class="badge bg-label-info">Google</span>
            @else
            <span class="badge bg-label-secondary">Manual</span>
            @endif
          </td>
          <td>
            <div class="dropdown">
              <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                <i class="bx bx-dots-vertical-rounded"></i>
              </button>
              <div class="dropdown-menu">
                <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#editUserModal{{ $user->id }}">
                  <i class="bx bx-edit-alt me-1"></i> Edit
                </a>
              </div>
            </div>
          </td>
        </tr>

        {{-- Modal for Editing --}}
        <div class="modal fade" id="editUserModal{{ $user->id }}" tabindex="-1" aria-labelledby="editUserLabel{{ $user->id }}" aria-hidden="true">
          <div class="modal-dialog">
            <form method="POST" action="{{ route('user.update', $user->id) }}">
              @csrf
              @method('PUT')
              <div class="modal-content">
                <div class="modal-header">
                  <h5 class="modal-title" id="editUserLabel{{ $user->id }}">Edit User</h5>
                  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                  <div class="mb-3">
                    <label class="form-label">Name</label>
                    <input type="text" name="name" class="form-control" value="{{ Crypt::decryptString($user->name) }}" required>
                  </div>
                  <div class="mb-3">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control" value="{{ Crypt::decryptString($user->email) }}" required>
                  </div>
                </div>
                <div class="modal-footer">
                  <button type="submit" class="btn btn-primary">Save changes</button>
                </div>
              </div>
            </form>
          </div>
        </div>
        @endforeach
      </tbody>
    </table>
  </div>
</div>
@endsection
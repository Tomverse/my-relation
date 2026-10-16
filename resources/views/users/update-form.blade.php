@extends('layouts.main')
@section('title', "User: {$title}")
@section('content')

    <form action="{{ route('users.update', ['user' => $user->email]) }}" method="post">
        @csrf
        <div class="app-cmp-data-form">
            <label>
                <b>Email</b>
                <span class="app-cl-code">{{ $user->email }}</span>
            </label>
            <label>
                <b>Name *</b>
                <input type="text" name="name" value="{{ old('name', $user->name) }}" required />
            </label>
            @can('updateRole', $user)
                <label>
                    <b>Role *</b>
                    <select name="role" required>
                        @foreach ($roles as $role)
                            <option value="{{ $role }}" @selected($role === old('role', $user->role))>{{ $role }}</option>
                        @endforeach
                    </select>
                </label>
            @else
                <label>
                    <b>Role</b>
                    <span class="app-cl-code">{{ $user->role }}</span>
                </label>
            @endcan
            <label>
                <b>Password</b>
                <input type="password" name="password" placeholder="Leave blank if you don't want to update" />
            </label>
        </div>
        <div class="app-cmp-form-actions">
            <button type="submit" class="app-cl-button app-cl-primary app-cl-filled">Update</button>
            <span class="app-cmp-form-secondary-actions">
                <a href="{{ session()->get('bookmarks.users.update-form') ?? route('users.view', ['user' => $user->email]) }}"
                    class="app-cl-button app-cl-warnning app-cl-filled">Cancel</a>
            </span>
        </div>
    </form>

@endsection

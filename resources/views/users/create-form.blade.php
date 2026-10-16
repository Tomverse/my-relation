@extends('layouts.main')
@section('title', "User: {$title}")
@section('content')

    <form action="{{ route('users.create') }}" method="post">
        @csrf
        <div class="app-cmp-data-form">
            <label>
                <b>Email *</b>
                <input type="email" name="email" value="{{ old('email') }}" required />
            </label>
            <label>
                <b>Password *</b>
                <input type="password" name="password" required />
            </label>
            <label>
                <b>Name *</b>
                <input type="text" name="name" value="{{ old('name') }}" required />
            </label>
            <label>
                <b>Role *</b>
                <select name="role" required>
                    <option value="" @selected(old('role') === null) disabled>--- Please Select Role ---</option>
                    @foreach ($roles as $role)
                        <option value="{{ $role }}" @selected($role === old('role'))>{{ $role }}</option>
                    @endforeach
                </select>
            </label>
        </div>
        <div class="app-cmp-form-actions">
            <button type="submit" class="app-cl-button app-cl-primary app-cl-filled">Create</button>
            <span class="app-cmp-form-secondary-actions">
                <a href="{{ session()->get('bookmarks.users.create-form') ?? route('users.index') }}"
                    class="app-cl-button app-cl-warnning app-cl-filled">Cancel</a>
            </span>
        </div>
    </form>

@endsection

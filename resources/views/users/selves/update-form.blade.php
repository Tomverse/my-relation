@extends('layouts.main')
@section('title', "User: {$title}")
@section('content')

    <form action="{{ route('users.selves.update') }}" method="post">
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
            <label>
                <b>Password</b>
                <input type="password" name="password" placeholder="Leave blank if you don't want to update" />
            </label>
        </div>
        <div class="app-cmp-form-actions">
            <button type="submit" class="app-cl-button app-cl-primary app-cl-filled">Update</button>
            <span class="app-cmp-form-secondary-actions">
                <a href="{{ session()->get('bookmarks.users.selves.update-form') ?? route('users.selves.view') }}"
                    class="app-cl-button app-cl-warnning app-cl-filled">Cancel</a>
            </span>
        </div>
    </form>

@endsection

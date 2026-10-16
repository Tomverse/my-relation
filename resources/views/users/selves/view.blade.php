@extends('layouts.main')
@section('title', "User: {$title}")

@section('header')
    @php
        session()->put('bookmarks.users.selves.update-form', url()->full());
        session()->put('bookmarks.users.selves.update', session()->get('bookmarks.users.selves.view'));
    @endphp

    <nav>
        <div class="app-cmp-form-actions">
            <a href="{{ session()->get('bookmarks.users.selves.view') ?? '/' }}" class="app-cl-button app-cl-primary">&lt;
                Back</a>
            <a href="{{ route('users.selves.update-form') }}" class="app-cl-button app-cl-primary app-cl-filled">Update</a>
        </div>
    </nav>
@endsection

@section('content')

    <dl class="app-cmp-data-detail">
        <dt>Email</dt>
        <dd class="app-cl-code">{{ $user->email }}</dd>

                <dt>Name</dt>
        <dd>{{ $user->name }}</dd>

        <dt>Role</dt>
        <dd class="app-cl-code">{{ $user->role }}</dd>
    </dl>

@endsection

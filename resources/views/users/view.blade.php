@extends('layouts.main')
@section('title', "User: {$title}")

@section('header')
    @php

        session()->put('bookmarks.users.delete', session()->get('bookmarks.users.view'));
        session()->put('bookmarks.users.update-form', url()->full());
        session()->put('bookmarks.users.update', session()->get('bookmarks.users.view'));
    @endphp

    <nav>
        @can('delete', $user)
            <form action="{{ route('users.delete', ['user' => $user->email]) }}" method="post" id="app-form-delete">
                @csrf
            </form>
        @endcan
        <div class="app-cmp-form-actions">
            <a href="{{ session()->get('bookmarks.users.view') ?? route('users.index') }}"
                class="app-cl-button app-cl-primary">&lt; Back</a>
            @can('update', $user)
                <a href="{{ route('users.update-form', ['user' => $user->email]) }}"
                    class="app-cl-button app-cl-primary app-cl-filled">Update</a>
            @endcan
            @can('delete', $user)
                <button type="submit" form="app-form-delete" class="app-cl-button app-cl-warnning app-cl-filled">Delete</button>
            @endcan
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

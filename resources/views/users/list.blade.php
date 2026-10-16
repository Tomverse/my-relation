@extends('layouts.main')
@section('title', "User: {$title}")

@section('header')
    <search>
        <form action="{{ route('users.list') }}" method="get" class="app-cmp-search-form">
            <fieldset>
                <legend>Search</legend>
                <div class="app-cmp-data-form">
                    <label>
                        <b>Term</b>
                        <input type="text" name="term" value="{{ $criteria['term'] }}" />
                    </label>
                </div>
                <div class="app-cmp-form-actions">
                    <button type="submit" class="app-cl-button app-cl-primary app-cl-filled">Filter</button>
                    <span class="app-cmp-form-secondary-actions">
                        <a href="{{ route('users.list') }}" class="app-cl-button app-cl-warnning app-cl-filled">Clear</a>
                    </span>
                </div>
            </fieldset>
        </form>
    </search>

    @php
        session()->put('bookmarks.users.create-form', url()->full());
        session()->put('bookmarks.users.create', session()->get('bookmarks.users.create-form'));
    @endphp

    <div class="app-cmp-links-bar">
        @can('create', \App\Models\User::class)
            <a href="{{ route('users.create-form') }}" class="app-cl-button app-cl-primary app-cl-filled">Create User</a>
        @else
            <span></span>
        @endcan
        {{ $users->withQueryString()->links() }}
    </div>
@endsection

@section('content')

    @php
        session()->put('bookmarks.users.view', url()->full());
    @endphp

    <table class="app-cmp-data-list">
        <caption>List of Users</caption>
        <colgroup>
            <col style="width: 0">
        </colgroup>
        <thead>
            <tr>
                <th>Email</th>
                <th>Name</th>
                <th>Role</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($users as $user)
                <tr>
                    <td>
                        <a href="{{ route('users.view', ['user' => $user->email]) }}"
                            class="app-cl-link app-cl-code">{{ $user->email }}</a>
                    </td>
                    <td>{{ $user->name }}</td>
                    <td class="app-cl-code">{{ $user->role }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

@endsection

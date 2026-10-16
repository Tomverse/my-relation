<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') - Chaiyapoom's Database</title>
    <link rel="stylesheet" href="{{ asset('css/styles.css') }}" />
</head>

<body>
    @php
        if (!Route::is('users.selves.*')) {
            session()->put('bookmarks.users.selves.view', url()->full());
        }
    @endphp

    <header id="app-main-header">
        <nav>
            <ul class="app-cmp-links">
                <li @class(['app-cl-active' => Route::is('products.*')])>
                    <a href="{{ route('products.list') }}">Product</a>
                </li>
                <li @class(['app-cl-active' => Route::is('shops.*')])>
                    <a href="{{ route('shops.list') }}">Shop</a>
                </li>
                <li @class(['app-cl-active' => Route::is('categories.*')])>
                    <a href="{{ route('categories.list') }}">Category</a>
                </li>
                @can('list', \App\Models\User::class)
                    <li @class([
                        'app-cl-active' => Route::is('users.*') && !Route::is('users.selves.*'),
                    ])>
                        <a href="{{ route('users.list') }}">User</a>
                    </li>
                @endcan
            </ul>

            @auth
                <form action="{{ route('logout') }}" method="post" id="app-form-logout">
                    @csrf
                </form>
                <ul class="app-cmp-user-panel">
                    <li>
                        <a href="{{ route('users.selves.view') }}">{{ \Auth::user()->name }}</a>
                    </li>
                    <li>
                        <button type="submit" form="app-form-logout">Logout</button>
                    </li>
                </ul>
            @endauth
        </nav>
    </header>

    <main id="app-main-content">
        <header class="app-cmp-content-header">
            <h1>@yield('title')</h1>

            <div class="app-cmp-notifications">
                @session('status')
                    <div role="status">{{ $value }}</div>
                @endsession
                @foreach ($errors->all() as $message)
                    <div role="alert">{{ $message }}</div>
                @endforeach
            </div>

            @yield('header')
        </header>

        @yield('content')
    </main>

    <footer id="app-main-footer">
        &copy; Copyright Chaiyapoom's Database.
    </footer>
</body>

</html>

<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Chaiyapoom's Database</title>
    <link rel="stylesheet" href="{{ asset('css/styles.css') }}" />
</head>

<body>
    <main id="app-main-content" class="app-cmp-login">
        <header class="app-cmp-content-header">
            <h1>Login</h1>
        </header>

        <form action="{{ route('authentication') }}" method="post">
            @csrf
            <div class="app-cmp-data-form">
                <label>
                    <b>Email *</b>
                    <input type="email" name="email" required />
                </label>
                <label>
                    <b>Password *</b>
                    <input type="password" name="password" required />
                </label>
            </div>
            <div class="app-cmp-form-actions">
                <button type="submit" class="app-cl-button app-cl-primary app-cl-filled">Login</button>
            </div>
            <div class="app-cmp-notifications">
                @error('credentials')
                    <div role="alert">{{ $message }}</div>
                @enderror
            </div>
        </form>
    </main>

    <footer id="app-main-footer">
        &copy; Copyright Chaiyapoom's Database.
    </footer>
</body>

</html>

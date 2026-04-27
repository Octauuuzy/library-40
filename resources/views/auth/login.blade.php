<x-guest-layout>
    <header class="auth-login-header">
        <div class="auth-logo" aria-hidden="true">
            <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M3 6.8C3 5.25 4.25 4 5.8 4H10.4C11.88 4 13.08 5.2 13.08 6.68V17.3C13.08 17.63 12.73 17.84 12.44 17.7C11.66 17.32 10.53 17 9.3 17H5.8C4.25 17 3 15.75 3 14.2V6.8Z" fill="currentColor"/>
                <path d="M21 6.8C21 5.25 19.75 4 18.2 4H13.6C12.12 4 10.92 5.2 10.92 6.68V17.3C10.92 17.63 11.27 17.84 11.56 17.7C12.34 17.32 13.47 17 14.7 17H18.2C19.75 17 21 15.75 21 14.2V6.8Z" stroke="currentColor" stroke-width="1.5"/>
            </svg>
        </div>
        <h1 class="auth-title">Perpustakaan</h1>
        <p class="auth-subtitle">Silakan login untuk melanjutkan</p>
    </header>

    @if (session('status'))
        <div class="auth-notice success">{{ session('status') }}</div>
    @endif

    @if ($errors->any())
        <div class="auth-notice error">
            <strong>Periksa input Anda:</strong>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('login') }}" class="auth-form">
        @csrf

        <div class="auth-field">
            <label for="email" class="auth-label">Email</label>
            <div class="auth-input-wrap">
                <span class="auth-input-icon" aria-hidden="true">
                    <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M3 6.75C3 5.78 3.78 5 4.75 5H19.25C20.22 5 21 5.78 21 6.75V17.25C21 18.22 20.22 19 19.25 19H4.75C3.78 19 3 18.22 3 17.25V6.75Z" stroke="currentColor" stroke-width="1.5"/>
                        <path d="M4 7L12 13L20 7" stroke="currentColor" stroke-width="1.5"/>
                    </svg>
                </span>
                <input
                    id="email"
                    type="email"
                    name="email"
                    value="{{ old('email') }}"
                    class="auth-input"
                    placeholder="Masukkan email"
                    required
                    autofocus
                    autocomplete="username"
                >
            </div>
        </div>

        <div class="auth-field">
            <label for="password" class="auth-label">Password</label>
            <div class="auth-input-wrap">
                <span class="auth-input-icon" aria-hidden="true">
                    <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M7.5 10V7.25C7.5 4.9 9.4 3 11.75 3C14.1 3 16 4.9 16 7.25V10" stroke="currentColor" stroke-width="1.5"/>
                        <rect x="5" y="10" width="14" height="11" rx="2" stroke="currentColor" stroke-width="1.5"/>
                    </svg>
                </span>
                <input
                    id="password"
                    type="password"
                    name="password"
                    class="auth-input"
                    placeholder="Masukkan password"
                    required
                    autocomplete="current-password"
                >
            </div>
        </div>

        <button type="submit" class="auth-submit">Login</button>
    </form>
</x-guest-layout>

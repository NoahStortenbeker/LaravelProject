@extends('layouts.guest')

@section('content')
<div class="body_wrapper">

<div class="login_wrapper">
    <div class="login_form_container">
        <div class="login_form_content">
            <h2>Login to your account</h2>

            @if ($errors->any())
                <div class="login_error" style="color:#ff4d4f; margin-bottom: 20px;">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            @if (session('status'))
                <div class="login_success" style="color:#22c55e; margin-bottom: 20px;">
                    {{ session('status') }}
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}" class="login_form" autocomplete="off">
                @csrf
                <div class="form_group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" value="{{ old('email') }}" required autofocus placeholder="JohnDoe@example.com" autocomplete="off" autocapitalize="none" spellcheck="false" inputmode="email">
                </div>
                <div class="form_group">
                    <label for="password">Password</label>
                    <div class="password_input_wrapper">
                        <input type="password" id="password" name="password" required autocomplete="new-password">
                        <i class="ri-eye-off-line toggle-password"></i>
                    </div>
                </div>
                <button type="submit" class="submit_btn">
                    <span class="btn_text">Login</span>
                    <i class="ri-login-box-line btn_icon"></i>
                </button>
                <div class="signup_link">
                    Don't have an account? <a href="{{ route('register') }}" class="transition-link">Sign Up</a>
                </div>
            </form>
        </div>
    </div>
    <div class="login_image_container">
        <img src="{{ asset('assets/register_login_png.jpg') }}" alt="Login Image">
    </div>
</div>
</div>
<script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js"></script>
<link href="https://cdn.jsdelivr.net/npm/remixicon@4.1.0/fonts/remixicon.css" rel="stylesheet">
@endsection

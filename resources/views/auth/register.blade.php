@extends('layouts.guest')

@section('content')
<div class="body_wrapper">
<div class="register_wrapper">
    <div class="register_image_container">
        <img src="{{ asset('assets/register_login_png.jpg') }}" alt="Register Image">
    </div>

    <div class="register_form_container">
        <h2>Create your account</h2>
        <form method="POST" action="{{ route('register') }}" class="register_form">
            @csrf
            <div class="form_row form_row--name">
                <div class="form_group">
                    <label for="name">Full name</label>
                    <input type="text" id="name" name="name" value="{{ old('name') }}" required placeholder="John Doe" autocomplete="off" autocorrect="off" spellcheck="false">
                    <x-input-error :messages="$errors->get('name')" class="mt-2" />
                </div>
                <div class="form_group">
                    <label for="username">User name</label>
                    <input type="text" id="username" name="username" value="{{ old('username') }}" required placeholder="john_doe" autocomplete="off" autocapitalize="none" spellcheck="false">
                    <x-input-error :messages="$errors->get('username')" class="mt-2" />
                    <p class="input_hint">Max 8 characters.</p>
                </div>
            </div>
            <div class="form_group">
                <label for="email">E-mail address</label>
                <input type="email" id="email" name="email" value="{{ old('email') }}" required placeholder="JohnDoe@hotmail.com" autocomplete="off" autocapitalize="none" spellcheck="false" inputmode="email">
                <x-input-error :messages="$errors->get('email')" class="mt-2" />
            </div>
            <div class="form_row">
                <div class="form_group">
                    <label for="password">Password</label>
                    <div class="password_input_wrapper">
                        <input type="password" id="password" name="password" required autocomplete="new-password">
                        <i class="ri-eye-off-line toggle-password"></i>
                    </div>
                        @if ($errors->has('password'))
                            @php $pwdMsg = $errors->first('password'); @endphp
                            @if (stripos($pwdMsg, 'confirmation') !== false)
                                <div class="login_error" style="color:#ff4d4f; margin-bottom: 20px;">Passwords do not match</div>
                            @endif
                        @endif
                </div>
                <div class="form_group">
                    <label for="password_confirmation">Confirm password</label>
                    <div class="password_input_wrapper">
                        <input type="password" id="password_confirmation" name="password_confirmation" required autocomplete="new-password">
                        <i class="ri-eye-off-line toggle-password"></i>
                    </div>
                        @if ($errors->has('password_confirmation'))
                            @php $confirmMsg = $errors->first('password_confirmation'); @endphp
                            @if (stripos($confirmMsg, 'match') !== false || stripos($confirmMsg, 'confirmation') !== false)
                                <div class="login_error" style="color:#ff4d4f; margin-bottom: 20px;">Passwords do not match</div>
                            @endif
                        @endif
                </div>
            </div>
            <button type="submit" class="submit_btn">Create Account</button>
            <p class="login_link">Already have an account? <a href="{{ route('login') }}" class="transition-link">Login</a></p>
        </form>
    </div>
</div>
</div>
<script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js"></script>
<link href="https://cdn.jsdelivr.net/npm/remixicon@4.1.0/fonts/remixicon.css" rel="stylesheet">
@endsection 

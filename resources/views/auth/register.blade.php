@extends('layout.auth-layout')

@section('content')
    <div class="container">
        <form action="/register" method="POST" class="contact-form">
            <h2 class="form-heading">Register</h2>

            @if (session('_flash.error'))
                <div class="error-message">
                    {{ session('_flash.error') }}
                </div>
            @endif

            <div class="form-input">
                <label for="">Full Name</label>
                <input type="text" name="username" value="{{ old('name') }}">
            </div>
            <div class="form-input">
                <label for="">Email</label>
                <input type="email" name="email" value="{{ old('email') }}">
            </div>
            <div class="form-input">
                <label for="">Password</label>
                <input type="password" name="password">
            </div>

            <input type="submit" value="Register" class="form-submit">
        </form>

        <p>Already have an account? <a href="/login">Login</a></p>
    </div>
@endsection

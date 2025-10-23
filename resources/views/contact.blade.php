@extends('layout.index')

@section('content')

    <form action="/contact" method="POST" class="contact-form">

        <h2 class="form-heading">Contact Form</h2>

        <div class="form-input">
            <label for="">Full name</label>
            <input type="text" name="name" value="{{ $old['name'] ?? ''}}">
             @if ($errors->has('name'))
                <span class="error-text">{{ $errors->first('name') }}</span>
            @endif
        </div>

        <div class="form-input">
            <label for="">Email</label>
            <input type="text" name="email" value="">
            @if ($errors->has('email'))
                <span class="error-text">{{ $errors->first('email') }}</span>
            @endif
        </div>

        <input type="submit" value="submit" class="form-submit">

    </form>

@endsection

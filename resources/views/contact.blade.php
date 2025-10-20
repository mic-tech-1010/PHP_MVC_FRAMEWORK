@extends('layout.index')

@section('content')
    <div class="container">
        <form action="/contact" method="POST" class="contact-form">
             <h2 class="form-heading">Contact Form</h2>
            <div class="form-input">
                <label for="">Full name</label>
                <input type="text" name="name">
            </div>
            <div class="form-input">
                <label for="">Email</label>
                <input type="text" name="email">
            </div>
            <input type="submit" value="submit" class="form-submit">
        </form>
    </div>
@endsection

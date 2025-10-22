 @extends('layout.auth-layout')

 @section('content')
     <div class="container">
         <form action="/contact" method="POST" class="contact-form">
             <h2 class="form-heading">Login</h2>
             <div class="form-input">
                 <label for="">Email</label>
                 <input type="text" name="email">
             </div>
             <div class="form-input">
                 <label for="">Password</label>
                 <input type="password" name="password">
             </div>
             <input type="submit" value="submit" class="form-submit">
         </form>
     </div>
 @endsection

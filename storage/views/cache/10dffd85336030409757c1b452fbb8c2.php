<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>test</title>
    <link rel="stylesheet" href="/css/style.css">
</head>

<body>

    <header>
    <div class="container flex-between">
    <a href="/">My Logo </a>
    <nav>
        <ul>
            <li><a href="/">Home</a></li>
            <li><a href="/about">About</a></li>
            <li><a href="/contact">Contact</a></li>
        </ul>
    </nav>
    </div>
</header>


    <main class="main">
        <div class="container">
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
        </div>
    </main>

      <footer>My Footer</footer>
</body>

</html>

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
            <li><a href=<?= htmlspecialchars(route('home'), ENT_QUOTES, "UTF-8") ?>>Home</a></li>
            <li><a href="/about">About</a></li>
            <li><a href="/contact">Contact</a></li>
        </ul>
    </nav>
    </div>
</header>


    <main class="main">
        <div class="container">
            <form action="/contact" method="POST" class="contact-form">

        <h2 class="form-heading">Contact Form</h2>

        <div class="form-input">
            <label for="">Full name</label>
            <input type="text" name="name" value="<?= htmlspecialchars($old['name'] ?? '', ENT_QUOTES, "UTF-8") ?>">
             <?php if ($errors->has('name')): ?>
                <span class="error-text"><?= htmlspecialchars($errors->first('name'), ENT_QUOTES, "UTF-8") ?></span>
            <?php endif; ?>
        </div>

        <div class="form-input">
            <label for="">Email</label>
            <input type="text" name="email" value="">
            <?php if ($errors->has('email')): ?>
                <span class="error-text"><?= htmlspecialchars($errors->first('email'), ENT_QUOTES, "UTF-8") ?></span>
            <?php endif; ?>
        </div>

        <input type="submit" value="submit" class="form-submit">

    </form>
        </div>
    </main>

      <footer>My Footer</footer>
</body>

</html>

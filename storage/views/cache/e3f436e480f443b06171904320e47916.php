<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>test</title>
    <link rel="stylesheet" href="/css/style.css">
</head>

<body>

    <main class="main">
        <div class="container">
            <div class="container">
        <form action="/login" method="POST" class="contact-form">
            <h2 class="form-heading">Login</h2>

            <?php if (session('_flash.error')): ?>
                <div class="error-message">
                    <?= htmlspecialchars(session('_flash.error'), ENT_QUOTES, "UTF-8") ?>
                </div>
            <?php endif; ?>

            <div class="form-input">
                <label for="">Email</label>
                <input type="text" name="email" value="<?= htmlspecialchars(old('email'), ENT_QUOTES, "UTF-8") ?>">
            </div>
            <div class="form-input">
                <label for="">Password</label>
                <input type="password" name="password">
            </div>

            <input type="submit" value="Login" class="form-submit">
        </form>

        <p>No account? <a href="/register">Register</a></p>
    </div>
        </div>
    </main>

</body>

</html>

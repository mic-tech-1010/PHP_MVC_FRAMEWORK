<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($title ?? 'My Site') ?></title>
     <link rel="stylesheet" href="/css/style.css">
</head>
<body>
    <h1><?= $name ?? 'Welcome!' ?></h1>
    <p>This is the homepage.</p>
</body>
</html>
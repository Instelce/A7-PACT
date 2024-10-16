<?php

use app\core\Application;

?>

<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo $this->title ?></title>
    <link rel="stylesheet" href="/css/output.css">
</head>

<body>

<x-navbar></x-navbar>

    <div>
        <!-- Show alert -->
        <?php if (Application::$app->session->getFlash('success')): ?>
            <div class="alert alert-success">
                <?php echo Application::$app->session->getFlash('success') ?>
            </div>
        <?php endif; ?>
        {{content}}
    </div>
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.js"></script>

    <script type="module" src="/js/main.js"></script>

</body>

</html>
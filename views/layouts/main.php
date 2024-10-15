<?php

use app\core\Application;

?>

<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo $this->title ?></title>
    <link rel="stylesheet" href="/css/main.css">
</head>

<body>
    <div>
        <!-- Show alert -->
        <?php if (Application::$app->session->getFlash('success')): ?>
            <div class="alert alert-success">
                <?php echo Application::$app->session->getFlash('success') ?>
            </div>
        <?php endif; ?>
        {{content}}
    </div>

    <app-input placeHolder="nom" icon="search" leftOrRight="right"></app-input>
    <script type="module" src="/js/main.js"></script>
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.js"></script>
    <script type="module" src="/js/components/input.js"></script>
    <script type="module" src="/js/components/WebComponent.js"></script>

</body>

</html>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title><?= phpb_trans('auth.title') ?></title>

    <link rel="stylesheet" href="<?= phpb_asset('pagebuilder/bootstrap-v4.3.1.min.css') ?>">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.8.1/css/all.css" integrity="sha384-50oBUHEmvpQ+1lW4y57PTFmhCaXp0ML5d60M1M7uH2+nqUivzIebhndOJK28anvf" crossorigin="anonymous">
    <link href="<?= phpb_asset('auth/app.css') ?>" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container">
    <?php
    require  __DIR__ . '/' . $viewFile . '.php';
    ?>
</div>

<script src="<?= phpb_asset('pagebuilder/jquery-3.4.1.min.js') ?>"></script>
<script src="<?= phpb_asset('pagebuilder/bootstrap-v4.3.1.min.js') ?>"></script>
</body>
</html>

<!doctype html>
<html lang="<?php echo e(str_replace('_', '-', app()->getLocale())); ?>">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>"/>
    <title><?php echo e(env('APP_NAME', 'Laravel')); ?></title>
    <link href="<?php echo e(mix('css/app.css')); ?>" type="text/css" rel="stylesheet"/>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
</head>
<body>
<?php if(Auth::check()): ?>
    <script>
        window.Laravel = <?php echo json_encode([
            'isLoggedin' => true,
            'user' => Auth::user()
        ]); ?>

    </script>
<?php else: ?>
    <script>
        window.Laravel = <?php echo json_encode([
            'isLoggedin' => false
        ]); ?>

    </script>
<?php endif; ?>
<div id="app">
</div>
<script src="<?php echo e(mix('js/app.js')); ?>" type="text/javascript"></script>
</body>
</html><?php /**PATH E:\xampp_php_7_4_16\htdocs\my_project_laravel_vue\resources\views/app.blade.php ENDPATH**/ ?>
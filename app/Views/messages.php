<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Mensagens</title>
</head>
<body>
    <div class="container mt-5">
        <div class="alert alert-info">
            <?php echo $message; ?>
            <p><?php echo anchor(base_url(), 'Página Inicial')?></p>
        </div>
    </div>
</body>
</html>

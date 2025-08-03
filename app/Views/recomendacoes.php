<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Recomendações</title>
</head>
<body>
    <div class="container mt-5">
        <h1>Tempo total assistido</h1>
        <p>
            <?php
                $horas = floor($totalMinutos / 60);
                $minutos = $totalMinutos % 60;
                echo "Você já assistiu aproximadamente <strong>{$horas} horas e {$minutos} minutos</strong> de filmes!";
            ?>
        </p>
        <p><?= anchor(base_url('producoes'), 'Voltar às produções') ?></p>
    </div>
</body>
</html>

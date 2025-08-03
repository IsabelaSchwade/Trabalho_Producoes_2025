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

    <h2>Recomendações com base no seu gosto:</h2>
<?php if (!empty($recomendacoes)): ?>
    <ul>
        <?php foreach ($recomendacoes as $filme): ?>
            <li>
                <strong><?= esc($filme['titulo']) ?></strong> (<?= esc($filme['ano']) ?>)<br>
                <?php if (!empty($filme['poster']) && $filme['poster'] !== 'N/A'): ?>
                    <img src="<?= esc($filme['poster']) ?>" alt="Pôster de <?= esc($filme['titulo']) ?>" style="max-height:150px;">
                <?php endif; ?>
            </li>
        <?php endforeach; ?>
    </ul>
<?php else: ?>
    <p>Nenhuma recomendação disponível no momento.</p>
<?php endif; ?>

</body>
</html>

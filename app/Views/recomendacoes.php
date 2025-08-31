<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Recomendações</title>
    <link rel="stylesheet" href="<?= base_url('css/style.css') ?>">
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
  

    <h2>Recomendações com base no seu gosto:</h2>
    <?php if (!empty($recomendacoes)): ?>
        <ul>
            <?php foreach ($recomendacoes as $filme): ?>
                <li>
                    <strong><?= esc($filme['titulo']) ?></strong> (<?= esc($filme['ano']) ?>)<br>
                    <?php if (!empty($filme['capa']) && $filme['capa'] !== 'N/A'): ?>
                        <img src="<?= esc($filme['capa']) ?>" alt="Pôster de <?= esc($filme['titulo']) ?>">
                    <?php endif; ?>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php else: ?>
        <p>Nenhuma recomendação disponível no momento.</p>
    <?php endif; ?>
      <p><?= anchor(base_url('filmes'), 'Voltar aos filmes', ['class'=>'btn btn-info']) ?></p>
</div>
</body>
</html>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Visualizar Filme</title>
</head>
<body>
<div class="container">
    <h1><?= esc($producao['filme']) ?></h1>

    <?php if (!empty($producao['poster']) && $producao['poster'] !== 'N/A'): ?>
        <img src="<?= esc($producao['poster']) ?>" alt="Poster" style="max-height:300px;">
    <?php endif; ?>

    <p><strong>Nota:</strong> <?= esc($producao['nota']) ?></p>
    <p><strong>Status:</strong> <?= esc($producao['status']) ?></p>
    <p><strong>Comentário:</strong> <?= esc($producao['comentario']) ?></p>
    <p><strong>Duração:</strong> <?= esc($producao['duracao']) ?> minutos</p>
    <p><strong>Diretor:</strong> <?= esc($producao['diretor']) ?></p>
    <p><strong>Elenco:</strong> <?= esc($producao['elenco']) ?></p>
    <p><strong>Sinopse:</strong> <?= esc($producao['sinopse']) ?></p>
    <p><strong>Gêneros:</strong> <?= esc($producao['generos']) ?></p>

    <p><?= anchor(base_url('producoes'), 'Voltar às produções') ?></p>
</div>
</body>
</html>

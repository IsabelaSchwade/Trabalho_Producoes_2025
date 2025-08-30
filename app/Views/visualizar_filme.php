<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Detalhes do Filme</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
</head>
<body>
<div class="container mt-4">
    <h1><?= esc($filme['filme'] ?? 'Título não informado') ?></h1>

    <?php if (!empty($filme['capa']) && $filme['capa'] !== 'N/A'): ?>
       <img src="<?= base_url('images/' . esc($filme['capa'])) ?>" alt="Capa do filme <?= esc($filme['filme'] ?? '') ?>" class="img-fluid mb-3" style="max-height:300px;">

    <?php else: ?>
        <p><em>Sem capa disponível</em></p>
    <?php endif; ?>

    <p><strong>Nota:</strong> <?= esc($filme['nota'] ?? 'Não informada') ?></p>
    <p><strong>Status:</strong> <?= esc($filme['status'] ?? 'Não informado') ?></p>
    <p><strong>Comentário:</strong> <?= esc($filme['comentario'] ?? 'Não informado') ?></p>
    <p><strong>Duração:</strong> <?= esc($filme['duracao'] ?? 'Não informada') ?> minutos</p>
    <p><strong>Diretor:</strong> <?= esc($filme['diretor'] ?? 'Não informado') ?></p>
    <p><strong>Elenco:</strong> <?= esc($filme['elenco'] ?? 'Não informado') ?></p>
    <p><strong>Sinopse:</strong> <?= esc($filme['sinopse'] ?? 'Não informada') ?></p>
    <p><strong>Gêneros:</strong> <?= esc($filme['generos'] ?? 'Não informado') ?></p>

    <p>
        <?= anchor(base_url('filmes'), 'Voltar aos filmes', ['class'=>'btn btn-info']) ?>
        <?= anchor(base_url('filme/editar/'.$filme['id']), 'Editar', ['class'=>'btn btn-warning']) ?>
        <a href="<?= base_url('filme/excluir/'.$filme['id']) ?>" 
           class="btn btn-danger"
           onclick="return confirm('Tem certeza que deseja excluir este filme?')">
           Excluir
        </a>
    </p>
</div>
</body>
</html>

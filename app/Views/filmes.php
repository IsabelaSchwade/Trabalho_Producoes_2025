<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Filmes</title>
    <link rel="stylesheet" href="<?= base_url('css/style.css') ?>">
    
</head>
<body>
    <h1> IsaLivix</h2>
<div class="container">
    <div style="margin-bottom:20px;">
        <?= anchor(base_url('filme/formulario'), 'Novo Filme', ['class' => 'btn btn-success']) ?>
        <?= anchor(base_url('recomendacoes'), 'Recomendações', ['class' => 'btn btn-info', 'style' => 'margin-left:10px;']) ?>
    </div>

    <div class="form-group" style="margin-bottom:20px;">
        <label for="filtroStatus"><strong>Filtrar por Status:</strong></label>
        <select id="filtroStatus" class="form-control" style="width:200px; display:inline-block; margin-left:10px;">
            <option value="<?= base_url('filmes') ?>" <?= empty($statusAtual) ? 'selected' : '' ?>>Todos</option>
            <option value="<?= base_url('filmes/assistido') ?>" <?= ($statusAtual ?? '') === 'assistido' ? 'selected' : '' ?>>Assistido</option>
            <option value="<?= base_url('filmes/planejado') ?>" <?= ($statusAtual ?? '') === 'planejado' ? 'selected' : '' ?>>Planejado</option>
            <option value="<?= base_url('filmes/em andamento') ?>" <?= ($statusAtual ?? '') === 'em andamento' ? 'selected' : '' ?>>Em andamento</option>
            <option value="<?= base_url('filmes/abandonado') ?>" <?= ($statusAtual ?? '') === 'abandonado' ? 'selected' : '' ?>>Abandonado</option>
        </select>
    </div>

    <script>
        document.getElementById('filtroStatus').addEventListener('change', function() {
            window.location.href = this.value; /* Ao mudar o <select>, a página redireciona automaticamente para a URL correspondente ao status selecionado.*/
        });
    </script>
    
    <div class="form-group" style="margin-bottom:20px; position: relative;">
    <label for="buscaFilme"><strong>Pesquisar por Filme:</strong></label>
    <form action="<?= base_url('filmes') ?>" method="get" style="display: inline-block;">
        <input type="text" id="buscaFilme" name="q" class="form-control" 
                style="width:300px; display:inline-block;" 
                value="<?= esc($buscaNome ?? '') ?>" placeholder="Digite o nome do filme" autocomplete="off">
        <button type="submit" class="btn btn-primary" style="margin-left: 10px;">Buscar</button>
        <?php if (!empty($statusAtual)): ?>
            <input type="hidden" name="status" value="<?= esc($statusAtual) ?>">
        <?php endif; ?>
    </form>
</div>

    <h2>
        <?php if (!empty($statusAtual)): ?>
            Listando filmes com status: <strong><?= ucfirst($statusAtual) ?></strong>
        <?php else: ?>
            Listando todos os filmes
        <?php endif; ?>
        <?php if (!empty($buscaNome)): ?>
            | Pesquisando por: <strong><?= esc($buscaNome) ?></strong>
        <?php endif; ?>
    </h2>

    <div class="movie-grid">
        <?php if(!empty($filmes)): ?>
            <?php foreach($filmes as $filme): ?>
                <a href="<?= base_url('filme/view/' . $filme['id']) ?>" class="movie-card">
                    
                    <?php if(!empty($filme['capa']) && $filme['capa'] !== 'N/A'): ?>
                        <img src="<?= ($filme['capa']) ?>" alt="Pôster do filme <?= esc($filme['filme']) ?>">
                    <?php endif; ?>
                    <span class="movie-card-title"><?= ($filme['filme']) ?></span>
                </a>
            <?php endforeach; ?>
        <?php else: ?>
            <div style="grid-column: 1 / -1; text-align: center;">
                <p>Nenhum filme encontrado.</p>
            </div>
        <?php endif; ?>
    </div>
</div>
</body>
</html>

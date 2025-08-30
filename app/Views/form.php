<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title><?= isset($producao) ? 'Editar Produção' : 'Nova Produção' ?></title>
    <link rel="stylesheet" href="<?= base_url('css/style.css') ?>">
</head>
<body>
<div class="container mt-5">
    <?= form_open(isset($producao) ? 'producao/update/' . $producao['id'] : 'producao/store') ?>

    <div class="form-group">
        <label for="filme">Filme</label>
        <input type="text" name="filme" id="filme" class="form-control" value="<?= esc($producao['filme'] ?? '') ?>">
    </div>

    <div class="form-group">
        <label for="nota">Nota</label>
        <input type="number" step="0.1" name="nota" id="nota" class="form-control" value="<?= esc($producao['nota'] ?? '') ?>">
    </div>

    <div class="form-group">
        <label for="comentario">Comentário</label>
        <textarea name="comentario" id="comentario" class="form-control"><?= esc($producao['comentario'] ?? '') ?></textarea>
    </div>

    <div class="form-group">
        <label for="status">Status</label>
        <select name="status" id="status" class="form-control">
            <?php
                $statuses = ['assistido', 'planejado', 'em andamento', 'abandonado'];
                foreach ($statuses as $status) {
                    $selected = ($producao['status'] ?? '') === $status ? 'selected' : '';
                    echo "<option value='$status' $selected>$status</option>";
                }
            ?>
        </select>
    </div>

    <div class="form-group">
        <label for="duracao">Duração (minutos)</label>
        <input type="number" name="duracao" id="duracao" class="form-control" value="<?= esc($producao['duracao'] ?? '') ?>">
    </div>
    
    <button type="submit" class="btn btn-success"><?= isset($producao) ? 'Atualizar' : 'Salvar' ?></button>
    <?= form_close(); ?>
    
    <div style="margin-top: 20px;">
        <a href="<?= base_url('producoes') ?>" class="btn btn-secondary">Voltar</a>
    </div>
</div>
</body>
</html>
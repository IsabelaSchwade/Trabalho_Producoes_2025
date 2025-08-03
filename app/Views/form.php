form.php:

<?php
    $isEdit = isset($producao);
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title><?= $isEdit ? 'Editar Produção' : 'Nova Produção' ?></title>
</head>
<body>
    <div class="container mt-5">
        <?= form_open($isEdit ? 'producao/update/' . $producao['id'] : 'producao/store') ?>

        <div class="formgroup">
            <label for="filme">Filme</label>
            <input type="text" name="filme" id="filme" class="form-control" value="<?= esc($producao['filme'] ?? '') ?>">
        </div>

        <div class="formgroup">
            <label for="nota">Nota</label>
            <input type="number" step="0.1" name="nota" id="nota" class="form-control" value="<?= esc($producao['nota'] ?? '') ?>">
        </div>

        <div class="formgroup">
            <label for="comentario">Comentário</label>
            <textarea name="comentario" id="comentario" class="form-control"><?= esc($producao['comentario'] ?? '') ?></textarea>
        </div>

        <div class="formgroup">
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
        <div class="formgroup">
    <label for="duracao">Duração (minutos)</label>
    <input type="number" name="duracao" id="duracao" class="form-control" value="<?= esc($producao['duracao'] ?? '') ?>">
</div>


        <button type="submit" class="btn btn-primary"><?= $isEdit ? 'Atualizar' : 'Salvar' ?></button>
        <?= form_close(); ?>
    </div>
</body>
</html>
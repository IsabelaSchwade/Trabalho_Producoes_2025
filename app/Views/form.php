<?= form_open(isset($filme) ? 'filme/atualizar/' . $filme['id'] : 'filme/cadastrar') ?>

<div class="form-group">
    <label for="filme">Filme</label>
    <input type="text" name="filme" id="filme" class="form-control" value="<?= esc($filme['filme'] ?? '') ?>">
</div>

<div class="form-group">
    <label for="nota">Nota</label>
    <input type="number" step="0.1" name="nota" id="nota" class="form-control" value="<?= esc($filme['nota'] ?? '') ?>">
</div>

<div class="form-group">
    <label for="comentario">Comentário</label>
    <textarea name="comentario" id="comentario" class="form-control"><?= esc($filme['comentario'] ?? '') ?></textarea>
</div>

<div class="form-group">
    <label for="status">Status</label>
    <select name="status" id="status" class="form-control">
        <?php
            $statuses = ['assistido', 'planejado', 'em andamento', 'abandonado'];
            foreach ($statuses as $status) {
                $selected = ($filme['status'] ?? '') === $status ? 'selected' : '';
                echo "<option value='$status' $selected>$status</option>";
            }
        ?>
    </select>
</div>

<div class="form-group">
    <label for="duracao">Duração (minutos)</label>
    <input type="number" name="duracao" id="duracao" class="form-control" value="<?= esc($filme['duracao'] ?? '') ?>">
</div>

<button type="submit" class="btn btn-success"><?= isset($filme) ? 'Atualizar' : 'Salvar' ?></button>
<?= form_close(); ?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Produções</title>
</head>
<script>
    function confirma(){
        return confirm('Deseja excluir esta produção?');
    }
</script>
<body>
    <div class="container">
        <?= anchor(base_url('producao/create'), 'Nova Produção', ['class' => 'btn btn-success']) ?>
        <?= anchor(base_url('recomendacoes'), 'Recomendações', ['class' => 'btn btn-info', 'style' => 'margin-left:10px;']) ?>
        <table class="table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Filme</th>
                    <th>Nota</th>
                    <th>Comentário</th>
                    <th>Status</th>
                    <th>Duração (minutos)</th>
                    <th>Pôster</th>
                    <th>Diretor</th>
                    <th>Elenco</th>
                    <th>Gêneros</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($producoes as $producao): ?>
                    <tr>
                        <td><?= $producao['id'] ?></td>
                        <td><?= $producao['filme'] ?></td>
                        <td><?= $producao['nota'] ?></td>
                        <td><?= $producao['comentario'] ?></td>
                        <td><?= $producao['status'] ?></td>
                        <td><?= $producao['duracao'] ?? '-' ?></td>
                        <td>
                            <?php if (!empty($producao['poster'])): ?>
                                <img src="<?= esc($producao['poster']) ?>" style="height:80px;">
                            <?php endif; ?>
                        </td>
                        <td><?= esc($producao['diretor']) ?></td>
                        <td><?= esc($producao['elenco']) ?></td>
                        <td><?= esc($producao['generos']) ?></td>
                        <td>
                            <?= anchor('producao/edit/'.$producao['id'], 'Editar') ?> |
                            <?= anchor('producao/delete/'.$producao['id'], 'Excluir', ['onclick' => 'return confirma()']) ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Produções</title>
    <link rel="stylesheet" href="<?= base_url('css/style.css') ?>">
</head>
<body>
<div class="container">
    <!-- Botões principais -->
    <div style="margin-bottom:20px;">
        <?= anchor(base_url('producao/create'), 'Nova Produção', ['class' => 'btn btn-success']) ?>
        <?= anchor(base_url('recomendacoes'), 'Recomendações', ['class' => 'btn btn-info', 'style' => 'margin-left:10px;']) ?>
    </div>

    <!-- Filtro por status -->
    <div class="form-group" style="margin-bottom:20px;">
        <label for="filtroStatus"><strong>Filtrar por Status:</strong></label>
        <select id="filtroStatus" class="form-control" style="width:200px; display:inline-block; margin-left:10px;">
            <option value="<?= base_url('producoes') ?>" <?= empty($statusAtual) ? 'selected' : '' ?>>Todos</option>
            <option value="<?= base_url('producoes/assistido') ?>" <?= ($statusAtual ?? '') === 'assistido' ? 'selected' : '' ?>>Assistido</option>
            <option value="<?= base_url('producoes/planejado') ?>" <?= ($statusAtual ?? '') === 'planejado' ? 'selected' : '' ?>>Planejado</option>
            <option value="<?= base_url('producoes/em andamento') ?>" <?= ($statusAtual ?? '') === 'em andamento' ? 'selected' : '' ?>>Em andamento</option>
            <option value="<?= base_url('producoes/abandonado') ?>" <?= ($statusAtual ?? '') === 'abandonado' ? 'selected' : '' ?>>Abandonado</option>
        </select>
    </div>

    <script>
        document.getElementById('filtroStatus').addEventListener('change', function() {
            window.location.href = this.value;
        });
    </script>

    <!-- Pesquisa por filme -->
    <div class="form-group" style="margin-bottom:20px; position: relative;">
        <label for="buscaFilme"><strong>Pesquisar por Filme:</strong></label>
        <input type="text" id="buscaFilme" name="q" class="form-control" 
               style="width:300px; display:inline-block;" 
               value="<?= esc($buscaNome ?? '') ?>" placeholder="Digite o nome do filme" autocomplete="off">
        <div id="resultadosBusca" style="display:none;"></div>
    </div>

    <script>
        document.getElementById("buscaFilme").addEventListener("keyup", function() {
            let query = this.value;
            if(query.length < 3){
                document.getElementById("resultadosBusca").style.display = "none";
                return;
            }
            fetch("<?= base_url('producao/search') ?>?q=" + encodeURIComponent(query))
                .then(res => res.json())
                .then(data => {
                    let resultadosDiv = document.getElementById("resultadosBusca");
                    resultadosDiv.innerHTML = "";
                    if(data.length > 0){
                        data.forEach(filme => {
                            let item = document.createElement("div");
                            item.innerHTML = `<img src="${filme.poster}" onerror="this.style.display='none'"> ${filme.titulo} (${filme.ano})`;
                            item.addEventListener("click", function(){
                                document.getElementById("buscaFilme").value = filme.titulo;
                                resultadosDiv.style.display = "none";
                            });
                            resultadosDiv.appendChild(item);
                        });
                        resultadosDiv.style.display = "block";
                    } else {
                        resultadosDiv.style.display = "none";
                    }
                });
        });

        document.getElementById("buscaFilme").addEventListener("keydown", function(e){
            if(e.key === "Enter"){
                e.preventDefault();
                let nome = this.value.trim();
                if(nome){
                    let statusParam = "<?= $statusAtual ?? '' ?>";
                    let url = "<?= base_url('producoes') ?>";
                    if(statusParam) url += "/" + encodeURIComponent(statusParam);
                    window.location.href = url + "?q=" + encodeURIComponent(nome);
                }
            }
        });
    </script>

    <!-- Título dinâmico -->
    <h2>
        <?php if (!empty($statusAtual)): ?>
            Listando produções com status: <strong><?= ucfirst($statusAtual) ?></strong>
        <?php else: ?>
            Listando todas as produções
        <?php endif; ?>
        <?php if (!empty($buscaNome)): ?>
            | Pesquisando por: <strong><?= esc($buscaNome) ?></strong>
        <?php endif; ?>
    </h2>

    <!-- Tabela de produções -->
    <table class="table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Filme</th>
                <th>Nota</th>
                <th>Comentário</th>
                <th>Status</th>
                <th>Duração (min)</th>
                <th>Pôster</th>
                <th>Diretor</th>
                <th>Elenco</th>
                <th>Gêneros</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php if(!empty($producoes)): ?>
                <?php foreach($producoes as $producao): ?>
                    <tr>
                        <td><?= $producao['id'] ?></td>
                        <td><?= $producao['filme'] ?></td>
                        <td><?= $producao['nota'] ?></td>
                        <td><?= $producao['comentario'] ?></td>
                        <td><?= $producao['status'] ?></td>
                        <td><?= $producao['duracao'] ?? '-' ?></td>
                        <td>
                            <?php if(!empty($producao['poster'])): ?>
                                <img src="<?= esc($producao['poster']) ?>" style="height:80px;">
                            <?php endif; ?>
                        </td>
                        <td><?= esc($producao['diretor']) ?></td>
                        <td><?= esc($producao['elenco']) ?></td>
                        <td><?= esc($producao['generos']) ?></td>
                        <td>
                            <?= anchor('producao/view/'.$producao['id'], 'Visualizar Filme') ?> |
                            <?= anchor('producao/edit/'.$producao['id'], 'Editar') ?> |
                            <?= anchor('producao/delete/'.$producao['id'], 'Excluir', ['onclick'=>'return confirm("Deseja realmente excluir?")']) ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="11">Nenhuma produção encontrada.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
</body>
</html>

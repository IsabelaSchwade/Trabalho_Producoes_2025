<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Filmes</title>
    <link rel="stylesheet" href="<?= base_url('css/style.css') ?>">
    <style>
        .movie-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
            gap: 20px;
            padding: 20px 0;
            justify-items: center;
        }

        .movie-card {
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
            text-decoration: none;
            color: inherit;
            transition: transform 0.2s;
            cursor: pointer;
        }

        .movie-card:hover {
            transform: scale(1.05);
        }

        .movie-card img {
            width: 100%;
            height: auto;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }

        .movie-card-title {
            margin-top: 10px;
            font-size: 1.1em;
            font-weight: bold;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            max-width: 100%;
        }

        #resultadosBusca div {
            background-color: #f1f1f1;
            padding: 5px;
            cursor: pointer;
        }

        #resultadosBusca div:hover {
            background-color: #ddd;
        }
    </style>
</head>
<body>
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
            window.location.href = this.value;
        });
    </script>

    <div class="form-group" style="margin-bottom:20px; position: relative;">
        <label for="buscaFilme"><strong>Pesquisar por Filme:</strong></label>
        <input type="text" id="buscaFilme" name="q" class="form-control" 
                style="width:300px; display:inline-block;" 
                value="<?= esc($buscaNome ?? '') ?>" placeholder="Digite o nome do filme" autocomplete="off">
        <div id="resultadosBusca" style="display:none; position:absolute; z-index:1000; width:300px;"></div>
    </div>

    <script>
        document.getElementById("buscaFilme").addEventListener("keyup", function() {
            let query = this.value;
            if(query.length < 3){
                document.getElementById("resultadosBusca").style.display = "none";
                return;
            }
            fetch("<?= base_url('filmes/search') ?>?q=" + encodeURIComponent(query))
                .then(res => res.json())
                .then(data => {
                    let resultadosDiv = document.getElementById("resultadosBusca");
                    resultadosDiv.innerHTML = "";
                    if(data.length > 0){
                        data.forEach(filme => {
                            let item = document.createElement("div");
                            item.innerHTML = `<img src="${filme.capa}" onerror="this.style.display='none'" style="height:50px; vertical-align:middle;"> ${filme.filme} (${filme.ano || ''})`;
                            item.addEventListener("click", function(){
                                document.getElementById("buscaFilme").value = filme.filme;
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
                    let url = "<?= base_url('filmes') ?>";
                    if(statusParam) url += "/" + encodeURIComponent(statusParam);
                    window.location.href = url + "?q=" + encodeURIComponent(nome);
                }
            }
        });
    </script>

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

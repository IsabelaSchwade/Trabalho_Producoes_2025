<?php

namespace App\Controllers;
use App\Controllers\BaseController;
use App\Models\FilmeModel; // importa o arquivo que será utilizado pra acessar o banco
/*
BaseController: permite acessar recursos básicos do CodeIgniter, como requisições HTTP ($this->request) e respostas ($this->response).

FilmeModel: Model que faz a comunicação com o banco de dados (tabela de filmes).
*/

class Filme extends BaseController
{
    private $filmeModel;

     // permite que todos os métodos acessem o banco de dados
    public function __construct()
    {
        $this->filmeModel = new FilmeModel();
       
    }

    public function index($status = null)
    {
        $query = $this->request->getGet('q');
        $builder = $this->filmeModel;
        //Inicializa o builder (objeto de consulta do Model), permitindo adicionar filtros dinamicamente.
        //$this->request vem do BaseController e representa a requisição HTTP atual.
       //getGet('q') pega o valor do parâmetro q enviado via URL (GET), por exemplo:
       //Serve para fazer uma busca por título de filme.
        if ($status) {
            //Se houver status, adiciona uma cláusula WHERE status = $status ao query builder.
            $builder = $builder->where('status', $status);
        }

        if ($query) {
           // Se houver termo de busca, adiciona uma cláusula LIKE no campo filme.
           //Isso permite buscar filmes que contenham o termo em qualquer posição do título.
            $builder = $builder->like('filme', $query);
        }

        $filmes = $builder->findAll();

        /*
        Executa a consulta construída até aqui.

    findAll() retorna todos os registros que batem com os filtros.

    Retorna um array de arrays associativos, cada um representando um filme.
        */
        return view('filmes', [
            'filmes' => $filmes,
            'statusAtual' => $status,
            'buscaNome' => $query
        ]);
    }

    public function excluir($id) // utiliza o id para identificar qual filme que vai ser excluido
    {
        if ($this->filmeModel->delete($id)) {// seleciona o filme com o id
             return view("messages", [
                'message' => 'Filme excluído com sucesso'
            ]);
        } else {
            echo "Ocorreu um erro";
        }
    }

    public function formulario() // exibe o formulario
    {
        return view('form');
    }

    public function cadastrar()
    {
        $postData = $this->request->getPost(); // pega os dados do formulario
        $apiData = $this->buscarDadosDoFilme($postData['filme']);
        //Chama o método privado buscarDadosDoFilme() para obter dados da API OMDB pelo título do filme.
        if ($apiData) {
            $postData = array_merge($postData, $apiData);
            //Mescla os dados do formulário com os dados da API, caso existam.
        }

        if ($this->filmeModel->save($postData)) {
            //Salva os dados no banco com $this->filmeModel->save().
            return view("messages", [
                'message' => 'Filme cadastrado com sucesso'
            ]);
        } else {
            echo "Ocorreu um erro";
        }
    }

    public function editar($id)
    {
        $filme = $this->filmeModel->find($id);
        return view('form', ['filme' => $filme]); // Busca um filme pelo ID e exibe o formulário pré-preenchido com os dados do filme.
    }

    public function atualizar($id)
    {
        $data = $this->request->getPost();
        //Captura os dados enviados pelo formulário.
        if ($this->filmeModel->update($id, $data)) {
            //Atualiza o filme no banco.
            return view("messages", [
                'message' => 'Filme atualizado com sucesso'
            ]);
        } else {
            echo "Ocorreu um erro";
        }
    }

    public function recomendacoes()
    {
        $filmes = $this->filmeModel // seleciona todos os filmes com a nota maior igual a 7 e pega a coluna generos
            ->select('generos')
            ->where('nota >=', 7.0)
            ->findAll();

        $generoCount = [];

        foreach ($filmes as $f) { // conta quantos generos tem cada filme bem avaliado
            if (!empty($f['generos'])) {
                $generos = explode(',', $f['generos']); // explode(',', $f['generos']) → transforma a string de gêneros separada por vírgula em um array
                foreach ($generos as $genero) {
                    $genero = trim($genero); //remove espaços antes ou depois.
                    $generoCount[$genero] = ($generoCount[$genero] ?? 0) + 1; // se o gênero ainda não existir no array $generoCount, inicializa com 0, depois soma 1.
                }
            }
        }

        arsort($generoCount); //em ordem decrescente de frequência, mantendo as chaves (os nomes dos gêneros).
        $generosFavoritos = array_keys/*retorna apenas o nome*/(array_slice($generoCount, 0, 3));
        //ordena os generos e seleciona os 3 mais bem avaliados
        $recomendacoes = [];
        foreach ($generosFavoritos as $genero) {
            $filme = $this->buscarFilmePorGenero($genero); // busca um filme daquele genero 
            if ($filme) {
                $recomendacoes[] = $filme;
            }
        }

        $totalMinutos = $this->filmeModel
            ->selectSum('duracao')
            ->where('status', 'assistido')
            ->first()['duracao'];

        return view('recomendacoes', [
            'totalMinutos' => $totalMinutos ?? 0,
            'recomendacoes' => $recomendacoes
        ]);
    }

    private function buscarFilmePorGenero($genero)
    {
        $apiKey = 'a0013cdf';
        $termos = urlencode($genero); // codifica o gênero para ser usado na URL
        $url = "http://www.omdbapi.com/?s={$termos}&type=movie&apikey={$apiKey}";

        /*
        $url → monta a URL da requisição para buscar filmes que contenham o gênero no título:

    s={$termos} → busca filmes cujo título contenha os termos fornecidos.

    type=movie → limita a busca a filmes (excluindo séries e episódios).

    apikey={$apiKey} → autenticação com a chave da API.
        */

        $client = \Config\Services::curlrequest(); // cria um cliente HTTP do CodeIgniter para fazer requisições externas.
        $response = $client->get($url); // envia uma requisição GET para a URL da OMDB e armazena a resposta em $response

        if ($response->getStatusCode() === 200) {
            $data = json_decode($response->getBody(), true);

            /*

            getBody() → obtém o conteúdo da resposta da API.
            json_decode(..., true) → converte o JSON retornado pela OMDB em array associativo PHP.
            */

            if (!empty($data['Search'])) {
                /*
                Verifica se a resposta possui a chave Search e se ela contém resultados.
                Search é um array de filmes retornados pela OMDB.
                */
                $filme = $data['Search'][0];
                return [
                    'titulo' => $filme['Title'],
                    'ano' => $filme['Year'],
                    'capa' => $filme['Poster']

                    /*

                    $filme = $data['Search'][0] → pega apenas o primeiro filme do resultado da busca.

                    return [...] → retorna um array associativo com os dados relevantes:

                    'titulo' → título do filme (Title da OMDB).

                    'ano' → ano de lançamento (Year da OMDB).

                    'capa' → URL da imagem do pôster (Poster da OMDB).
                    */
                ];
            }
        }

        return null;
    }

    private function buscarDadosDoFilme($titulo)
    {
        $apiKey = 'a0013cdf';
        $titulo = urlencode($titulo); // $titulo = urlencode($titulo) → codifica o título para ser usado na URL (O Poderoso Chefão → O+Poderoso+Chef%C3%A3o).
        $url = "http://www.omdbapi.com/?t={$titulo}&apikey={$apiKey}&plot=full";

        /*

        $url → monta a URL da requisição:

        t={$titulo} → busca pelo título exato do filme.

        apikey={$apiKey} → autenticação.

        plot=full → retorna a sinopse completa.
        */
        $client = \Config\Services::curlrequest();
        $response = $client->get($url);

        if ($response->getStatusCode() === 200) {
            $data = json_decode($response->getBody(), true);

            /*
            getBody() → obtém o conteúdo da resposta da API.

            json_decode(..., true) → converte o JSON em array associativo PHP.
            */
            if ($data['Response'] === 'True') {
                return [
                    'diretor' => $data['Director'] ?? null,
                    'elenco' => $data['Actors'] ?? null,
                    'capa' => $data['Poster'] ?? null,
                    'sinopse' => $data['Plot'] ?? null,
                    'generos' => $data['Genre'] ?? null,
                ];
            }
        }

        return null;
    }

    public function search()
    {
        $termo = $this->request->getGet('q');

        /*
        tGet('q') → captura o valor do parâmetro GET q da URL.

Exemplo: /filme/search?q=Matrix → $termo = "Matrix"
*/
        $filmes = [];

        if ($termo) {
            //Verifica se o usuário forneceu algum termo de busca.

//Se $termo estiver vazio, o método retornará apenas o array vazio.
            $filmes = $this->filmeModel
                ->like('filme', $termo)
                ->findAll();

                /*
                $this->filmeModel → referência ao Model de filmes.

like('filme', $termo) → adiciona um filtro LIKE na coluna filme, buscando títulos que contenham o termo em qualquer posição.

Exemplo: se $termo = "Matrix", ele encontrará "Matrix", "The Matrix Reloaded", etc.

findAll() → executa a consulta e retorna todos os registros que batem com o filtro como um array de arrays associativos.
                */
        }

        return $this->response->setJSON($filmes);

        /*
        $this->response → objeto de resposta HTTP do CodeIgniter.

setJSON($filmes) → converte o array $filmes em JSON e envia como resposta.
        */
    }

    public function view($id)
    {
        $filme = $this->filmeModel->find($id);

        if (!$filme) {
            return view('messages', [
                'message' => 'Filme não encontrado!'
            ]);
        }

        return view('visualizar_filme', [
            'filme' => $filme
        ]);
    }
}

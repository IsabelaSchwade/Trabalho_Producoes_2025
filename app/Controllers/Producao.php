<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\ProducaoModel;
use CodeIgniter\HTTP\ResponseInterface;

class Producao extends BaseController
{
    private $producaoModel;
    public function __construct(){
        $this->producaoModel = new ProducaoModel();
    }

public function index($status = null)
{
    $query = $this->request->getGet('q');

    $builder = $this->producaoModel;

    if ($status) {
        $builder = $builder->where('status', $status);
    }

    if ($query) {
        $builder = $builder->like('filme', $query);
    }

    $producoes = $builder->findAll();

    return view('producoes', [
        'producoes' => $producoes,
        'statusAtual' => $status,
        'buscaNome' => $query
    ]);
}



    public function delete($id){
        if($this->producaoModel->delete($id)){
            echo view('messages',[
                'message' => 'Produção excluída com sucesso!'
            ]);
        }else
            echo "Erro";
    }

    public function create(){
        return view('form');
    }

    public function store(){
    $postData = $this->request->getPost();
    
    // Buscar dados adicionais da OMDb
    $apiData = $this->fetchMovieData($postData['filme']);

    if ($apiData) {
        $postData = array_merge($postData, $apiData);
    }

    if ($this->producaoModel->save($postData)){
        return view("messages", [
            'message' => 'Produção salva com sucesso'
        ]);
    } else {
        echo "Ocorreu um erro";
    }
}


    public function edit($id)
    {
        $producao = $this->producaoModel->find($id);
        return view('form', ['producao' => $producao]);
    }

    public function update($id)
    {
        $data = $this->request->getPost();
        if ($this->producaoModel->update($id, $data)) {
            return redirect()->to('/producoes')->with('message', 'Produção atualizada com sucesso!');
        } else {
            return redirect()->back()->withInput()->with('error', 'Erro ao atualizar a produção.');
        }
    }


    //------------------------------------------------------------------------------------------------------
   public function recomendacoes()
{
    // primeiro selecionar no banco os generos que foram avaliados com notas altas
    $producoes = $this->producaoModel // ta selecionando o model responsavel por pegar os dados do banco
        ->select('generos')
        ->where('nota >=', 7.0)
        ->findAll(); // busca todos os generos que atendem os criterios

    $generoCount = []; // armazena tudo em um array

    foreach ($producoes as $p) {
        if (!empty($p['generos'])) { // verifica se não tá vazio
            $generos = explode(',', $p['generos']);// separa a string por vírgula
            foreach ($generos as $genero) {
                $genero = trim($genero); // remove espaços extras 
                $generoCount[$genero] = ($generoCount[$genero] ?? 0) + 1;
            }
        }
    }

    arsort($generoCount);
    $generosFavoritos = array_keys(array_slice($generoCount, 0, 3)); // top 3

    // Passo 2: Buscar recomendações da OMDb com base nos gêneros
    $recomendacoes = [];
    foreach ($generosFavoritos as $genero) {
        $filme = $this->buscarFilmePorGenero($genero);
        if ($filme) {
            $recomendacoes[] = $filme;
        }
    }

    // Passo 3: Total de minutos assistidos
    $totalMinutos = $this->producaoModel
        ->selectSum('duracao')
        ->where('status', 'assistido')
        ->first()['duracao'];

    return view('recomendacoes', [
        'totalMinutos' => $totalMinutos ?? 0,
        'recomendacoes' => $recomendacoes
    ]);
}

//----------------------------------------------------------------------------------------

private function buscarFilmePorGenero($genero)
{
    $apiKey = 'a0013cdf';
    $termos = urlencode($genero);
    $url = "http://www.omdbapi.com/?s={$termos}&type=movie&apikey={$apiKey}";

    $client = \Config\Services::curlrequest();
    $response = $client->get($url);

    if ($response->getStatusCode() === 200) {
        $data = json_decode($response->getBody(), true);

        if (!empty($data['Search'])) {
            $filme = $data['Search'][0]; // pegar o primeiro resultado
            return [
                'titulo' => $filme['Title'],
                'ano' => $filme['Year'],
                'poster' => $filme['Poster']
            ];
        }
    }

    return null;
}


private function fetchMovieData($titulo)
{
    $apiKey = 'a0013cdf';
    $titulo = urlencode($titulo);
    $url = "http://www.omdbapi.com/?t={$titulo}&apikey={$apiKey}&plot=full";

    $client = \Config\Services::curlrequest();
    $response = $client->get($url);

    if ($response->getStatusCode() === 200) {
        $data = json_decode($response->getBody(), true);

        if ($data['Response'] === 'True') {
            return [
                'diretor' => $data['Director'] ?? null,
                'elenco' => $data['Actors'] ?? null,
                'poster' => $data['Poster'] ?? null,
                'sinopse' => $data['Plot'] ?? null,
                'generos' => $data['Genre'] ?? null, // 
            ];
        }
    }

    return null;
}

public function search()
{
    $termo = $this->request->getGet('q'); // pega parâmetro "q" da URL
    $producoes = [];

    if ($termo) {
        $producoes = $this->producaoModel
            ->like('filme', $termo) // pesquisa pelo título
            ->findAll();
    }

    return view('producoes', [
        'producoes' => $producoes,
        'termo' => $termo
    ]);
}

public function view($id)
{
    $producao = $this->producaoModel->find($id);

    if (!$producao) {
        return view('messages', [
            'message' => 'Produção não encontrada!'
        ]);
    }

    return view('visualizar_filme', [
        'producao' => $producao
    ]);
}


}

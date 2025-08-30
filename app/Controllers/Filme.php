<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\FilmeModel;
use CodeIgniter\HTTP\ResponseInterface;

class Filme extends BaseController
{
    private $filmeModel;

    public function __construct()
    {
        $this->filmeModel = new FilmeModel();
    }

    public function index($status = null)
    {
        $query = $this->request->getGet('q');
        $builder = $this->filmeModel;

        if ($status) {
            $builder = $builder->where('status', $status);
        }

        if ($query) {
            $builder = $builder->like('filme', $query);
        }

        $filmes = $builder->findAll();

        return view('filmes', [
            'filmes' => $filmes,
            'statusAtual' => $status,
            'buscaNome' => $query
        ]);
    }

    public function excluir($id)
    {
        if ($this->filmeModel->delete($id)) {
            echo view('messages', [
                'message' => 'Filme excluído com sucesso!'
            ]);
        } else {
            echo "Erro";
        }
    }

    public function formulario()
    {
        return view('form');
    }

    public function cadastrar()
    {
        $postData = $this->request->getPost();
        $apiData = $this->buscarDadosDoFilme($postData['filme']);

        if ($apiData) {
            $postData = array_merge($postData, $apiData);
        }

        if ($this->filmeModel->save($postData)) {
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
        return view('form', ['filme' => $filme]);
    }

    public function atualizar($id)
    {
        $data = $this->request->getPost();
        if ($this->filmeModel->update($id, $data)) {
            return redirect()->to('/filmes')->with('message', 'Filme atualizado com sucesso!');
        } else {
            return redirect()->back()->withInput()->with('error', 'Erro ao atualizar o filme.');
        }
    }

    public function recomendacoes()
    {
        $filmes = $this->filmeModel
            ->select('generos')
            ->where('nota >=', 7.0)
            ->findAll();

        $generoCount = [];

        foreach ($filmes as $f) {
            if (!empty($f['generos'])) {
                $generos = explode(',', $f['generos']);
                foreach ($generos as $genero) {
                    $genero = trim($genero);
                    $generoCount[$genero] = ($generoCount[$genero] ?? 0) + 1;
                }
            }
        }

        arsort($generoCount);
        $generosFavoritos = array_keys(array_slice($generoCount, 0, 3));

        $recomendacoes = [];
        foreach ($generosFavoritos as $genero) {
            $filme = $this->buscarFilmePorGenero($genero);
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
        $termos = urlencode($genero);
        $url = "http://www.omdbapi.com/?s={$termos}&type=movie&apikey={$apiKey}";

        $client = \Config\Services::curlrequest();
        $response = $client->get($url);

        if ($response->getStatusCode() === 200) {
            $data = json_decode($response->getBody(), true);

            if (!empty($data['Search'])) {
                $filme = $data['Search'][0];
                return [
                    'titulo' => $filme['Title'],
                    'ano' => $filme['Year'],
                    'capa' => $filme['Poster']
                ];
            }
        }

        return null;
    }

    private function buscarDadosDoFilme($titulo)
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
        $filmes = [];

        if ($termo) {
            $filmes = $this->filmeModel
                ->like('filme', $termo)
                ->findAll();
        }

        return $this->response->setJSON($filmes);
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

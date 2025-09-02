<?php

namespace App\Controllers;
use App\Controllers\BaseController;
use App\Models\FilmeModel;

class Filme extends BaseController
{
    private $filmeModel;

    public function __construct(){
        $this->filmeModel = new FilmeModel();
    }

    private function buscarDadosDoFilme($tituloFilme){
        $chaveApi = 'a0013cdf';
        $tituloCodificado = urlencode($tituloFilme); 
        $url = "http://www.omdbapi.com/?t={$tituloCodificado}&apikey={$chaveApi}&plot=full";
       
        $cliente = \Config\Services::curlrequest();
        $resposta = $cliente->get($url);
    
        if ($resposta->getStatusCode() === 200) {
            $dadosApi = json_decode($resposta->getBody(), true);

            if ($dadosApi['Response'] === 'True') {
                return [
                    'diretor' => $dadosApi['Director'] ?? null,
                    'elenco'  => $dadosApi['Actors'] ?? null,
                    'capa'    => $dadosApi['Poster'] ?? null,
                    'sinopse' => $dadosApi['Plot'] ?? null,
                    'generos' => $dadosApi['Genre'] ?? null,
                ];
            }
        }

        return null;
    }

      public function formulario(){
        return view('form');
    }

    public function cadastrar() {
        $dadosFormulario = $this->request->getPost();
        $dadosApi = $this->buscarDadosDoFilme($dadosFormulario['filme']);

        if ($dadosApi) {
            $dadosFormulario = array_merge($dadosFormulario, $dadosApi);
        }

        if ($this->filmeModel->save($dadosFormulario)) {
            return view("messages", [
                'message' => 'Filme cadastrado com sucesso'
            ]);
        } else {
            echo "Ocorreu um erro";
        }
    }

        public function excluir($idFilme) {
        if ($this->filmeModel->delete($idFilme)) {
            return view("messages", [
                'message' => 'Filme excluído com sucesso'
            ]);
        } else {
            echo "Ocorreu um erro";
        }
    }


    public function editar($idFilme){ 
        $filme = $this->filmeModel->find($idFilme);

        return view('form', ['filme' => $filme]);
    }

    public function atualizar($idFilme){
        $dadosAtualizados = $this->request->getPost();

        if ($this->filmeModel->update($idFilme, $dadosAtualizados)) {
            return view("messages", [
                'message' => 'Filme atualizado com sucesso'
            ]);
        } else {
            echo "Ocorreu um erro";
        }
    }

    public function buscarPorNome()
    {
        $termo = $this->request->getGet('q');
        $resultadoBusca = [];

        if ($termo) { 
            $resultadoBusca = $this->filmeModel
                ->like('filme', $termo)
                ->findAll(); 
        }

        return $this->response->setJSON($resultadoBusca);
    }
    public function index($statusAtual = null){
        $termoBusca = $this->request->getGet('q'); 
        $consulta = $this->filmeModel;

        if ($statusAtual) {
            $consulta = $consulta->where('status', $statusAtual);
        }

        if ($termoBusca) {
            $consulta = $consulta->like('filme', $termoBusca);
        }

        $listaFilmes = $consulta->findAll();
        return view('filmes', [
            'filmes'      => $listaFilmes,
            'statusAtual' => $statusAtual,
            'buscaNome'   => $termoBusca
        ]);
    }



     private function buscarFilmePorGenero($genero){
        $chaveApi = 'a0013cdf';
        $generoCodificado = urlencode($genero);
        $url = "http://www.omdbapi.com/?s={$generoCodificado}&type=movie&apikey={$chaveApi}";

        $cliente = \Config\Services::curlrequest();
        $resposta = $cliente->get($url);

        if ($resposta->getStatusCode() === 200) {
            $dadosApi = json_decode($resposta->getBody(), true);
            
            if (!empty($dadosApi['Search'])) { 
                $filmeApi = $dadosApi['Search'][0]; 
                return [
                    'titulo' => $filmeApi['Title'],
                    'ano'    => $filmeApi['Year'],
                    'capa'   => $filmeApi['Poster']
                ];
            }
        }

        return null;
    }

    public function recomendacoes(){
        $filmesBemAvaliados = $this->filmeModel
            ->select('generos')
            ->where('nota >=', 7.0)
            ->findAll();
        
        $contagemGeneros = [];

        foreach ($filmesBemAvaliados as $filme) {
            if (!empty($filme['generos'])) {
                $generos = explode(',', $filme['generos']); 
                foreach ($generos as $genero) {
                    $genero = trim($genero); 
                    $contagemGeneros[$genero] = ($contagemGeneros[$genero] ?? 0) + 1;
                }
            }
        }

        arsort($contagemGeneros); 
        $generosFavoritos = array_keys(array_slice($contagemGeneros, 0, 3)); 

        $listaRecomendacoes = [];
        foreach ($generosFavoritos as $genero) {
            $filmeRecomendado = $this->buscarFilmePorGenero($genero);
            if ($filmeRecomendado) {
                $listaRecomendacoes[] = $filmeRecomendado;
            }
        }

        $totalMinutosAssistidos = $this->filmeModel
            ->selectSum('duracao')
            ->where('status', 'assistido')
            ->first()['duracao'];

        return view('recomendacoes', [
            'totalMinutos'  => $totalMinutosAssistidos ?? 0,
            'recomendacoes' => $listaRecomendacoes
        ]);
    }

   

    public function view($idFilme)
    {
        $filme = $this->filmeModel->find($idFilme);

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

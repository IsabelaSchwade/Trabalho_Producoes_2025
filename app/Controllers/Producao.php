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

    public function index()
    {
        return view('producoes', [
            'producoes' => $this->producaoModel->findAll()
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
        if ($this->producaoModel->save($this->request->getPost())){
            return view("messages",[
                'message' => 'Produção salva com sucesso'
            ]);
        } else{
            echo"Ocorreu um erro";
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

    public function recomendacoes()
{
    // Soma das durações apenas de filmes assistidos
    $totalMinutos = $this->producaoModel
        ->selectSum('duracao')
        ->where('status', 'assistido')
        ->first()['duracao'];

    return view('recomendacoes', [
        'totalMinutos' => $totalMinutos ?? 0
    ]);
}

}

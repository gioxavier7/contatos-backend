<?php

require_once __DIR__ . '/../model/Contato.php';

/**
 * controller responsável pelas requisições de Contatos
 * data: 23/12/2025
 * dev: Giovanna Soares Xavier profissao
 */

class ContatoController{
    private $model;

    public function __construct()
    {
        $this->model = new Contato();
        header('Content-Type: application/json; charset=utf-8');
    }

    //listar todos contatos
    public function listarTodos()
    {
        try{
            $contatos = $this->model->listar();

            http_response_code(200);
            echo json_encode([
                'success' => true,
                'data' => $contatos
            ]);
        }catch(Exception $e){
            $this->error($e->getMessage());
        }
    }

    //buscar contato específico por id
    public function buscarContato($id)
    {
        if(!is_numeric($id)){
            return $this->erro('ID inválido', 400);
        }

        $contato = $this->model->buscarPorId((int)$id);

        if(!$contato){
            return $this->erro('Contato não encontrado', 404);
        }

        http_response_code(200);
        echo json_encode([
            'success' => true,
            'data' => $contato
        ]);
    }

    //cadastrar um novo contato
    public function cadastrarContato()
    {
        $dados = json_decode(file_get_contents('php://input'), true);

        if (!$this->validarCamposObrigatorios($dados)) {
            return;
        }

        try {
            $id = $this->model->criar($dados);
            $contato = $this->model->buscarPorId($id);

            http_response_code(201);
            echo json_encode([
                'success' => true,
                'message' => 'Contato cadastrado com sucesso!',
                'data' => $contato
            ]);
        } catch (Exception $e) {
            $this->erro($e->getMessage());
        }
    }


    //atualizar um contato
    public function atualizarContato($id)
    {
        if (!is_numeric($id)) {
            return $this->erro('ID inválido', 400);
        }

        $dados = json_decode(file_get_contents('php://input'), true);

        if (!$this->validarCamposObrigatorios($dados, false)) {
            return;
        }

        try {
            $atualizado = $this->model->atualizar((int) $id, $dados);

            if (!$atualizado) {
                return $this->erro('Erro ao atualizar contato', 500);
            }

            $contatoAtualizado = $this->model->buscarPorId((int) $id);

            http_response_code(200);
            echo json_encode([
                'success' => true,
                'message' => 'Contato atualizado com sucesso!',
                'data' => $contatoAtualizado
            ]);
        } catch (Exception $e) {
            $this->erro($e->getMessage());
        }
    }


    //deletar um contato
    public function deletarContato($id)
    {
        if (!is_numeric($id)) {
            return $this->erro('ID inválido', 400);
        }

        try {
            $removido = $this->model->deletar((int)$id);

            if (!$removido) {
                return $this->erro('Erro ao remover contato', 500);
            }

            http_response_code(200);
            echo json_encode([
                'success' => true,
                'message' => 'Contato removido com sucesso'
            ]);
        } catch (Exception $e) {
            $this->erro($e->getMessage());
        }
    }

    //validação de campos obrigatórios
    private function validarCamposObrigatorios(array $dados = null, bool $criar = true): bool
    {
        if (!$dados) {
            $this->erro('Payload inválido', 400);
            return false;
        }

        $campos = ['nome', 'email', 'data_nascimento', 'profissao'];

        foreach ($campos as $campo) {
            if ($criar && empty($dados[$campo])) {
                $this->erro("Campo obrigatório ausente: {$campo}", 400);
                return false;
            }
        }

        if (!filter_var($dados['email'], FILTER_VALIDATE_EMAIL)) {
            $this->erro('E-mail inválido', 400);
            return false;
        }

        return true;
    }

   //resposta de erro padronizada
    private function erro(string $mensagem, int $status = 500)
    {
        http_response_code($status);
        echo json_encode([
            'success' => false,
            'message' => $mensagem
        ]);
    }
}
<?php

require_once __DIR__ . '/../config/Database.php';

/**
 * model responsável pelas operações de persistencia da entidade Contato
 * data: 23/12/2025
 * dev: Giovanna Soares Xavier
 */

class Contato{
   private $conn;
   private $table = 'contatos';
   
   public function __construct()
   {
        $this->conn = Database::getInstance()->getConnection();
   }

   //retorna todos os contatos cadastrados
   public function listar(): array
   {
        $sql = "SELECT
                    c.id,
                    c.nome,
                    c.email,
                    c.data_nascimento,
                    c.permite_notificacao_email,
                    p.nome AS profissao
                FROM contatos c
                INNER JOIN profissoes p ON p.id = c.id_profissao
                ORDER BY c.nome";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll();
   }

   //busca um contato pelo id
   public function buscarPorId(int $id): ?array
   {
        $sql = "SELECT 
                    c.id,
                    c.nome,
                    c.email,
                    c.data_nascimento,
                    c.permite_notificacao_email,
                    p.nome AS profissao
                FROM contatos c
                INNER JOIN profissoes p ON p.id = c.id_profissao
                WHERE c.id = :id
                LIMIT 1";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        $resultado = $stmt->fetch();
        return $resultado ?: null;
   }

   //cadastra um novo contato
   public function criar(array $dados): int
   {
        $sql = "INSERT INTO contatos
                    (nome, email, data_nascimento, permite_notificacao_email, id_profissao)
                VALUES
                    (:nome, :email, :data_nascimento, :permite_notificacao_email, :id_profissao)";

        $stmt = $this->conn->prepare($sql);

        $stmt->bindParam(':nome', $dados['nome']);
        $stmt->bindParam(':email', $dados['email']);
        $stmt->bindParam(':data_nascimento', $dados['data_nascimento']);
        $stmt->bindParam(':permite_notificacao_email', $dados['permite_notificacao_email'], PDO::PARAM_BOOL);
        $stmt->bindParam(':id_profissao', $dados['id_profissao'], PDO::PARAM_INT);

        $stmt->execute();

        return (int) $this->conn->lastInsertId();
   }

   //atualiza um contato existente
   public function atualizar(int $id, array $dados): bool
    {
        $sql = "UPDATE contatos SET
                    nome = :nome,
                    email = :email,
                    data_nascimento = :data_nascimento,
                    permite_notificacao_email = :permite_notificacao_email,
                    id_profissao = :id_profissao
                WHERE id = :id";

        $stmt = $this->conn->prepare($sql);

        $stmt->bindParam(':nome', $dados['nome']);
        $stmt->bindParam(':email', $dados['email']);
        $stmt->bindParam(':data_nascimento', $dados['data_nascimento']);
        $stmt->bindParam(':permite_notificacao_email', $dados['permite_notificacao_email'], PDO::PARAM_BOOL);
        $stmt->bindParam(':id_profissao', $dados['id_profissao'], PDO::PARAM_INT);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);

        return $stmt->execute();
    }
    
     //Remove um contato
    public function deletar(int $id): bool
    {
        $sql = "DELETE FROM contatos WHERE id = :id";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);

        return $stmt->execute();
    }
}
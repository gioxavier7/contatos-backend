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
                    id,
                    nome,
                    email,
                    data_nascimento,
                    profissao,
                    celular,
                    telefone,
                    notifica_email
                FROM contatos
                ORDER BY nome";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll();
    }

   //busca um contato pelo id
   public function buscarPorId(int $id): ?array
    {
        $sql = "SELECT
                    id,
                    nome,
                    email,
                    data_nascimento,
                    profissao,
                    celular,
                    telefone,
                    notifica_email
                FROM contatos
                WHERE id = :id
                LIMIT 1";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        $resultado = $stmt->fetch();
        return $resultado ?: null;
    }

    //cadastrar um novo contato
    public function criar(array $dados): int
    {
        $sql = "INSERT INTO contatos
                    (nome, email, data_nascimento, profissao, celular, telefone, notifica_email)
                VALUES
                    (:nome, :email, :data_nascimento, :profissao, :celular, :telefone, :notifica_email)";

        $stmt = $this->conn->prepare($sql);

        $stmt->bindParam(':nome', $dados['nome']);
        $stmt->bindParam(':email', $dados['email']);
        $stmt->bindParam(':data_nascimento', $dados['data_nascimento']);
        $stmt->bindParam(':profissao', $dados['profissao']);
        $stmt->bindParam(':celular', $dados['celular']);
        $stmt->bindParam(':telefone', $dados['telefone']);
        $stmt->bindParam(':notifica_email', $dados['notifica_email'], PDO::PARAM_BOOL);

        $stmt->execute();

        return (int) $this->conn->lastInsertId();
    }

    //atualiza um contato cadastrado
   public function atualizar(int $id, array $dados): bool
    {
        $sql = "UPDATE contatos SET
                    nome = :nome,
                    email = :email,
                    data_nascimento = :data_nascimento,
                    profissao = :profissao,
                    celular = :celular,
                    telefone = :telefone,
                    notifica_email = :notifica_email
                WHERE id = :id";

        $stmt = $this->conn->prepare($sql);

        $stmt->bindParam(':nome', $dados['nome']);
        $stmt->bindParam(':email', $dados['email']);
        $stmt->bindParam(':data_nascimento', $dados['data_nascimento']);
        $stmt->bindParam(':profissao', $dados['profissao']);
        $stmt->bindParam(':celular', $dados['celular']);
        $stmt->bindParam(':telefone', $dados['telefone']);
        $stmt->bindParam(':notifica_email', $dados['notifica_email'], PDO::PARAM_BOOL);
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

    //listar contatos com filtro e paginação
    public function listarComFiltro(
    ?string $nome,
    ?string $profissao,
    int $page,
    int $limit
    ): array {

        $offset = ($page - 1) * $limit;

        $sql = "SELECT *
                FROM contatos
                WHERE 1 = 1";

        $params = [];

        if ($nome) {
            $sql .= " AND nome LIKE :nome";
            $params[':nome'] = "%{$nome}%";
        }

        if ($profissao) {
            $sql .= " AND profissao LIKE :profissao";
            $params[':profissao'] = "%{$profissao}%";
        }

        $sql .= " ORDER BY nome
                LIMIT :limit OFFSET :offset";

        $stmt = $this->conn->prepare($sql);

        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }

        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);

        $stmt->execute();

        return $stmt->fetchAll();
    }

    //paginação
    public function contarRegistros(?string $nome, ?string $profissao): int
    {
        $sql = "SELECT COUNT(*) FROM contatos WHERE 1=1";
        $params = [];

        if ($nome) {
            $sql .= " AND nome LIKE :nome";
            $params[':nome'] = "%{$nome}%";
        }

        if ($profissao) {
            $sql .= " AND profissao LIKE :profissao";
            $params[':profissao'] = "%{$profissao}%";
        }

        $stmt = $this->conn->prepare($sql);

        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }

        $stmt->execute();

        return (int) $stmt->fetchColumn();
    }
}
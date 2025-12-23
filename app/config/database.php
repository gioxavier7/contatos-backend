<?php

/**
 * classe responsável pela conexão com o banco de dados via PDO
 * Data: 23/12/2025
 * Autora: Giovanna Soares Xavier
 */

class Database
{
    private static $instance = null;
    private $connection;

    private $host = 'localhost';
    private $db = 'db_contatos';
    private $user = 'root';
    private $pass = '';
    private $charset = 'utf8mb4';

    
    //construtor privado para evitar múltiplas instâncias
    private function __construct()
    {
        try {
            $dsn = "mysql:host={$this->host};dbname={$this->db};charset={$this->charset}";

            $this->connection = new PDO(
                $dsn,
                $this->user,
                $this->pass,
                [
                    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES   => false,
                ]
            );
        } catch (PDOException $e) {
            throw new Exception(
                'Erro ao conectar ao banco de dados: ' . $e->getMessage()
            );
        }
    }

     //retorna a instância única da classe
    public static function getInstance(): Database
    {
        if (self::$instance === null) {
            self::$instance = new Database();
        }

        return self::$instance;
    }

 
    //retorna a conexão PDO ativa
    public function getConnection(): PDO
    {
        return $this->connection;
    }
}

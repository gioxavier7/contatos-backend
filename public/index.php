<?php

// CONFIGURAÇÃO DE CORS
//permitir qualquer origem (para teste), em produção restrinja para o domínio do front
header("Access-Control-Allow-Origin: *");
//métodos permitidos
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
//headers permitidos
header("Access-Control-Allow-Headers: Content-Type, Authorization");
//tempo de cache do preflight
header("Access-Control-Max-Age: 86400");

//responder imediatamente requisições OPTIONS
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

// ROTAS DA API
require_once __DIR__ . '/../app/routes/api.php';

<?php

require_once __DIR__ . '/../controller/ContatoController.php';

/**
 * Router para API REST
 * data: 23/12/2025
 * dev: Giovanna Soares Xavier
 */

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$method = $_SERVER['REQUEST_METHOD'];

$uri = explode('/', trim($uri, '/'));

$controller = new ContatoController();

// rotas /contatos
if ($uri[0] === 'contatos') {

    // /contatos
    if (count($uri) === 1) {
        if ($method === 'GET') {
            $controller->listarTodos();
            return;
        }

        if ($method === 'POST') {
            $controller->cadastrarContato();
            return;
        }
    }

    // /contatos/{id}
    if (count($uri) === 2) {
        $id = $uri[1];

        if ($method === 'GET') {
            $controller->buscarContato($id);
            return;
        }

        if ($method === 'PUT') {
            $controller->atualizarContato($id);
            return;
        }

        if ($method === 'DELETE') {
            $controller->deletarContato($id);
            return;
        }
    }
}

//rota não encontrada
http_response_code(404);
echo json_encode([
    'success' => false,
    'message' => 'Rota não encontrada'
]);
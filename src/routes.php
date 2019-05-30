<?php

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;


$app->get('/astronauts', function (Request $request, Response $response, $args) {
    $stmt = $this->db->query('SELECT * FROM kosmonauti');
    $data = $stmt->fetchAll();
    return $response->withJson($data);
})->setName('astronauts');

$app->post('/astronauts', function (Request $request, Response $response, $args) {
    $data = $request->getParsedBody();

    try {
        $stmt = $this->db->prepare('INSERT INTO kosmonauti (first_name, last_name, birth_date, superpower) VALUES
                                      (:fn, :ln, :bd, :s)');
        $stmt->bindValue(':fn', $data['first_name']);
        $stmt->bindValue(':ln', $data['last_name']);
        $stmt->bindValue(':bd', $data['birth_date']);
        $stmt->bindValue(':s', $data['superpower']);
        $stmt->execute();

        return $response->withStatus(201);
    } catch(Exception $e) {
        return $response->withJson([
            'error' => $e->getMessage()
        ], 500);
    }
})->setName('createAstronaut');

$app->post('/edit-astronaut', function (Request $request, Response $response, array $args) {
    $data = $request->getParsedBody();
    try {
        $stmt = $this->db->prepare('UPDATE kosmonauti SET first_name = :fn, last_name=:ln, birth_date = :bd, superpower= :s
			WHERE id = :id');
        $stmt->bindValue(':id', $data['id']);
        $stmt->bindValue(':fn', $data['first_name']);
        $stmt->bindValue(':ln', $data['last_name']);
        $stmt->bindValue(':bd', $data['birth_date']);
        $stmt->bindValue(':s', $data['superpower']);
        $stmt->execute();

        return $response->withStatus(200);
    } catch(Exception $e) {
        return $response->withJson([
            'error' => $e->getMessage()
        ], 500);
    }
});

$app->post('/delete-astronaut', function (Request $request, Response $response, array $args) {
    $data = $request->getParsedBody();
    try {
        $stmt = $this->db->prepare('DELETE FROM kosmonauti
			WHERE id = :id');
        $stmt->bindValue(':id', $data['id']);
        $stmt->execute();

        return $response->withStatus(204);
    } catch(Exception $e) {
        return $response->withJson([
            'error' => $e->getMessage()
        ], 500);
    }
});



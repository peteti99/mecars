<?php
require __DIR__ . "/db.php";
require __DIR__ . "/auth_guard.php";

$data = json_decode(file_get_contents("php://input"), true) ?: [];
$id = (int)($data["id"] ?? 0);

if ($id <= 0) {
  http_response_code(400);
  echo json_encode(["ok"=>false, "error"=>"ID inválido"]);
  exit;
}

$st = $pdo->prepare("DELETE FROM clientes WHERE id = :id");
$st->execute([":id" => $id]);

echo json_encode(["ok"=>true]);

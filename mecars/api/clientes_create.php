<?php
require __DIR__ . "/db.php";
require __DIR__ . "/auth_guard.php";

$data = json_decode(file_get_contents("php://input"), true) ?: [];

$nombre  = trim($data["nombre"] ?? "");
$telefono = trim($data["telefono"] ?? "");

if ($nombre === "" || $telefono === "") {
  http_response_code(400);
  echo json_encode(["ok"=>false, "error"=>"Nombre y teléfono son obligatorios"]);
  exit;
}

$email = trim($data["email"] ?? "");
$fn = $data["fecha_nacimiento"] ?? null;
$fc = $data["fecha_compra"] ?? null;
$auto = trim($data["auto"] ?? "");
$notas = trim($data["notas"] ?? "");

$st = $pdo->prepare("
  INSERT INTO clientes (nombre, telefono, email, fecha_nacimiento, fecha_compra, auto, notas)
  VALUES (:nombre, :telefono, :email, :fn, :fc, :auto, :notas)
");
$st->execute([
  ":nombre" => $nombre,
  ":telefono" => $telefono,
  ":email" => $email !== "" ? $email : null,
  ":fn" => $fn ?: null,
  ":fc" => $fc ?: null,
  ":auto" => $auto !== "" ? $auto : null,
  ":notas" => $notas !== "" ? $notas : null,
]);

echo json_encode(["ok"=>true, "id" => (int)$pdo->lastInsertId()]);

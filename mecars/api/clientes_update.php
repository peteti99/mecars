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
  UPDATE clientes
  SET nombre=:nombre, telefono=:telefono, email=:email,
      fecha_nacimiento=:fn, fecha_compra=:fc, auto=:auto, notas=:notas
  WHERE id=:id
");
$st->execute([
  ":id" => $id,
  ":nombre" => $nombre,
  ":telefono" => $telefono,
  ":email" => $email !== "" ? $email : null,
  ":fn" => $fn ?: null,
  ":fc" => $fc ?: null,
  ":auto" => $auto !== "" ? $auto : null,
  ":notas" => $notas !== "" ? $notas : null,
]);

echo json_encode(["ok"=>true]);

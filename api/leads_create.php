<?php
require __DIR__ . "/db.php";
require __DIR__ . "/auth_guard.php";

$data = json_decode(file_get_contents("php://input"), true) ?: [];

$nombre  = trim($data["nombre"] ?? "");
$telefono = trim($data["telefono"] ?? "");
$mensaje = trim($data["mensaje"] ?? "");

if ($nombre === "" || $telefono === "" || $mensaje === "") {
  http_response_code(400);
  echo json_encode(["ok"=>false, "error"=>"Nombre, teléfono y mensaje son obligatorios"]);
  exit;
}

$email = trim($data["email"] ?? "");
$auto  = trim($data["auto"] ?? "");
$origen = trim($data["origen"] ?? "web");

$st = $pdo->prepare("
  INSERT INTO leads (nombre, telefono, email, auto, mensaje, origen)
  VALUES (:n, :t, :e, :a, :m, :o)
");
$st->execute([
  ":n" => $nombre,
  ":t" => $telefono,
  ":e" => $email !== "" ? $email : null,
  ":a" => $auto !== "" ? $auto : null,
  ":m" => $mensaje,
  ":o" => $origen !== "" ? $origen : null,
]);

echo json_encode(["ok"=>true, "id" => (int)$pdo->lastInsertId()]);

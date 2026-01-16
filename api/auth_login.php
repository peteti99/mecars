<?php
require __DIR__ . "/db.php";
session_start();

$data = json_decode(file_get_contents("php://input"), true) ?: [];
$user = trim($data["usuario"] ?? "");
$pass = $data["password"] ?? "";

if ($user === "" || $pass === "") {
  http_response_code(400);
  echo json_encode(["ok"=>false, "error"=>"Faltan datos"]);
  exit;
}

$st = $pdo->prepare("SELECT id, usuario, password_hash FROM usuarios WHERE usuario = :u LIMIT 1");
$st->execute([":u" => $user]);
$row = $st->fetch();

if (!$row || !password_verify($pass, $row["password_hash"])) {
  http_response_code(401);
  echo json_encode(["ok"=>false, "error"=>"Usuario o contraseña incorrectos"]);
  exit;
}

$_SESSION["auth"] = true;
$_SESSION["user_id"] = (int)$row["id"];
$_SESSION["usuario"] = $row["usuario"];

echo json_encode(["ok"=>true, "usuario"=>$row["usuario"]]);

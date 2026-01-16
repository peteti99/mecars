<?php
require __DIR__ . "/db.php";
session_start();

if (!($_SESSION["auth"] ?? false)) {
  http_response_code(401);
  echo json_encode(["ok"=>false, "error"=>"No autenticado"]);
  exit;
}

echo json_encode(["ok"=>true, "usuario"=>($_SESSION["usuario"] ?? "admin")]);

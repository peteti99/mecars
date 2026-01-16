<?php
session_start();

if (!($_SESSION["auth"] ?? false)) {
  http_response_code(401);
  header("Content-Type: application/json; charset=utf-8");
  echo json_encode(["ok"=>false, "error"=>"No autenticado"]);
  exit;
}

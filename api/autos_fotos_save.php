<?php
header('Content-Type: application/json; charset=utf-8');
require __DIR__ . "/db.php";
require __DIR__ . "/auth_guard.php";

$data = json_decode(file_get_contents("php://input"), true) ?: [];
$auto_id = (int)($data["auto_id"] ?? 0);
$urls = $data["urls"] ?? [];

if ($auto_id <= 0) {
  echo json_encode(["ok"=>false, "error"=>"auto_id inválido"]);
  exit;
}
if (!is_array($urls) || count($urls) === 0) {
  echo json_encode(["ok"=>false, "error"=>"No hay urls para guardar"]);
  exit;
}

try {
  $st = $pdo->prepare("INSERT INTO autos_fotos (auto_id, url, orden) VALUES (:auto_id, :url, :orden)");
  $orden = 0;
  foreach ($urls as $u) {
    $u = trim((string)$u);
    if ($u === "") continue;
    $st->execute([":auto_id"=>$auto_id, ":url"=>$u, ":orden"=>$orden]);
    $orden++;
  }
  echo json_encode(["ok"=>true]);
} catch (Throwable $e) {
  echo json_encode(["ok"=>false, "error"=>$e->getMessage()]);
}

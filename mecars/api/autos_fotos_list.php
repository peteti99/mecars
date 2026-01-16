<?php
header('Content-Type: application/json; charset=utf-8');
require __DIR__ . "/db.php";
require __DIR__ . "/auth_guard.php";

$id = (int)($_GET["id"] ?? 0); // id del auto
if ($id <= 0) {
  echo json_encode(["ok"=>false, "error"=>"ID inválido"]);
  exit;
}

try {
  $st = $pdo->prepare("SELECT id, url, orden FROM autos_fotos WHERE auto_id=:id ORDER BY orden ASC, id ASC");
  $st->execute([":id"=>$id]);
  $rows = $st->fetchAll(PDO::FETCH_ASSOC);

  echo json_encode(["ok"=>true, "data"=>$rows]);
} catch (Throwable $e) {
  echo json_encode(["ok"=>false, "error"=>$e->getMessage()]);
}

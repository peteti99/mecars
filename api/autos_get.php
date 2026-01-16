<?php
header('Content-Type: application/json; charset=utf-8');
require __DIR__ . "/db.php";

$id = (int)($_GET["id"] ?? 0);
if ($id <= 0) {
  echo json_encode(["ok"=>false, "error"=>"ID inválido"]);
  exit;
}

try {
  // 1) Auto
  $st = $pdo->prepare("SELECT * FROM autos WHERE id = :id LIMIT 1");
  $st->execute([":id"=>$id]);
  $a = $st->fetch(PDO::FETCH_ASSOC);

  if (!$a) {
    echo json_encode(["ok"=>false, "error"=>"No encontrado"]);
    exit;
  }

  // 2) Imágenes del auto (galería)  ✅ ahora lee autos_fotos
  $st2 = $pdo->prepare("SELECT url FROM autos_fotos WHERE auto_id=:id ORDER BY orden ASC, id ASC");
  $st2->execute([":id"=>$id]);
  $imgs = $st2->fetchAll(PDO::FETCH_COLUMN);

  // si no hay imágenes en tabla, caemos a la columna "imagen" vieja
  if (!$imgs || count($imgs) === 0) {
    $img = trim($a["imagen"] ?? "");
    if ($img !== "") $imgs = [$img];
    else $imgs = [];
  }

  $a["imagenes"] = $imgs;

  echo json_encode(["ok"=>true, "data"=>$a]);
} catch (Throwable $e) {
  echo json_encode(["ok"=>false, "error"=>$e->getMessage()]);
}

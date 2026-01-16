<?php
header('Content-Type: application/json; charset=utf-8');
require __DIR__ . "/db.php";
require __DIR__ . "/auth_guard.php";

$data = json_decode(file_get_contents("php://input"), true) ?: [];
$foto_id = (int)($data["foto_id"] ?? 0);

if ($foto_id <= 0) {
  echo json_encode(["ok"=>false, "error"=>"foto_id inválido"]);
  exit;
}

try {
  // 1) Traer URL para poder borrar el archivo (opcional)
  $st = $pdo->prepare("SELECT url FROM autos_fotos WHERE id=:id LIMIT 1");
  $st->execute([":id"=>$foto_id]);
  $url = $st->fetchColumn();

  // 2) Borrar de DB
  $del = $pdo->prepare("DELETE FROM autos_fotos WHERE id=:id");
  $del->execute([":id"=>$foto_id]);

  // 3) (Opcional) borrar archivo físico si está en uploads/
  if ($url) {
    $url = trim((string)$url);

    // Solo borramos si está dentro de /uploads para evitar borrar cosas raras
    if (str_starts_with($url, "uploads/")) {
      $baseDir = realpath(__DIR__ . "/.."); // /mecars
      $path = realpath($baseDir . "/" . $url);

      // Seguridad extra: confirmamos que el path real está dentro de /uploads
      $uploadsDir = realpath($baseDir . "/uploads");
      if ($path && $uploadsDir && str_starts_with($path, $uploadsDir) && file_exists($path)) {
        @unlink($path);
      }
    }
  }

  echo json_encode(["ok"=>true]);
} catch (Throwable $e) {
  echo json_encode(["ok"=>false, "error"=>$e->getMessage()]);
}

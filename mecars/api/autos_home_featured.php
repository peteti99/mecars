<?php
header("Content-Type: application/json; charset=utf-8");
require_once __DIR__ . "/db.php";

try {
  // Auto destacado: el último publicado (podés cambiar el ORDER luego)
  $sql = "SELECT id, marca, modelo, anio, km, transmision, combustible, precio, imagen
        FROM autos
        WHERE publicado = 1 AND destacado = 1
        ORDER BY id DESC
        LIMIT 1";


  $stmt = $pdo->query($sql);
  $row = $stmt->fetch(PDO::FETCH_ASSOC);

  echo json_encode(["ok" => true, "data" => $row ?: null]);
} catch (Exception $e) {
  echo json_encode(["ok" => false, "error" => $e->getMessage()]);
}

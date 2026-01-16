<?php
header('Content-Type: application/json; charset=utf-8');

require __DIR__ . "/auth_guard.php";
require __DIR__ . "/db.php";

try {
  $data = json_decode(file_get_contents("php://input"), true);
  if (!is_array($data)) $data = [];

  $id = (int)($data["id"] ?? 0);
  if ($id <= 0) {
    http_response_code(400);
    echo json_encode(["ok"=>false, "error"=>"ID inválido"]);
    exit;
  }

  // Leer y normalizar
  $marca       = trim($data["marca"] ?? "");
  $modelo      = trim($data["modelo"] ?? "");
  $anio        = ($data["anio"] ?? "") !== "" ? (int)$data["anio"] : null;
  $color       = trim($data["color"] ?? "");
  $estado      = trim($data["estado"] ?? "Disponible");
  $precio      = ($data["precio"] ?? "") !== "" ? (float)$data["precio"] : null;
  $km          = ($data["km"] ?? "") !== "" ? (int)$data["km"] : null;
  $combustible = trim($data["combustible"] ?? "");
  $transmision = trim($data["transmision"] ?? "");
  $imagen      = trim($data["imagen"] ?? "");
  $publicado   = !empty($data["publicado"]) ? 1 : 0;
  $destacado   = !empty($data["destacado"]) ? 1 : 0;

  if ($marca === "" || $modelo === "") {
    http_response_code(400);
    echo json_encode(["ok"=>false, "error"=>"Marca y modelo son obligatorios"]);
    exit;
  }

  $st = $pdo->prepare("
    UPDATE autos SET
      marca=:marca,
      modelo=:modelo,
      anio=:anio,
      km=:km,
      color=:color,
      precio=:precio,
      estado=:estado,
      combustible=:combustible,
      transmision=:transmision,
      imagen=:imagen,
      publicado=:publicado,
      destacado=:destacado
    WHERE id=:id
  ");

  // ⭐ Destacado único: si este auto se marca como destacado, desmarcamos el resto
if ($destacado === 1) {
  $pdo->exec("UPDATE autos SET destacado = 0");
}


  // ✅ Ejecutar el statement correcto ($st) y mandar variables ya normalizadas
  $st->execute([
    ":id" => $id,
    ":marca" => $marca,
    ":modelo" => $modelo,
    ":anio" => $anio,
    ":km" => $km,
    ":color" => ($color !== "" ? $color : null),
    ":precio" => $precio,
    ":estado" => $estado,
    ":combustible" => ($combustible !== "" ? $combustible : null),
    ":transmision" => ($transmision !== "" ? $transmision : null),
    ":imagen" => ($imagen !== "" ? $imagen : null),
    ":publicado" => $publicado,
    ":destacado" => $destacado
  ]);

  echo json_encode(["ok"=>true]);
  exit;

} catch (Throwable $e) {
  echo json_encode(["ok"=>false, "error"=>$e->getMessage()]);
  exit;
}

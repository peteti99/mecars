<?php
header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/auth_guard.php';
require_once __DIR__ . '/db.php';

try {
  $in = json_decode(file_get_contents("php://input"), true) ?: [];

  $marca       = trim($in["marca"] ?? "");
  $modelo      = trim($in["modelo"] ?? "");
  $anio        = ($in["anio"] ?? null);
  $km          = ($in["km"] ?? null);
  $color       = trim($in["color"] ?? "");
  $precio      = ($in["precio"] ?? null);
  $estado      = trim($in["estado"] ?? "");
  $combustible = trim($in["combustible"] ?? "");
  $transmision = trim($in["transmision"] ?? "");
  $imagen      = trim($in["imagen"] ?? "");
  $publicado   = isset($in["publicado"]) ? (int)$in["publicado"] : 1;
  $destacado   = isset($in["destacado"]) ? (int)$in["destacado"] : 0;

  if ($marca === "" || $modelo === "") {
    echo json_encode(["ok"=>false, "error"=>"Marca y Modelo son obligatorios"]);
    exit;
  }

  $anio   = ($anio === "" ? null : $anio);
  $km     = ($km === "" ? null : $km);
  $precio = ($precio === "" ? null : $precio);

  $pdo->beginTransaction();

  $sql = "INSERT INTO autos
    (marca, modelo, anio, km, color, precio, estado, combustible, transmision, imagen, publicado, destacado)
    VALUES
    (:marca, :modelo, :anio, :km, :color, :precio, :estado, :combustible, :transmision, :imagen, :publicado, :destacado)";

  $st = $pdo->prepare($sql);
  $st->execute([
    ":marca" => $marca,
    ":modelo" => $modelo,
    ":anio" => $anio,
    ":km" => $km,
    ":color" => $color,
    ":precio" => $precio,
    ":estado" => $estado,
    ":combustible" => $combustible,
    ":transmision" => $transmision,
    ":imagen" => $imagen,
    ":publicado" => $publicado,
    ":destacado" => $destacado,
  ]);

  $autoId = (int)$pdo->lastInsertId();

  // insertar galería si vino "imagenes"
  $imgs = $in["imagenes"] ?? [];
  if (is_string($imgs)) {
    $imgs = array_values(array_filter(array_map("trim", explode(",", $imgs))));
  }
  if (is_array($imgs) && count($imgs) > 0) {
    $st2 = $pdo->prepare("INSERT INTO autos_imagenes (auto_id, url, orden) VALUES (:auto_id, :url, :orden)");
    $ord = 1;
    foreach ($imgs as $u) {
      $u = trim((string)$u);
      if ($u === "") continue;
      $st2->execute([":auto_id"=>$autoId, ":url"=>$u, ":orden"=>$ord]);
      $ord++;
    }
  }

  $pdo->commit();

  echo json_encode(["ok"=>true, "id"=>$autoId]);
} catch (Throwable $e) {
  if ($pdo && $pdo->inTransaction()) $pdo->rollBack();
  echo json_encode(["ok"=>false, "error"=>$e->getMessage()]);
}

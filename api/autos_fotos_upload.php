<?php
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/auth_guard.php';

$baseDir = realpath(__DIR__ . "/..");
$uploadDir = $baseDir . "/uploads";

if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

if (!isset($_FILES["images"])) {
  // cuando llega como images[] PHP lo arma como "images"
  echo json_encode(["ok"=>false, "error"=>"No llegaron archivos (images)"]);
  exit;
}

$files = $_FILES["images"];
$urls = [];

$count = is_array($files["name"]) ? count($files["name"]) : 0;

for ($i=0; $i < $count; $i++) {
  if ($files["error"][$i] !== UPLOAD_ERR_OK) continue;

  $tmp = $files["tmp_name"][$i];
  $name = $files["name"][$i];

  $ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));
  if (!in_array($ext, ["jpg","jpeg","png","webp"])) continue;

  $safe = "auto_" . date("Ymd_His") . "_" . bin2hex(random_bytes(5)) . "." . $ext;
  $dest = $uploadDir . "/" . $safe;

  if (move_uploaded_file($tmp, $dest)) {
    $urls[] = "uploads/" . $safe;
  }
}

echo json_encode(["ok"=>true, "urls"=>$urls]);

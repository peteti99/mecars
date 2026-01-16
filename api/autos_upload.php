<?php
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/auth_guard.php';

$baseDir = realpath(__DIR__ . "/..");
$uploadDir = $baseDir . "/uploads";
if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

if (!isset($_FILES["images"])) {
  echo json_encode(["ok"=>false, "error"=>"No llegaron archivos (images)"]);
  exit;
}

$files = $_FILES["images"];
$urls = [];

$isMulti = is_array($files["name"]);
$count = $isMulti ? count($files["name"]) : 1;

for ($i=0; $i < $count; $i++) {
  $err  = $isMulti ? $files["error"][$i]    : $files["error"];
  $tmp  = $isMulti ? $files["tmp_name"][$i] : $files["tmp_name"];
  $name = $isMulti ? $files["name"][$i]     : $files["name"];

  if ($err !== UPLOAD_ERR_OK) continue;

  $ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));
  if (!in_array($ext, ["jpg","jpeg","png","webp"])) continue;

  $safe = "auto_" . date("Ymd_His") . "_" . bin2hex(random_bytes(5)) . "." . $ext;
  $dest = $uploadDir . "/" . $safe;

  if (move_uploaded_file($tmp, $dest)) {
    $urls[] = "uploads/" . $safe;
  }
}

echo json_encode(["ok"=>true, "urls"=>$urls]);

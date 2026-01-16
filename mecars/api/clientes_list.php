<?php
require __DIR__ . "/db.php";
require __DIR__ . "/auth_guard.php";

$q = trim($_GET["q"] ?? "");

if ($q !== "") {
  $like = "%".$q."%";
  $st = $pdo->prepare("
    SELECT * FROM clientes
    WHERE nombre LIKE :q OR telefono LIKE :q OR email LIKE :q OR auto LIKE :q
    ORDER BY id DESC
    LIMIT 500
  ");
  $st->execute([":q" => $like]);
} else {
  $st = $pdo->query("SELECT * FROM clientes ORDER BY id DESC LIMIT 500");
}

echo json_encode(["ok"=>true, "data"=>$st->fetchAll()]);

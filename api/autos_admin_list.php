<?php
require __DIR__ . "/db.php";
require __DIR__ . "/auth_guard.php";

$st = $pdo->query("SELECT * FROM autos ORDER BY id DESC LIMIT 500");
echo json_encode(["ok"=>true, "data"=>$st->fetchAll()]);

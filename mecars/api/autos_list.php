<?php
require __DIR__ . "/db.php";

$q = trim($_GET["q"] ?? "");
$brand = trim($_GET["brand"] ?? "");
$yearMin = (int)($_GET["yearMin"] ?? 0);
$priceMax = (int)($_GET["priceMax"] ?? 0);

$where = "WHERE publicado = 1";
$params = [];

if ($q !== "") {
  $where .= " AND (titulo LIKE :q OR marca LIKE :q OR modelo LIKE :q OR descripcion LIKE :q)";
  $params[":q"] = "%".$q."%";
}
if ($brand !== "") {
  $where .= " AND marca = :b";
  $params[":b"] = $brand;
}
if ($yearMin > 0) {
  $where .= " AND anio >= :y";
  $params[":y"] = $yearMin;
}
if ($priceMax > 0) {
  $where .= " AND precio <= :p";
  $params[":p"] = $priceMax;
}

$sql = "SELECT * FROM autos $where ORDER BY id DESC LIMIT 500";
$st = $pdo->prepare($sql);
$st->execute($params);

echo json_encode(["ok"=>true, "data"=>$st->fetchAll()]);

// autos_list.php (o autos_latest.php)
$stmt = $pdo->query("SELECT id, marca, modelo, anio, km, transmision, combustible, precio, imagen, estado, color
                     FROM autos
                     WHERE publicado = 1
                     ORDER BY id DESC
                     LIMIT 3");
$data = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode(["ok"=>true, "data"=>$data]);

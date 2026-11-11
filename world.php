<?php
$host = 'localhost';
$username = 'lab5_user';
$password = 'password123';
$dbname = 'world';

$conn = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);

$country = isset($_GET['country']) ? trim($_GET['country']) : '';

$stmt = $conn->prepare("SELECT * FROM countries WHERE name LIKE :country");
$stmt->execute([':country' => '%' . $country . '%']);

$results = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>
<ul>
<?php foreach ($results as $row): ?>
  <li><?=  htmlspecialchars($row['name'] . ' is ruled by ' . $row['head_of_state']); ?></li>
<?php endforeach; ?>
</ul>

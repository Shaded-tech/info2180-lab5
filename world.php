<?php
$host = 'localhost';
$username = 'lab5_user';
$password = 'password123';
$dbname = 'world';

try {
$conn = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);

} catch (PDOException $e) {
   http_response_code(500);
    echo '<div class="message error"><p class="message-title">Database unavailable</p>'
        . '<p>Could not connect to the world database.</p></div>';
    exit; 
}

function e($value) {
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function result_head($title) {
    echo '<h2>' . $title . '</h2>';
}

function message($title, $text) {
    echo '<div class="message"><p class="message-title">' . e($title) . '</p><p>' . e($text) . '</p></div>';
}

$country = isset($_GET['country']) ? trim($_GET['country']) : '';
$lookup  = isset($_GET['lookup']) ? $_GET['lookup'] : 'country';

// Escape % and _ so they are searched literally instead of acting as wildcards.
$term = '%' . addcslashes($country, '%_\\') . '%';

if ($lookup === 'cities') {
    if ($country === '') {
        message('Enter a country first', 'Type a country name, then choose Lookup Cities to see its cities.');
        exit;
    }

    $stmt = $conn->prepare(
        'SELECT c.name AS city, c.district, c.population, cs.name AS country
         FROM cities c
         JOIN countries cs ON c.country_code = cs.code
         WHERE cs.name LIKE :country
         ORDER BY c.population DESC'
    );
    $stmt->execute([':country' => $term]);
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (count($rows) === 0) {
        message('No cities found', 'No country matches "' . $country . '".');
        exit;
    }

    $matched = array_unique(array_column($rows, 'country'));
    $label = count($matched) === 1 ? reset($matched) : $country;

    result_head('Cities in ' . e($label));
    ?>
    <div class="table-card">
        <table>
            <thead>
                <tr>
                    <th>Name</th>
                    <th>District</th>
                    <th class="num">Population</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($rows as $row): ?>
                <tr>
                    <td><?= e($row['city']) ?></td>
                    <td><?= e($row['district']) ?></td>
                    <td class="num strong"><?= number_format((int) $row['population']) ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php
} else {

$stmt = $conn->prepare(
    'SELECT name, continent, independence_year, head_of_state
     FROM countries
     WHERE name LIKE :country
     ORDER BY name'
);
$stmt->execute([':country' => $term]);
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (count($rows) === 0) {
    message('No countries found', 'No country matches "' . $country . '".');
    exit;
}

$title = $country === '' ? 'All countries' : 'Countries matching “' . e($country) . '”';
result_head($title);
?>
<div class="table-card">
    <table>
        <thead>
            <tr>
                <th>Name</th>
                <th>Continent</th>
                <th>Independence</th>
                <th>Head of State</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($rows as $row): ?>
            <tr>
                <td><?= e($row['name']) ?></td>
                <td><?= e($row['continent']) ?></td>
                <td class="strong"><?= $row['independence_year'] === null ? '—' : e($row['independence_year']) ?></td>
                <td><?= $row['head_of_state'] === null || $row['head_of_state'] === '' ? '—' : e($row['head_of_state']) ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php
}

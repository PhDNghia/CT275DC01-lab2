<?php
require_once __DIR__ . '/../partials/header.php';

if (!isset($pdo)) {
    $host = 'localhost';
    $db   = 'ct275_lab2';
    $user = 'postgres';
    $pass = '1029384756';
    $dsn  = "pgsql:host=$host;dbname=$db";

    try {
        $pdo = new PDO($dsn, $user, $pass, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
    } catch (PDOException $e) {
        die("Kết nối CSDL thất bại: " . $e->getMessage());
    }
}

$sources = [];
$sourceStmt = $pdo->query("SELECT DISTINCT source FROM quotes ORDER BY source ASC");
if ($sourceStmt) {
    $sources = $sourceStmt->fetchAll(PDO::FETCH_COLUMN);
}

$keyword = isset($_GET['keyword']) ? trim($_GET['keyword']) : '';
$source  = isset($_GET['source']) ? trim($_GET['source']) : '';

$quotes = [];
$sql = "SELECT * FROM quotes WHERE 1=1";
$params = [];

if ($keyword !== '') {
    $sql .= " AND quote ILIKE :keyword";
    $params[':keyword'] = '%' . $keyword . '%';
}

if ($source !== '') {
    $sql .= " AND source = :source";
    $params[':source'] = $source;
}

$sql .= " ORDER BY id DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$quotes = $stmt->fetchAll();
?>

<main class="container">
    <h2>Tìm kiếm trích dẫn</h2>

    <form action="search.php" method="GET" class="search-form" style="margin-bottom: 20px;">
        <div style="margin-bottom: 10px;">
            <label for="keyword">Từ khóa:</label>
            <input type="text" name="keyword" id="keyword" value="<?= htmlspecialchars($keyword) ?>" placeholder="Nhập từ khóa trích dẫn...">
        </div>

        <div style="margin-bottom: 10px;">
            <label for="source">Tác giả / Nguồn:</label>
            <select name="source" id="source">
                <option value="">-- Tất cả nguồn --</option>
                <?php foreach ($sources as $s): ?>
                    <option value="<?= htmlspecialchars($s) ?>" <?= $source === $s ? 'selected' : '' ?>>
                        <?= htmlspecialchars($s) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <button type="submit">Tìm kiếm</button>
    </form>

    <hr>

    <h3>Kết quả tìm kiếm</h3>
    <?php if (count($quotes) > 0): ?>
        <?php foreach ($quotes as $q): ?>
            <blockquote style="margin-bottom: 15px;">
                <p>"<?= htmlspecialchars($q['quote']) ?>"</p>
                <small>- <?= htmlspecialchars($q['source']) ?></small>
            </blockquote>
        <?php endforeach; ?>
    <?php else: ?>
        <p>Không tìm thấy trích dẫn nào phù hợp.</p>
    <?php endif; ?>
</main>

<?php
require_once __DIR__ . '/../partials/footer.php';
?>
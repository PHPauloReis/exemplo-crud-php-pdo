<?php
$host = getenv('DB_HOST');
$db   = getenv('DB_NAME');
$user = getenv('DB_USER');
$pass = getenv('DB_PASS');
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
    // PDO::ATTR_PERSISTENT         => true,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    throw new \PDOException($e->getMessage(), (int)$e->getCode());
}

const PRODUTOS_CACHE_KEY = 'produtos';
const PRODUTOS_CACHE_TTL = 60;

function getRedis() {
    static $redis = null;
    if ($redis === null) {
        $redis = new Redis();
        $redis->connect(getenv('REDIS_HOST') ?: 'redis', 6379);
    }
    return $redis;
}

function getProdutos($pdo) {
    $redis = getRedis();
    $cached = $redis->get(PRODUTOS_CACHE_KEY);
    if ($cached !== false) {
        return json_decode($cached, true);
    }

    $stmt = $pdo->query('SELECT * FROM produtos');
    $produtos = $stmt->fetchAll();
    $redis->setex(PRODUTOS_CACHE_KEY, PRODUTOS_CACHE_TTL, json_encode($produtos));
    return $produtos;
}

function getProduto($pdo, $id) {
    $stmt = $pdo->prepare('SELECT * FROM produtos WHERE id = ?');
    $stmt->execute([$id]);
    return $stmt->fetch();
}

function createProduto($pdo, $nome, $preco, $quantidade) {
    try {
        $pdo->beginTransaction();
        $stmt = $pdo->prepare('INSERT INTO produtos (nome, preco, quantidade) VALUES (?, ?, ?)');
        $stmt->execute([$nome, $preco, $quantidade]);
        $pdo->commit();
        getRedis()->del(PRODUTOS_CACHE_KEY);
        return true;
    } catch (Exception $e) {
        $pdo->rollBack();
        return false;
    }
}

function updateProduto($pdo, $id, $nome, $preco, $quantidade) {
    try {
        $pdo->beginTransaction();
        $stmt = $pdo->prepare('UPDATE produtos SET nome = ?, preco = ?, quantidade = ? WHERE id = ?');
        $stmt->execute([$nome, $preco, $quantidade, $id]);
        $pdo->commit();
        getRedis()->del(PRODUTOS_CACHE_KEY);
        return true;
    } catch (Exception $e) {
        $pdo->rollBack();
        return false;
    }
}

function deleteProduto($pdo, $id) {
    try {
        $pdo->beginTransaction();
        $stmt = $pdo->prepare('DELETE FROM produtos WHERE id = ?');
        $stmt->execute([$id]);
        $pdo->commit();
        getRedis()->del(PRODUTOS_CACHE_KEY);
        return true;
    } catch (Exception $e) {
        $pdo->rollBack();
        return false;
    }
}
?>

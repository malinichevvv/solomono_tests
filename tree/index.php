<?php

header('Content-Type: application/json');

require_once '../components/Database.php';

$dbConfig = require_once 'dbConfig.php';

/** @var PDO $conn */
$conn = null;
try {
    $db = new Database($dbConfig);
    $conn = $db->getConnection();
} catch (Exception $e) {
    die($e->getMessage());
}

$stmt = $conn->prepare("SELECT * FROM categories");
$stmt->execute();

$tree = [];
$references = [];

while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $id = (int)$row['categories_id'];
    $parent = (int)$row['parent_id'];

    if (!isset($references[$id])) {
        $references[$id] = [];
    }

    if ($parent === 0) {
        $tree[$id] =& $references[$id];
    } else {
        if (!isset($references[$parent])) {
            $references[$parent] = [];
        }
        $references[$parent][$id] =& $references[$id];
    }
}

$stack = [[&$tree, null]];
while ($stack) {
    [$node, $parentId] = array_pop($stack);
    foreach ($node as $id => &$child) {
        if ($child === []) {
            $child = $id;
        } else {
            $stack[] = [&$child, $id];
        }
    }
}

echo json_encode($tree);
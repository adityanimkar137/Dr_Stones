<?php
require_once __DIR__ . '/config.php';

header_remove();

try {
    $conn = getDBConnection();

    $res1 = $conn->query("SELECT id, stone_name, status, approved_at, rejection_reason, created_at FROM proposals ORDER BY id DESC LIMIT 10");
    $proposals = [];
    if ($res1) {
        while ($r = $res1->fetch_assoc()) $proposals[] = $r;
        $res1->free();
    }

    $res2 = $conn->query("SELECT id, name, price, image FROM items ORDER BY id DESC LIMIT 10");
    $items = [];
    if ($res2) {
        while ($r = $res2->fetch_assoc()) $items[] = $r;
        $res2->free();
    }

    $conn->close();

    echo json_encode(['success'=>true,'proposals'=>$proposals,'items'=>$items], JSON_PRETTY_PRINT);
} catch (Exception $e) {
    echo json_encode(['success'=>false,'error'=>$e->getMessage()]);
}

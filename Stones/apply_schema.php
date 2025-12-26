<?php
require_once __DIR__ . '/config.php';

try {
    $c = getDBConnection();

    $cols = [];
    $res = $c->query("SHOW COLUMNS FROM proposals");
    if ($res) {
        while ($r = $res->fetch_assoc()) $cols[] = $r['Field'];
        $res->free();
    }

    $added = [];
    if (!in_array('approved_at', $cols)) {
        $c->query("ALTER TABLE proposals ADD COLUMN approved_at TIMESTAMP NULL DEFAULT NULL");
        $added[] = 'approved_at';
    }
    if (!in_array('rejection_reason', $cols)) {
        $c->query("ALTER TABLE proposals ADD COLUMN rejection_reason TEXT NULL");
        $added[] = 'rejection_reason';
    }

    $c->close();

    echo json_encode(['success'=>true,'added'=>$added]);
} catch (Exception $e) {
    echo json_encode(['success'=>false,'error'=>$e->getMessage()]);
}

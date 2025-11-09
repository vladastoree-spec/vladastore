<?php
require_once '../config.php';
require_once '../functions.php';

header('Content-Type: application/json');

if (!isset($_GET['game_id'])) {
    echo json_encode(['success' => false, 'message' => 'Game ID required']);
    exit;
}

$game_id = intval($_GET['game_id']);
$game = getGame($game_id);
$products = getGameProducts($game_id);
$formFields = getGameFormFields($game_id);

if ($game) {
    echo json_encode([
        'success' => true,
        'game' => $game,
        'products' => $products,
        'formFields' => $formFields
    ]);
} else {
    echo json_encode(['success' => false, 'message' => 'Game not found']);
}
?>
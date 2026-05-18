<?php
// api/leaderboard.php — Returns top 10 students as JSON
if (session_status() === PHP_SESSION_NONE) { session_start(); }
header('Content-Type: application/json');

require_once __DIR__ . '/../models/ResultModel.php';

$model   = new ResultModel();
$leaders = $model->getLeaderboard();

// Add rank field for JS
$result = [];
foreach ($leaders as $i => $row) {
    $result[] = [
        'rank'         => $i + 1,
        'name'         => $row['name'],
        'total_score'  => (int)$row['total_score'],
        'quizzes_taken'=> (int)$row['quizzes_taken'],
    ];
}
echo json_encode($result);

<?php
// models/QuizModel.php
// Handles all DB operations for the quizzes table.
require_once __DIR__ . '/../config/database.php';

class QuizModel {

    private function getConn() {
        return getDB();
    }

    /**
     * COMMIT 6: Create a new quiz (always starts as 'draft').
     */
    public function createQuiz($instructor_id, $title, $description, $time_limit) {
        $db = $this->getConn();
        $stmt = $db->prepare("
            INSERT INTO quizzes (instructor_id, title, description, total_marks, time_limit_minutes, status, created_at)
            VALUES (?, ?, ?, 0, ?, 'draft', NOW())
        ");
        $stmt->execute([$instructor_id, $title, $description, $time_limit]);
        return $db->lastInsertId();
    }
}
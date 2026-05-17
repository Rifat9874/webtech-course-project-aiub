<?php
// models/QuestionModel.php
require_once __DIR__ . '/../config/database.php';

class QuestionModel {

    private function getConn() {
        return getDB();
    }

    /**
     * COMMIT 11: Insert a new question row.
     */
    public function addQuestion($quiz_id, $question_text, $marks, $order_index) {
        $db = $this->getConn();
        $stmt = $db->prepare("
            INSERT INTO questions (quiz_id, question_text, marks, order_index)
            VALUES (?, ?, ?, ?)
        ");
        $stmt->execute([$quiz_id, $question_text, $marks, $order_index]);
        return $db->lastInsertId();
    }

    /**
     * COMMIT 11: Insert one option for a question.
     */
    public function addOption($question_id, $option_text, $is_correct) {
        $db = $this->getConn();
        $stmt = $db->prepare("
            INSERT INTO options (question_id, option_text, is_correct) VALUES (?, ?, ?)
        ");
        $stmt->execute([$question_id, $option_text, $is_correct]);
    }
}
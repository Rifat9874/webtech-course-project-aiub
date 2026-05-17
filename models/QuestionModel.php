<?php
// models/QuestionModel.php
require_once __DIR__ . '/../config/database.php';

class QuestionModel {

    private function getConn() {
        return getDB();
    }

    /**
     * : Insert a new question row.
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
     * : Insert one option for a question.
     */
    public function addOption($question_id, $option_text, $is_correct) {
        $db = $this->getConn();
        $stmt = $db->prepare("
            INSERT INTO options (question_id, option_text, is_correct) VALUES (?, ?, ?)
        ");
        $stmt->execute([$question_id, $option_text, $is_correct]);
    }
/**
     * : Fetch all questions for a quiz with their options nested.
     */
    public function getQuestionsByQuiz($quiz_id) {
        $db = $this->getConn();
        $stmt = $db->prepare("SELECT * FROM questions WHERE quiz_id = ? ORDER BY order_index ASC");
        $stmt->execute([$quiz_id]);
        $questions = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($questions as &$question) {
            $optStmt = $db->prepare("SELECT * FROM options WHERE question_id = ? ORDER BY id ASC");
            $optStmt->execute([$question['id']]);
            $question['options'] = $optStmt->fetchAll(PDO::FETCH_ASSOC);
        }
        unset($question);
        return $questions;
    }

}
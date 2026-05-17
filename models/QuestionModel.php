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

/**
     *  Update question text.
     */
    public function updateQuestion($id, $question_text) {
        $db = $this->getConn();
        $stmt = $db->prepare("UPDATE questions SET question_text = ? WHERE id = ?");
        $stmt->execute([$question_text, $id]);
    }

    /**
     * Update all 4 options — reset is_correct then set the correct one.
     */
    public function updateOptions($question_id, $options_array, $correct_option_id) {
        $db = $this->getConn();
        $stmt = $db->prepare("UPDATE options SET is_correct = 0 WHERE question_id = ?");
        $stmt->execute([$question_id]);
        foreach ($options_array as $option) {
            $stmt = $db->prepare("UPDATE options SET option_text = ? WHERE id = ?");
            $stmt->execute([$option['text'], $option['id']]);
        }
        $stmt = $db->prepare("UPDATE options SET is_correct = 1 WHERE id = ?");
        $stmt->execute([$correct_option_id]);
    }

    /**
     * Delete a question (options first due to FK).
     */
    public function deleteQuestion($id) {
        $db = $this->getConn();
        $stmt = $db->prepare("DELETE FROM options WHERE question_id = ?");
        $stmt->execute([$id]);
        $stmt = $db->prepare("DELETE FROM questions WHERE id = ?");
        $stmt->execute([$id]);
    }

/**
     *  Count questions in a quiz (used for order_index).
     */
    public function countQuestions($quiz_id) {
        $db = $this->getConn();
        $stmt = $db->prepare("SELECT COUNT(*) FROM questions WHERE quiz_id = ?");
        $stmt->execute([$quiz_id]);
        return (int) $stmt->fetchColumn();
    }

    /**
     *  Get one question by ID (used in API for ownership check).
     */
    public function getQuestionById($id) {
        $db = $this->getConn();
        $stmt = $db->prepare("SELECT * FROM questions WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

}
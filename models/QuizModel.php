<?php
// models/QuizModel.php
require_once __DIR__ . '/../config/database.php';

class QuizModel {

    private function getConn() {
        return getDB();
    }

    public function createQuiz($instructor_id, $title, $description, $time_limit) {
        $db = $this->getConn();
        $stmt = $db->prepare("
            INSERT INTO quizzes (instructor_id, title, description, total_marks, time_limit_minutes, status, created_at)
            VALUES (?, ?, ?, 0, ?, 'draft', NOW())
        ");
        $stmt->execute([$instructor_id, $title, $description, $time_limit]);
        return $db->lastInsertId();
    }

    /**
     * COMMIT 7: Fetch a single quiz by ID.
     */
    public function getQuizById($id) {
        $db = $this->getConn();
        $stmt = $db->prepare("SELECT * FROM quizzes WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * COMMIT 7: Get all quizzes belonging to one instructor.
     */
    public function getQuizzesByInstructor($instructor_id) {
        $db = $this->getConn();
        $stmt = $db->prepare("SELECT * FROM quizzes WHERE instructor_id = ? ORDER BY created_at DESC");
        $stmt->execute([$instructor_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

/**
     * COMMIT 8: Update quiz title, description, time_limit.
     */
    public function updateQuiz($id, $title, $description, $time_limit) {
        $db = $this->getConn();
        $stmt = $db->prepare("
            UPDATE quizzes SET title = ?, description = ?, time_limit_minutes = ? WHERE id = ?
        ");
        $stmt->execute([$title, $description, $time_limit, $id]);
    }

/**
     * COMMIT 9: Delete quiz with manual cascade — options → questions → quiz.
     */
    public function deleteQuiz($id) {
        $db = $this->getConn();
        // Step 1: Delete options
        $stmt = $db->prepare("DELETE o FROM options o INNER JOIN questions q ON o.question_id = q.id WHERE q.quiz_id = ?");
        $stmt->execute([$id]);
        // Step 2: Delete questions
        $stmt = $db->prepare("DELETE FROM questions WHERE quiz_id = ?");
        $stmt->execute([$id]);
        // Step 3: Delete quiz
        $stmt = $db->prepare("DELETE FROM quizzes WHERE id = ?");
        $stmt->execute([$id]);
    }

}
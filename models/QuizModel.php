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


    /**
     * COMMIT 10: Flip quiz status between draft and published.
     */
    public function toggleStatus($id) {
        $db   = $this->getConn();
        $quiz = $this->getQuizById($id);
        if (!$quiz) return false;
        $newStatus = ($quiz['status'] === 'draft') ? 'published' : 'draft';
        $stmt = $db->prepare("UPDATE quizzes SET status = ? WHERE id = ?");
        $stmt->execute([$newStatus, $id]);
        return $newStatus;
    }

    /**
     * COMMIT 10: Recalculate total_marks by summing all question marks.
     */
    public function updateTotalMarks($quiz_id) {
        $db   = $this->getConn();
        $stmt = $db->prepare("SELECT COALESCE(SUM(marks), 0) AS total FROM questions WHERE quiz_id = ?");
        $stmt->execute([$quiz_id]);
        $row  = $stmt->fetch(PDO::FETCH_ASSOC);
        $stmt = $db->prepare("UPDATE quizzes SET total_marks = ? WHERE id = ?");
        $stmt->execute([$row['total'], $quiz_id]);
    }

    /**
     * COMMIT 10: Count published quizzes (for student dashboard).
     */
    public function countPublishedQuizzes() {
        $db   = $this->getConn();
        $stmt = $db->prepare("SELECT COUNT(*) as total FROM quizzes WHERE status = 'published'");
        $stmt->execute();
        $row  = $stmt->fetch(PDO::FETCH_ASSOC);
        return (int)($row['total'] ?? 0);
    }

}
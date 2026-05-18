<?php
// models/ResultModel.php
// All database queries for results, analytics, and leaderboard.

require_once __DIR__ . '/../config/database.php';

class ResultModel {

    private $db;

    public function __construct() {
        $this->db = getDB();
    }

    // Single attempt: score + quiz title + student name
    public function getAttemptResult($attempt_id) {
        $stmt = $this->db->prepare("
            SELECT a.id AS attempt_id, a.score, a.started_at, a.completed_at,
                   q.title AS quiz_title, q.total_marks, u.name AS student_name
            FROM attempts a
            JOIN quizzes q ON a.quiz_id    = q.id
            JOIN users   u ON a.student_id = u.id
            WHERE a.id = ?
            LIMIT 1
        ");
        $stmt->execute([$attempt_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Per-question breakdown: what student chose vs correct answer
    public function getAnswerBreakdown($attempt_id) {
        $stmt = $this->db->prepare("
            SELECT q.question_text,
                   sel.option_text AS selected_option_text,
                   sel.is_correct  AS is_selected_correct,
                   cor.option_text AS correct_option_text
            FROM answers ans
            JOIN questions q   ON ans.question_id        = q.id
            JOIN options   sel ON ans.selected_option_id = sel.id
            JOIN options   cor ON cor.question_id = q.id AND cor.is_correct = 1
            WHERE ans.attempt_id = ?
            ORDER BY q.order_index ASC
        ");
        $stmt->execute([$attempt_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Student's full attempt history (completed only)
    public function getMyAttempts($student_id) {
        $stmt = $this->db->prepare("
            SELECT a.id AS attempt_id, q.title AS quiz_title,
                   a.score, q.total_marks, a.started_at, a.completed_at,
                   CASE WHEN q.total_marks > 0 AND (a.score / q.total_marks * 100) >= 60
                        THEN 'Pass' ELSE 'Fail' END AS pass_fail
            FROM attempts a
            JOIN quizzes q ON a.quiz_id = q.id
            WHERE a.student_id   = ?
              AND a.completed_at IS NOT NULL
            ORDER BY a.started_at DESC
        ");
        $stmt->execute([$student_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // All attempts on a quiz — for instructor analytics
    public function getQuizAttempts($quiz_id) {
        $stmt = $this->db->prepare("
            SELECT u.name AS student_name, a.score, q.total_marks,
                   a.started_at, a.completed_at,
                   TIMESTAMPDIFF(SECOND, a.started_at, a.completed_at) AS duration_seconds,
                   CASE WHEN q.total_marks > 0 AND (a.score / q.total_marks * 100) >= 60
                        THEN 'Pass' ELSE 'Fail' END AS pass_fail
            FROM attempts a
            JOIN users   u ON a.student_id = u.id
            JOIN quizzes q ON a.quiz_id    = q.id
            WHERE a.quiz_id      = ?
              AND a.completed_at IS NOT NULL
            ORDER BY a.started_at DESC
        ");
        $stmt->execute([$quiz_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Class statistics for one quiz
    public function getQuizStats($quiz_id) {
        $stmt = $this->db->prepare("
            SELECT ROUND(AVG(a.score), 1) AS avg_score,
                   MAX(a.score)           AS max_score,
                   MIN(a.score)           AS min_score,
                   q.total_marks,
                   ROUND(
                       SUM(CASE WHEN q.total_marks > 0
                                AND (a.score / q.total_marks * 100) >= 60
                                THEN 1 ELSE 0 END
                       ) / COUNT(*) * 100
                   , 1) AS pass_rate
            FROM attempts a
            JOIN quizzes q ON a.quiz_id = q.id
            WHERE a.quiz_id      = ?
              AND a.completed_at IS NOT NULL
        ");
        $stmt->execute([$quiz_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Top 10 students by total score — COMPLETED attempts only
    public function getLeaderboard() {
        $stmt = $this->db->prepare("
            SELECT u.name                    AS name,
                   COALESCE(SUM(a.score), 0) AS total_score,
                   COUNT(a.id)               AS quizzes_taken
            FROM attempts a
            JOIN users u ON a.student_id = u.id
            WHERE a.completed_at IS NOT NULL
            GROUP BY a.student_id, u.name
            ORDER BY total_score DESC
            LIMIT 10
        ");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Instructor's quizzes for analytics dropdown
    public function getInstructorQuizzes($instructor_id) {
        $stmt = $this->db->prepare("
            SELECT id, title FROM quizzes
            WHERE instructor_id = ?
            ORDER BY title ASC
        ");
        $stmt->execute([$instructor_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}

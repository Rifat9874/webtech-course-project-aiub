<?php
// models/AttemptModel.php
// Handles all database operations related to quiz attempts and student answers.

require_once __DIR__ . '/../config/database.php';

class AttemptModel
{

    // Get the PDO connection from config/database.php
    private function getConn()
    {
        return getDB();
    }

    // ─────────────────────────────────────────────────────────────
    // getPublishedQuizzes()
    // Returns every quiz whose status = 'published'.
    // Students can only see and take published quizzes.
    // ─────────────────────────────────────────────────────────────
    public function getPublishedQuizzes()
    {
        $db = $this->getConn();

        $stmt = $db->prepare("
            SELECT *
            FROM quizzes
            WHERE status = 'published'
            ORDER BY created_at DESC
        ");
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // ─────────────────────────────────────────────────────────────
    // getStudentAttempt($student_id, $quiz_id)
    // Returns the attempt row for this student + quiz, or NULL if
    // they have never started this quiz.
    // ─────────────────────────────────────────────────────────────
    public function getStudentAttempt($student_id, $quiz_id)
    {
        $db = $this->getConn();

        $stmt = $db->prepare("
            SELECT *
            FROM attempts
            WHERE student_id = ?
              AND quiz_id    = ?
            LIMIT 1
        ");
        $stmt->execute([$student_id, $quiz_id]);

        // fetch() returns the row, or FALSE if not found
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        // Return NULL instead of FALSE — easier to check with if (!$attempt)
        return $row ?: null;
    }

    // ─────────────────────────────────────────────────────────────
    // createAttempt($quiz_id, $student_id)
    // Inserts a new attempt row.
    // score starts as NULL (not graded yet).
    // started_at is set to NOW() by the database.
    // Returns the new attempt's ID.
    // ─────────────────────────────────────────────────────────────
    public function createAttempt($quiz_id, $student_id)
    {
        $db = $this->getConn();

        $stmt = $db->prepare("
            INSERT INTO attempts (quiz_id, student_id, score, started_at)
            VALUES (?, ?, NULL, NOW())
        ");
        $stmt->execute([$quiz_id, $student_id]);

        // Return the auto-generated ID so we can use it immediately
        return $db->lastInsertId();
    }

    // ─────────────────────────────────────────────────────────────
    // getAttemptById($id)
    // Returns a single attempt row by its primary key.
    // Used in the result page and submit endpoint.
    // ─────────────────────────────────────────────────────────────
    public function getAttemptById($id)
    {
        $db = $this->getConn();

        $stmt = $db->prepare("SELECT * FROM attempts WHERE id = ?");
        $stmt->execute([$id]);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    // ─────────────────────────────────────────────────────────────
    // saveAnswer($attempt_id, $question_id, $selected_option_id)
    // Records which option the student chose for one question.
    // Called once per question during quiz submission.
    // ─────────────────────────────────────────────────────────────
    public function saveAnswer($attempt_id, $question_id, $selected_option_id)
    {
        $db = $this->getConn();

        $stmt = $db->prepare("
            INSERT INTO answers (attempt_id, question_id, selected_option_id)
            VALUES (?, ?, ?)
        ");
        $stmt->execute([$attempt_id, $question_id, $selected_option_id]);
    }

    // ─────────────────────────────────────────────────────────────
    // isCorrect($option_id)
    // Checks whether a specific option is the correct answer.
    // Returns true if is_correct = 1, false otherwise.
    // ─────────────────────────────────────────────────────────────
    public function isCorrect($option_id)
    {
        $db = $this->getConn();

        $stmt = $db->prepare("
            SELECT is_correct
            FROM options
            WHERE id = ?
        ");
        $stmt->execute([$option_id]);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        // If the option doesn't exist, treat it as wrong
        if (!$row) return false;

        // is_correct is stored as 1 (correct) or 0 (wrong)
        return (int) $row['is_correct'] === 1;
    }

    // ─────────────────────────────────────────────────────────────
    // completeAttempt($attempt_id, $score)
    // Writes the final score and marks completed_at = NOW().
    // Called once after all answers have been graded.
    // ─────────────────────────────────────────────────────────────
    public function completeAttempt($attempt_id, $score)
    {
        $db = $this->getConn();

        $stmt = $db->prepare("
            UPDATE attempts
            SET score        = ?,
                completed_at = NOW()
            WHERE id = ?
        ");
        $stmt->execute([$score, $attempt_id]);
    }

    // ─────────────────────────────────────────────────────────────
    // hasCompletedAttempt($student_id, $quiz_id)
    // Returns true if the student already has a COMPLETED attempt
    // for this quiz (completed_at IS NOT NULL).
    // Used to block re-submission.
    // ─────────────────────────────────────────────────────────────
    public function hasCompletedAttempt($student_id, $quiz_id)
    {
        $db = $this->getConn();

        $stmt = $db->prepare("
            SELECT id
            FROM attempts
            WHERE student_id   = ?
              AND quiz_id      = ?
              AND completed_at IS NOT NULL
            LIMIT 1
        ");
        $stmt->execute([$student_id, $quiz_id]);

        // If a row exists, the student already completed this quiz
        return $stmt->fetch() !== false;
    }
    // Count completed attempts for a student
    public function countCompletedAttempts($student_id)
    {
        $db = $this->getConn();

        $stmt = $db->prepare("
            SELECT COUNT(*) as total
            FROM attempts
            WHERE student_id   = ?
              AND completed_at IS NOT NULL
        ");
        $stmt->execute([$student_id]);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return (int)($row['total'] ?? 0);
    }

    // Sum of all scores for a student
    public function getTotalScore($student_id)
    {
        $db = $this->getConn();

        $stmt = $db->prepare("
            SELECT SUM(score) as total
            FROM attempts
            WHERE student_id   = ?
              AND completed_at IS NOT NULL
        ");
        $stmt->execute([$student_id]);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return (int)($row['total'] ?? 0);
    }
    public function getValidatedAnswer($quiz_id, $question_id, $option_id)
    {
        $db = $this->getConn();

        $stmt = $db->prepare("
        SELECT 
            o.id AS option_id,
            o.is_correct,
            q.id AS question_id,
            q.marks
        FROM options o
        JOIN questions q ON q.id = o.question_id
        WHERE o.id = ?
          AND q.id = ?
          AND q.quiz_id = ?
    ");

        $stmt->execute([$option_id, $question_id, $quiz_id]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}

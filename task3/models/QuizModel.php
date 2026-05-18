<?php
// models/QuizModel.php
// QuizModel handles all database operations related to quizzes.

require_once __DIR__ . '/../config/database.php';

class QuizModel
{

    // Get the PDO database connection
    private function getConn()
    {
        return getDB(); // getDB() is defined in config/database.php
    }

    /**
     * Create a new quiz (always starts as 'draft').
     * Returns the new quiz's ID.
     */
    public function createQuiz($instructor_id, $title, $description, $time_limit)
    {
        $db = $this->getConn();

        // Insert new quiz row — status is always 'draft', total_marks starts at 0
        $stmt = $db->prepare("
            INSERT INTO quizzes (instructor_id, title, description, total_marks, time_limit_minutes, status, created_at)
            VALUES (?, ?, ?, 0, ?, 'draft', NOW())
        ");
        $stmt->execute([$instructor_id, $title, $description, $time_limit]);

        // Return the ID of the newly created quiz
        return $db->lastInsertId();
    }

    /**
     * Get all quizzes that belong to a specific instructor.
     */
    public function getQuizzesByInstructor($instructor_id)
    {
        $db = $this->getConn();

        $stmt = $db->prepare("
            SELECT * FROM quizzes
            WHERE instructor_id = ?
            ORDER BY created_at DESC
        ");
        $stmt->execute([$instructor_id]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get a single quiz by its ID.
     */
    public function getQuizById($id)
    {
        $db = $this->getConn();

        $stmt = $db->prepare("SELECT * FROM quizzes WHERE id = ?");
        $stmt->execute([$id]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Update the title, description, and time limit of a quiz.
     */
    public function updateQuiz($id, $title, $description, $time_limit)
    {
        $db = $this->getConn();

        $stmt = $db->prepare("
            UPDATE quizzes
            SET title = ?, description = ?, time_limit_minutes = ?
            WHERE id = ?
        ");
        $stmt->execute([$title, $description, $time_limit, $id]);
    }

    /**
     * Delete a quiz and all its related questions and options.
     * We must delete child records (options, questions) before the parent (quiz).
     */
    public function deleteQuiz($id)
    {
        $db = $this->getConn();

        // Step 1: Delete all options for questions in this quiz
        $stmt = $db->prepare("
            DELETE o FROM options o
            INNER JOIN questions q ON o.question_id = q.id
            WHERE q.quiz_id = ?
        ");
        $stmt->execute([$id]);

        // Step 2: Delete all questions for this quiz
        $stmt = $db->prepare("DELETE FROM questions WHERE quiz_id = ?");
        $stmt->execute([$id]);

        // Step 3: Delete the quiz itself
        $stmt = $db->prepare("DELETE FROM quizzes WHERE id = ?");
        $stmt->execute([$id]);
    }

    /**
     * Toggle the status of a quiz between 'draft' and 'published'.
     */
    public function toggleStatus($id)
    {
        $db = $this->getConn();

        // First get the current status
        $quiz = $this->getQuizById($id);

        if (!$quiz) {
            return false; // Quiz not found
        }

        // Flip the status
        $newStatus = ($quiz['status'] === 'draft') ? 'published' : 'draft';

        $stmt = $db->prepare("UPDATE quizzes SET status = ? WHERE id = ?");
        $stmt->execute([$newStatus, $id]);

        return $newStatus; // Return the new status so the API can send it back
    }

    /**
     * Recalculate total_marks for a quiz by summing all question marks.
     * Called after adding, editing, or deleting a question.
     */
    public function updateTotalMarks($quiz_id)
    {
        $db = $this->getConn();

        // SUM the marks column from questions for this quiz
        $stmt = $db->prepare("
            SELECT COALESCE(SUM(marks), 0) AS total
            FROM questions
            WHERE quiz_id = ?
        ");
        $stmt->execute([$quiz_id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $total = $row['total'];

        // Update the quizzes table with the new total
        $stmt = $db->prepare("UPDATE quizzes SET total_marks = ? WHERE id = ?");
        $stmt->execute([$total, $quiz_id]);
    }
    // Count all published quizzes
    public function countPublishedQuizzes()
    {
        $db = $this->getConn();

        $stmt = $db->prepare("
            SELECT COUNT(*) as total
            FROM quizzes
            WHERE status = 'published'
        ");
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return (int)($row['total'] ?? 0);
    }
}

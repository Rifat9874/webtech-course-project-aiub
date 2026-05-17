<?php
// models/QuestionModel.php
// QuestionModel handles all database operations for questions and their options.

require_once __DIR__ . '/../config/database.php';

class QuestionModel
{

    // Get the PDO database connection
    private function getConn()
    {
        return getDB(); // getDB() is defined in config/database.php
    }

    /**
     * Add a new question to a quiz.
     * Returns the new question's ID (needed to then add its options).
     */
    public function addQuestion($quiz_id, $question_text, $marks, $order_index)
    {
        $db = $this->getConn();

        $stmt = $db->prepare("
            INSERT INTO questions (quiz_id, question_text, marks, order_index)
            VALUES (?, ?, ?, ?)
        ");
        $stmt->execute([$quiz_id, $question_text, $marks, $order_index]);

        // Return the new question's ID so we can attach options to it
        return $db->lastInsertId();
    }

    /**
     * Add a single option (answer choice) for a question.
     * is_correct = 1 means this option is the correct answer.
     */
    public function addOption($question_id, $option_text, $is_correct)
    {
        $db = $this->getConn();

        $stmt = $db->prepare("
            INSERT INTO options (question_id, option_text, is_correct)
            VALUES (?, ?, ?)
        ");
        $stmt->execute([$question_id, $option_text, $is_correct]);
    }

    /**
     * Get all questions for a quiz, each with its 4 options.
     * Returns an array of questions; each question has an 'options' sub-array.
     */
    public function getQuestionsByQuiz($quiz_id)
    {
        $db = $this->getConn();

        // Fetch all questions for this quiz, ordered by order_index
        $stmt = $db->prepare("
            SELECT * FROM questions
            WHERE quiz_id = ?
            ORDER BY order_index ASC
        ");
        $stmt->execute([$quiz_id]);
        $questions = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // For each question, fetch its options
        foreach ($questions as &$question) {
            $optStmt = $db->prepare("
                SELECT * FROM options
                WHERE question_id = ?
                ORDER BY id ASC
            ");
            $optStmt->execute([$question['id']]);
            $question['options'] = $optStmt->fetchAll(PDO::FETCH_ASSOC);
        }
        unset($question); // Break the reference to avoid bugs

        return $questions;
    }

    /**
     * Update the text of a question.
     */
    public function updateQuestion($id, $question_text)
    {
        $db = $this->getConn();

        $stmt = $db->prepare("
            UPDATE questions SET question_text = ? WHERE id = ?
        ");
        $stmt->execute([$question_text, $id]);
    }

    /**
     * Update all 4 options for a question.
     * Sets is_correct = 1 only for the correct_option_id; all others become 0.
     *
     * $options_array = array of ['id' => ..., 'text' => ...]
     * $correct_option_id = the option ID that is correct
     */
    public function updateOptions($question_id, $options_array, $correct_option_id)
    {
        $db = $this->getConn();

        // First set ALL options for this question to is_correct = 0
        $stmt = $db->prepare("UPDATE options SET is_correct = 0 WHERE question_id = ?");
        $stmt->execute([$question_id]);

        // Loop through each option and update its text
        foreach ($options_array as $option) {
            $stmt = $db->prepare("
                UPDATE options SET option_text = ? WHERE id = ?
            ");
            $stmt->execute([$option['text'], $option['id']]);
        }

        // Now mark the correct option
        $stmt = $db->prepare("UPDATE options SET is_correct = 1 WHERE id = ?");
        $stmt->execute([$correct_option_id]);
    }

    /**
     * Delete a question and all its options.
     * Options must be deleted first (foreign key constraint).
     */
    public function deleteQuestion($id)
    {
        $db = $this->getConn();

        // Step 1: Delete all options for this question
        $stmt = $db->prepare("DELETE FROM options WHERE question_id = ?");
        $stmt->execute([$id]);

        // Step 2: Delete the question itself
        $stmt = $db->prepare("DELETE FROM questions WHERE id = ?");
        $stmt->execute([$id]);
    }

    /**
     * Count how many questions a quiz has.
     * Used to set order_index when adding a new question.
     */
    public function countQuestions($quiz_id)
    {
        $db = $this->getConn();

        $stmt = $db->prepare("SELECT COUNT(*) FROM questions WHERE quiz_id = ?");
        $stmt->execute([$quiz_id]);

        return (int) $stmt->fetchColumn();
    }

    /**
     * Get a single question by its ID.
     * Used in API endpoints to verify ownership before editing/deleting.
     */
    public function getQuestionById($id)
    {
        $db = $this->getConn();

        $stmt = $db->prepare("SELECT * FROM questions WHERE id = ?");
        $stmt->execute([$id]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}

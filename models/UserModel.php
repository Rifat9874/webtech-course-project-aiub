<?php
// models/UserModel.php
// This file handles all database queries related to users.
// Think of a "model" as the part of your app that talks to the database.

class UserModel
{

    // $db holds our database connection (PDO object)
    private $db;

    // The constructor runs automatically when you do: new UserModel($pdo)
    public function __construct($db)
    {
        $this->db = $db;
    }

    // -------------------------------------------------------------------------
    // findByEmail($email)
    // Looks up a single user row by their email address.
    // Returns the user as an associative array, or false if not found.
    // -------------------------------------------------------------------------
    public function findByEmail($email)
    {
        // Prepare the SQL — the :email placeholder prevents SQL injection
        $stmt = $this->db->prepare(
            "SELECT * FROM users WHERE email = :email LIMIT 1"
        );
        // Bind the real value to the placeholder and run the query
        $stmt->execute([':email' => $email]);
        // fetch() returns one row as an array (keys = column names), or false
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // -------------------------------------------------------------------------
    // createUser($name, $email, $password_hash, $role)
    // Inserts a brand-new user row into the database.
    // Returns true on success, false on failure.
    // -------------------------------------------------------------------------
    public function createUser($name, $email, $password_hash, $role, $isActive = 1)
    {
        $stmt = $this->db->prepare(
            "INSERT INTO users (name, email, password_hash, role, is_active, created_at)
         VALUES (:name, :email, :password_hash, :role, :is_active, NOW())"
        );
        return $stmt->execute([
            ':name'          => $name,
            ':email'         => $email,
            ':password_hash' => $password_hash,
            ':role'          => $role,
            ':is_active'     => $isActive,
        ]);
    }
    // -------------------------------------------------------------------------
    // getAllUsers()
    // Returns every user in the database as an array of rows.
    // Used by the admin panel.
    // -------------------------------------------------------------------------
    public function getAllUsers()
    {
        $stmt = $this->db->prepare("SELECT * FROM users ORDER BY created_at DESC");
        $stmt->execute();
        // fetchAll() returns ALL matching rows at once
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // -------------------------------------------------------------------------
    // toggleActive($userId)
    // Flips is_active between 0 (suspended) and 1 (active) for a given user.
    // Uses a neat SQL trick: (1 - is_active) flips 1→0 and 0→1.
    // -------------------------------------------------------------------------
    public function toggleActive($userId)
    {
        $stmt = $this->db->prepare(
            "UPDATE users SET is_active = (1 - is_active) WHERE id = :id"
        );
        $stmt->execute([':id' => $userId]);

        // Now fetch the NEW value of is_active so we can return it to the caller
        $user = $this->getUserById($userId);
        return $user['is_active'];
    }

    // -------------------------------------------------------------------------
    // getUserById($id)
    // Fetches a single user by their numeric ID.
    // -------------------------------------------------------------------------
    public function getUserById($id)
    {
        $stmt = $this->db->prepare(
            "SELECT * FROM users WHERE id = :id LIMIT 1"
        );
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // -------------------------------------------------------------------------
    // getStudentStats($studentId)
    // Returns stats for the student dashboard:
    //   - available_quizzes : total quizzes in the system
    //   - attempts_taken    : how many times this student has attempted quizzes
    //   - total_score       : sum of their scores across all attempts
    //
    // We use COALESCE(value, 0) so that NULL (no rows) becomes 0.
    // -------------------------------------------------------------------------
    public function getStudentStats($studentId)
    {
        // Count all quizzes available in the system
        $quizStmt = $this->db->prepare(
            "SELECT COUNT(*) AS available_quizzes FROM quizzes WHERE status = 'published'"
        );
        $quizStmt->execute();
        $quizRow = $quizStmt->fetch(PDO::FETCH_ASSOC);

        // Count attempts and total score for this specific student
        // LEFT JOIN means we still get a row even if there are no attempts
        $attemptStmt = $this->db->prepare(
            "SELECT
                COUNT(a.id)               AS attempts_taken,
                COALESCE(SUM(a.score), 0) AS total_score
             FROM users u
             LEFT JOIN attempts a ON a.student_id = u.id
                                 AND a.completed_at IS NOT NULL
             WHERE u.id = :student_id"
        );
        $attemptStmt->execute([':student_id' => $studentId]);
        $attemptRow = $attemptStmt->fetch(PDO::FETCH_ASSOC);

        // Merge both result rows into one array and return it
        return [
            'available_quizzes' => $quizRow['available_quizzes']  ?? 0,
            'attempts_taken'    => $attemptRow['attempts_taken']   ?? 0,
            'total_score'       => $attemptRow['total_score']      ?? 0,
        ];
    }

    // -------------------------------------------------------------------------
    // getInstructorStats($instructorId)
    // Returns stats for the instructor dashboard:
    //   - quiz_count    : quizzes this instructor has created
    //   - total_attempts: total attempts made on their quizzes
    // -------------------------------------------------------------------------
    public function getInstructorStats($instructorId)
    {
        $stmt = $this->db->prepare(
            "SELECT
                COUNT(DISTINCT q.id)  AS quiz_count,
                COALESCE(COUNT(a.id), 0) AS total_attempts
             FROM users u
             LEFT JOIN quizzes  q ON q.instructor_id = u.id
             LEFT JOIN attempts a ON a.quiz_id = q.id
             WHERE u.id = :instructor_id"
        );
        $stmt->execute([':instructor_id' => $instructorId]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return [
            'quiz_count'     => $row['quiz_count']     ?? 0,
            'total_attempts' => $row['total_attempts']  ?? 0,
        ];
    }
}

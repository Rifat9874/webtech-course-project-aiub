-- ============================================================
-- database.sql — Online Quiz & Exam Platform
-- Import this file in phpMyAdmin to set up all tables.
-- Database name: quiz_platform
-- ============================================================

CREATE DATABASE IF NOT EXISTS quiz_platform
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE quiz_platform;

-- Users: students, instructors, admins
CREATE TABLE IF NOT EXISTS users (
    id            INT AUTO_INCREMENT PRIMARY KEY,
    name          VARCHAR(100) NOT NULL,
    email         VARCHAR(150) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    role          ENUM('student','instructor','admin') NOT NULL DEFAULT 'student',
    is_active     TINYINT(1)  NOT NULL DEFAULT 1,
    created_at    TIMESTAMP   NOT NULL DEFAULT CURRENT_TIMESTAMP
);

-- Quizzes: created by instructors
CREATE TABLE IF NOT EXISTS quizzes (
    id                 INT AUTO_INCREMENT PRIMARY KEY,
    instructor_id      INT          NOT NULL,
    title              VARCHAR(200) NOT NULL,
    description        TEXT,
    total_marks        INT          NOT NULL DEFAULT 0,
    time_limit_minutes INT          NOT NULL DEFAULT 30,
    status             ENUM('draft','published') NOT NULL DEFAULT 'draft',
    created_at         TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (instructor_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Questions: MCQ questions belonging to a quiz
CREATE TABLE IF NOT EXISTS questions (
    id            INT AUTO_INCREMENT PRIMARY KEY,
    quiz_id       INT  NOT NULL,
    question_text TEXT NOT NULL,
    marks         INT  NOT NULL DEFAULT 1,
    order_index   INT  NOT NULL DEFAULT 0,
    FOREIGN KEY (quiz_id) REFERENCES quizzes(id) ON DELETE CASCADE
);

-- Options: 4 choices per question, one marked correct
CREATE TABLE IF NOT EXISTS options (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    question_id INT          NOT NULL,
    option_text VARCHAR(500) NOT NULL,
    is_correct  TINYINT(1)  NOT NULL DEFAULT 0,
    FOREIGN KEY (question_id) REFERENCES questions(id) ON DELETE CASCADE
);

-- Attempts: one row per student per quiz attempt
CREATE TABLE IF NOT EXISTS attempts (
    id           INT AUTO_INCREMENT PRIMARY KEY,
    quiz_id      INT      NOT NULL,
    student_id   INT      NOT NULL,
    score        INT      NULL DEFAULT NULL,
    started_at   DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    completed_at DATETIME NULL DEFAULT NULL,
    FOREIGN KEY (quiz_id)    REFERENCES quizzes(id) ON DELETE CASCADE,
    FOREIGN KEY (student_id) REFERENCES users(id)   ON DELETE CASCADE
);

-- Answers: one row per question per attempt
CREATE TABLE IF NOT EXISTS answers (
    id                 INT AUTO_INCREMENT PRIMARY KEY,
    attempt_id         INT NOT NULL,
    question_id        INT NOT NULL,
    selected_option_id INT NOT NULL,
    FOREIGN KEY (attempt_id)         REFERENCES attempts(id)  ON DELETE CASCADE,
    FOREIGN KEY (question_id)        REFERENCES questions(id) ON DELETE CASCADE,
    FOREIGN KEY (selected_option_id) REFERENCES options(id)  ON DELETE CASCADE
);

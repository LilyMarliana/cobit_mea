-- Database schema for COBIT 5 MEA Assessment System

-- Roles table (must be created first due to foreign key constraints)
CREATE TABLE IF NOT EXISTS roles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    role_name VARCHAR(50) UNIQUE NOT NULL,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Insert default roles
INSERT INTO roles (role_name, description) VALUES
('admin', 'Administrator with full access'),
('user', 'Regular user with limited access');

-- Users table (assuming it already exists from the template)
-- If not, create it:
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    first_name VARCHAR(50) NOT NULL,
    last_name VARCHAR(50) NOT NULL,
    organization VARCHAR(100),
    role_id INT DEFAULT 2,
    is_active TINYINT(1) DEFAULT 1,
    last_login TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (role_id) REFERENCES roles(id)
);

-- MEA Processes table
CREATE TABLE IF NOT EXISTS mea_processes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    process_code VARCHAR(10) UNIQUE NOT NULL,
    process_name VARCHAR(200) NOT NULL,
    description TEXT,
    process_order INT NOT NULL
);

-- Insert MEA processes
INSERT INTO mea_processes (process_code, process_name, description, process_order) VALUES
('MEA01', 'Monitor, Evaluate and Assess Performance and Conformance', 'To provide assurance that IT-related goals, objectives and activities are monitored, evaluated and assessed against relevant criteria.', 1),
('MEA02', 'Monitor, Evaluate and Assess IT Governance System Performance', 'To provide assurance that the IT governance system is performing as required to support the enterprise''s achievement of its goals.', 2),
('MEA03', 'Monitor, Evaluate and Assess Risk', 'To provide assurance that IT-related risks are monitored, evaluated and assessed to support risk management and risk appetite decisions.', 3);

-- Assessment Questions table
CREATE TABLE IF NOT EXISTS assessment_questions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    process_code VARCHAR(10) NOT NULL,
    question_text TEXT NOT NULL,
    question_order INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (process_code) REFERENCES mea_processes(process_code)
);

-- Insert sample questions for MEA processes
INSERT INTO assessment_questions (process_code, question_text, question_order) VALUES
-- MEA01 Questions
('MEA01', 'Does the enterprise have a defined approach for monitoring IT-related performance and conformance?', 1),
('MEA01', 'Are IT-related performance and conformance metrics defined and aligned with enterprise goals?', 2),
('MEA01', 'Is the performance and conformance of IT-related activities regularly reported to stakeholders?', 3),
('MEA01', 'Are IT-related performance and conformance issues identified and addressed?', 4),
('MEA01', 'Is the effectiveness of the monitoring, evaluation and assessment process reviewed and improved?', 5),

-- MEA02 Questions
('MEA02', 'Does the enterprise have a defined approach for monitoring IT governance system performance?', 1),
('MEA02', 'Are IT governance system performance metrics defined and aligned with enterprise governance objectives?', 2),
('MEA02', 'Is IT governance system performance regularly reported to governance bodies?', 3),
('MEA02', 'Are IT governance system performance issues identified and addressed?', 4),
('MEA02', 'Is the effectiveness of the IT governance system monitoring process reviewed and improved?', 5),

-- MEA03 Questions
('MEA03', 'Does the enterprise have a defined approach for monitoring IT-related risks?', 1),
('MEA03', 'Are IT-related risk metrics defined and aligned with enterprise risk management objectives?', 2),
('MEA03', 'Is IT-related risk exposure regularly reported to risk management bodies?', 3),
('MEA03', 'Are IT-related risk management issues identified and addressed?', 4),
('MEA03', 'Is the effectiveness of the IT risk monitoring process reviewed and improved?', 5);

-- Assessments table
CREATE TABLE IF NOT EXISTS assessments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    title VARCHAR(200) NOT NULL,
    description TEXT,
    status ENUM('in_progress', 'completed') DEFAULT 'in_progress',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    completed_at TIMESTAMP NULL,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Assessment Responses table
CREATE TABLE IF NOT EXISTS assessment_responses (
    id INT AUTO_INCREMENT PRIMARY KEY,
    assessment_id INT NOT NULL,
    question_id INT NOT NULL,
    response_value TINYINT NOT NULL, -- 0-5 scale
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (assessment_id) REFERENCES assessments(id) ON DELETE CASCADE,
    FOREIGN KEY (question_id) REFERENCES assessment_questions(id) ON DELETE CASCADE
);

-- Assessment Results table
CREATE TABLE IF NOT EXISTS assessment_results (
    id INT AUTO_INCREMENT PRIMARY KEY,
    assessment_id INT NOT NULL,
    process_code VARCHAR(10) NOT NULL, -- MEA01, MEA02, MEA03
    average_score DECIMAL(3,2) NOT NULL, -- Average maturity level for this process
    total_questions INT NOT NULL,
    total_responses INT NOT NULL,
    calculated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (assessment_id) REFERENCES assessments(id) ON DELETE CASCADE,
    FOREIGN KEY (process_code) REFERENCES mea_processes(process_code)
);

-- Assessment Summary table
CREATE TABLE IF NOT EXISTS assessment_summary (
    id INT AUTO_INCREMENT PRIMARY KEY,
    assessment_id INT NOT NULL,
    overall_maturity_level DECIMAL(3,2) NOT NULL, -- Overall maturity level
    maturity_status VARCHAR(20) NOT NULL, -- Initial, Repeatable, Defined, Managed, Optimized
    recommendations TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (assessment_id) REFERENCES assessments(id) ON DELETE CASCADE
);

-- Activity logs table (if not already in the template)
CREATE TABLE IF NOT EXISTS activity_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    activity_type VARCHAR(50) NOT NULL,
    description TEXT,
    ip_address VARCHAR(45),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);


-- Failed login attempts table (for security)
CREATE TABLE IF NOT EXISTS failed_login_attempts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    ip_address VARCHAR(45) NOT NULL,
    username VARCHAR(50),
    attempt_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Insert default admin user (password: admin123)
-- Password hash for 'admin123' using password_hash()
-- NOTE: Please replace CORRECT_HASH_VALUE with the actual hash generated by generate_correct_hash.php
INSERT INTO users (username, email, password, first_name, last_name, organization, role_id, is_active) VALUES
('admin', 'admin@example.com', 'CORRECT_HASH_VALUE', 'Administrator', 'Account', 'COBIT MEA Organization', 1, 1),
('user', 'user@example.com', 'CORRECT_HASH_VALUE', 'Regular', 'User', 'COBIT MEA Organization', 2, 1),
('lily', 'lilymarliana392@gmail.com', 'CORRECT_HASH_VALUE', 'Lily', 'Marliana', 'COBIT MEA Organization', 2, 1);
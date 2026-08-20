-- Hi Teac Academy Database Schema
-- Compatible with MySQL/MariaDB
-- Recommended Database Name: hi_teac_academy

SET FOREIGN_KEY_CHECKS = 0;
DROP TABLE IF EXISTS `notifications`;
DROP TABLE IF EXISTS `settings`;
DROP TABLE IF EXISTS `contacts`;
DROP TABLE IF EXISTS `announcements`;
DROP TABLE IF EXISTS `gallery`;
DROP TABLE IF EXISTS `replies`;
DROP TABLE IF EXISTS `questions`;
DROP TABLE IF EXISTS `admissions`;
DROP TABLE IF EXISTS `courses`;
DROP TABLE IF EXISTS `teachers`;
DROP TABLE IF EXISTS `students`;
DROP TABLE IF EXISTS `admins`;
SET FOREIGN_KEY_CHECKS = 1;

-- 1. Admins Table
CREATE TABLE `admins` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `username` VARCHAR(50) NOT NULL UNIQUE,
  `password` VARCHAR(255) NOT NULL,
  `email` VARCHAR(100) NOT NULL UNIQUE,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2. Students Table
CREATE TABLE `students` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `first_name` VARCHAR(50) NOT NULL,
  `last_name` VARCHAR(50) NOT NULL,
  `email` VARCHAR(100) NOT NULL UNIQUE,
  `password` VARCHAR(255) NOT NULL,
  `phone` VARCHAR(20) DEFAULT NULL,
  `gender` VARCHAR(15) DEFAULT NULL,
  `dob` DATE DEFAULT NULL,
  `address` TEXT DEFAULT NULL,
  `profile_pic` VARCHAR(255) DEFAULT NULL,
  `status` VARCHAR(20) DEFAULT 'Active',
  `reset_token` VARCHAR(255) DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 3. Teachers Table
CREATE TABLE `teachers` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(100) NOT NULL,
  `designation` VARCHAR(100) NOT NULL,
  `specialization` VARCHAR(255) NOT NULL,
  `photo` VARCHAR(255) DEFAULT NULL,
  `email` VARCHAR(100) DEFAULT NULL,
  `phone` VARCHAR(20) DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 4. Courses Table
CREATE TABLE `courses` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(100) NOT NULL,
  `code` VARCHAR(20) NOT NULL UNIQUE,
  `duration` VARCHAR(50) NOT NULL,
  `fee` DECIMAL(10, 2) NOT NULL,
  `description` TEXT NOT NULL,
  `modules` TEXT NOT NULL, -- Stored as comma-separated or text block
  `instructor_id` INT DEFAULT NULL,
  `status` VARCHAR(20) DEFAULT 'Active',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`instructor_id`) REFERENCES `teachers` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 5. Admissions Table
CREATE TABLE `admissions` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `student_id` INT NOT NULL,
  `course_id` INT NOT NULL,
  `apply_date` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `status` VARCHAR(20) DEFAULT 'Pending', -- Pending, Approved, Rejected
  `review_comments` TEXT DEFAULT NULL,
  `matric_certificate` VARCHAR(255) NOT NULL, -- Path to file upload
  `cnic_copy` VARCHAR(255) NOT NULL,          -- Path to file upload
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE,
  FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 6. Questions Table
CREATE TABLE `questions` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `student_id` INT NOT NULL,
  `subject` VARCHAR(255) NOT NULL,
  `question_text` TEXT NOT NULL,
  `status` VARCHAR(20) DEFAULT 'Pending', -- Pending, Answered
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 7. Replies Table
CREATE TABLE `replies` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `question_id` INT NOT NULL,
  `replier_type` VARCHAR(20) NOT NULL, -- Admin, Teacher
  `replier_id` INT NOT NULL,           -- Admin ID or Teacher ID
  `reply_text` TEXT NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`question_id`) REFERENCES `questions` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 8. Gallery Table
CREATE TABLE `gallery` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `title` VARCHAR(150) NOT NULL,
  `image_path` VARCHAR(255) NOT NULL,
  `category` VARCHAR(50) NOT NULL, -- Campus, Classrooms, Labs, Events
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 9. Announcements Table
CREATE TABLE `announcements` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `title` VARCHAR(200) NOT NULL,
  `content` TEXT NOT NULL,
  `type` VARCHAR(50) DEFAULT 'General', -- General, Exam, Holiday, Admission
  `status` VARCHAR(20) DEFAULT 'Active',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 10. Contacts Table
CREATE TABLE `contacts` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(100) NOT NULL,
  `email` VARCHAR(100) NOT NULL,
  `subject` VARCHAR(200) NOT NULL,
  `message` TEXT NOT NULL,
  `status` VARCHAR(20) DEFAULT 'Pending', -- Pending, Replied
  `reply_text` TEXT DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 11. Settings Table
CREATE TABLE `settings` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `setting_key` VARCHAR(100) NOT NULL UNIQUE,
  `setting_value` TEXT DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 12. Notifications Table
CREATE TABLE `notifications` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `user_type` VARCHAR(20) NOT NULL, -- Student, Admin
  `user_id` INT NOT NULL,
  `title` VARCHAR(255) NOT NULL,
  `message` TEXT NOT NULL,
  `is_read` TINYINT(1) DEFAULT 0,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =========================================================
-- SEED DATA
-- =========================================================

-- Seed default Admin (Password: AdminPassword123)
INSERT INTO `admins` (`username`, `password`, `email`) VALUES
('admin', '$2y$12$NwAEcSXdiffXF4VEUslhfeqpG3YUiDrFDEAe2hHlA7.SwKIECMXuS', 'admin@hiteacademy.edu');

-- Seed default Settings
INSERT INTO `settings` (`setting_key`, `setting_value`) VALUES
('site_name', 'Hi Teac Academy'),
('contact_email', 'shahzaibbangash24@gmail.com'),
('contact_phone', '03304347547'),
('address', 'Kohat, KPK, Pakistan'),
('facebook_url', 'https://facebook.com/hiteacademy'),
('twitter_url', 'https://twitter.com/hiteacademy'),
('instagram_url', 'https://instagram.com/hiteacademy'),
('youtube_url', 'https://youtube.com/hiteacademy'),
('admission_status', 'Open');

-- Seed initial Teachers
INSERT INTO `teachers` (`id`, `name`, `designation`, `specialization`, `photo`, `email`, `phone`) VALUES
(1, 'Engr. Muhammad Ali', 'Senior IT Instructor', 'Web Development, Networking & Cybersecurity', 'assets/images/teacher-ali.jpg', 'ali.instructor@hiteacademy.edu.pk', '+92 321 9876543'),
(2, 'Prof. Sarah Khan', 'Office Automation Specialist', 'MS Office, Office Management & Graphic Design', 'assets/images/teacher-sarah.jpg', 'sarah.instructor@hiteacademy.edu.pk', '+92 333 4567890');

-- Seed initial Courses (DIT and CIT)
INSERT INTO `courses` (`id`, `name`, `code`, `duration`, `fee`, `description`, `modules`, `instructor_id`, `status`) VALUES
(1, 'Diploma in Information Technology (DIT)', 'DIT-01', '1 Year (2 Semesters)', 24000.00, 'A comprehensive one-year diploma registered with the Board of Technical Education, covering computer software, networking, web development, and database administration.', 'Semester 1: Information Technology, Office Automation, Web Designing & Development (HTML/CSS/JS), C/C++ Programming\nSemester 2: Graphic Designing, Database Systems (SQL), Operating Systems, Computer Networking', 1, 'Active'),
(2, 'Certificate in Information Technology (CIT)', 'CIT-02', '6 Months', 12000.00, 'A foundational six-month certificate program covering fundamental concepts of operating systems, office software suites, simple scripting, and internet technologies.', 'Module 1: Computer Fundamentals & Windows\nModule 2: MS Office (Word, Excel, PowerPoint, Access)\nModule 3: Inpage Urdu & Basic Graphics\nModule 4: Internet, Emails & Business Communications', 2, 'Active');

-- Seed initial Announcements
INSERT INTO `announcements` (`title`, `content`, `type`, `status`) VALUES
('Admissions Open for DIT & CIT Fall Semester', 'Hi Teac Academy is proud to announce that admissions for the Fall 2026 batch of DIT and CIT courses are now officially open. Register online and apply today to secure your seat!', 'Admission', 'Active'),
('Independence Day Holiday Notice', 'Please note that the academy will remain closed on 14th August in observance of Independence Day. Regular classes will resume from 15th August.', 'Holiday', 'Active');

-- Seed initial Gallery images
INSERT INTO `gallery` (`title`, `image_path`, `category`) VALUES
('High-Tech Computer Lab', 'assets/images/gallery-lab.jpg', 'Labs'),
('Interactive Lecture Hall', 'assets/images/gallery-classroom.jpg', 'Classrooms'),
('Coding Bootcamp Event', 'assets/images/gallery-event.jpg', 'Events'),
('Main Building Frontage', 'assets/images/gallery-campus.jpg', 'Campus');

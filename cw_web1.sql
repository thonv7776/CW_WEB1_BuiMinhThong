

CREATE DATABASE IF NOT EXISTS `cw_web1`
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;
USE `cw_web1`;


DROP TABLE IF EXISTS `admin_activity_log`;
DROP TABLE IF EXISTS `questions`;
DROP TABLE IF EXISTS `messages`;
DROP TABLE IF EXISTS `admins`;
DROP TABLE IF EXISTS `users`;
DROP TABLE IF EXISTS `modules`;


CREATE TABLE `users` (
    `user_id`        INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `username`       VARCHAR(50)  NOT NULL,
    `email`          VARCHAR(120) NOT NULL,
    `password_hash`  VARCHAR(255) DEFAULT NULL,
    `otp_code`       VARCHAR(6)   DEFAULT NULL,
    `otp_expires_at` DATETIME     DEFAULT NULL,
    `is_verified`    TINYINT(1)   NOT NULL DEFAULT 0,
    `created_at`     TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`user_id`),
    UNIQUE KEY `uq_users_email` (`email`),
    UNIQUE KEY `uq_users_username` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE `modules` (
    `module_id`   INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `module_name` VARCHAR(100) NOT NULL,
    PRIMARY KEY (`module_id`),
    UNIQUE KEY `uq_modules_name` (`module_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE `questions` (
    `question_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `title`       VARCHAR(150) NOT NULL,
    `content`     TEXT         NOT NULL,
    `image`       VARCHAR(255) DEFAULT NULL,
    `user_id`     INT UNSIGNED NOT NULL,
    `module_id`   INT UNSIGNED NOT NULL,
    `created_at`  TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`question_id`),
    KEY `fk_questions_user`   (`user_id`),
    KEY `fk_questions_module` (`module_id`),
    CONSTRAINT `fk_questions_user`
        FOREIGN KEY (`user_id`)   REFERENCES `users`   (`user_id`)
        ON DELETE RESTRICT ON UPDATE CASCADE,
    CONSTRAINT `fk_questions_module`
        FOREIGN KEY (`module_id`) REFERENCES `modules` (`module_id`)
        ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE `messages` (
    `message_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `name`       VARCHAR(100) NOT NULL,
    `email`      VARCHAR(120) NOT NULL,
    `subject`    VARCHAR(150) NOT NULL,
    `body`       TEXT         NOT NULL,
    `created_at` TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`message_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE `admins` (
    `admin_id`        INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `username`        VARCHAR(50)  NOT NULL,
    `password_hash`   VARCHAR(255) NOT NULL,
    PRIMARY KEY (`admin_id`),
    UNIQUE KEY `uq_admins_username` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE `admin_activity_log` (
    `log_id`        INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `admin_id`      INT UNSIGNED DEFAULT NULL,
    `action`        VARCHAR(50)  NOT NULL,
    `target_entity` VARCHAR(50)  DEFAULT NULL,
    `target_id`     INT UNSIGNED DEFAULT NULL,
    `created_at`    TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`log_id`),
    KEY `fk_activity_admin` (`admin_id`),
    CONSTRAINT `fk_activity_admin`
        FOREIGN KEY (`admin_id`) REFERENCES `admins` (`admin_id`)
        ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



INSERT INTO `users` (`user_id`, `username`, `email`, `password_hash`, `is_verified`) VALUES
    (1, 'Alice Nguyen', 'alice.nguyen@example.com', '$2y$10$Ms/pMmZ27nxdHeworOPxEO5aRKaNBFokqSSB03JR6vXZGowuj67CS', 1),
    (2, 'Bilal Ahmed',  'bilal.ahmed@example.com',  NULL, 1),
    (3, 'Chloe Baker',  'chloe.baker@example.com',  NULL, 1),
    (4, 'Dev Patel',    'dev.patel@example.com',    NULL, 1);

INSERT INTO `modules` (`module_id`, `module_name`) VALUES
    (1, 'COMP1841 Web Programming 1'),
    (2, 'COMP1842 Web Programming 2'),
    (3, 'COMP1811 Programming Fundamentals'),
    (4, 'COMP1848 Databases');

-- ~4 questions; two carry an optional screenshot, two do not.
INSERT INTO `questions` (`question_id`, `title`, `content`, `image`, `user_id`, `module_id`) VALUES
    (1,
     'Why does my PDO prepared statement return no rows?',
     'I am using a named placeholder :id but execute() returns an empty set even though the row exists in phpMyAdmin. Am I binding the parameter correctly?',
     'pdo-empty-result.png', 1, 1),
    (2,
     'How do I center a card with CSS flexbox?',
     'I want a single card centred both horizontally and vertically in the viewport. justify-content and align-items are not doing what I expect. What is the minimal flexbox setup?',
     'flexbox-center.png', 3, 1),
    (3,
     'INNER JOIN vs LEFT JOIN for my questions list',
     'My question list should show the author and module for every post. Should I use INNER JOIN or LEFT JOIN, and what is the practical difference for my data?',
     NULL, 2, 4),
    (4,
     'move_uploaded_file returns false on WAMP',
     'My image upload validation passes but move_uploaded_file() keeps returning false. The target public/images/ folder exists. What am I missing on Windows/WAMP?',
     NULL, 4, 2);

INSERT INTO `messages` (`message_id`, `name`, `email`, `subject`, `body`) VALUES
    (1, 'Erin Fisher', 'erin.fisher@example.com', 'Question about posting images',
        'Hi, is there a maximum file size for the screenshots I attach to a question? Thanks.');


INSERT INTO `admins` (`admin_id`, `username`, `password_hash`) VALUES
    (1, 'admin', '$2y$10$XJEMo0b47iXFqVMtdwFoVu0FugCbLR5pLTySvtjSbNxhnfp0aKfau');

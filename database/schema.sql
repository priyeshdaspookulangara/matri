-- Guruvision Matrimony Database Schema
-- Normalized relational design with foreign key constraints

SET FOREIGN_KEY_CHECKS = 0;
DROP TABLE IF EXISTS user_videos;
DROP TABLE IF EXISTS user_photos;
DROP TABLE IF EXISTS partner_preferences;
DROP TABLE IF EXISTS success_stories;
DROP TABLE IF EXISTS users;
SET FOREIGN_KEY_CHECKS = 1;

CREATE TABLE users (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    profile_id VARCHAR(20) NOT NULL UNIQUE,
    email VARCHAR(150) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    is_premium TINYINT(1) NOT NULL DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    -- Account Details
    created_by ENUM('Self', 'Parents', 'Sibling', 'Relative', 'Friend') NOT NULL,
    full_name VARCHAR(100) NOT NULL,

    -- Basic Details
    gender ENUM('Male', 'Female') NOT NULL,
    dob DATE NOT NULL,
    marital_status ENUM('Unmarried', 'Widower / Widow', 'Divorced') NOT NULL,
    children ENUM('No', 'Yes') DEFAULT NULL,
    mother_tongue ENUM('Malayalam', 'English', 'Hindi', 'Tamil', 'Kannada') NOT NULL DEFAULT 'Malayalam',
    about_me TEXT DEFAULT NULL,

    -- Religious & Physical Details
    religion VARCHAR(50) NOT NULL DEFAULT 'Hindu',
    caste ENUM('Ezhava', 'Thiyya') NOT NULL,
    physical_status ENUM('Normal', 'Physically Challenged') NOT NULL DEFAULT 'Normal',
    height SMALLINT UNSIGNED NOT NULL COMMENT 'Height in cm (137 to 213)',
    weight SMALLINT UNSIGNED DEFAULT NULL COMMENT 'Weight in kg (40 to 140)',
    body_type ENUM('Average', 'Athletic', 'Slim', 'Heavy') DEFAULT 'Average',
    complexion ENUM('Very Fair', 'Fair', 'Medium', 'Dark') NOT NULL,
    blood_group ENUM('A+ve', 'A-ve', 'B+ve', 'B-ve', 'AB+ve', 'AB-ve', 'O+ve', 'O-ve') DEFAULT NULL,
    diet ENUM('Vegetarian', 'Non Vegetarian', 'Eggatarian') DEFAULT 'Non Vegetarian',

    -- Education & Professional Details
    edu_category VARCHAR(100) DEFAULT NULL,
    edu_detail VARCHAR(100) NOT NULL,
    employed_in ENUM('Abroad', 'Agriculture', 'Business / Self Employed', 'Government', 'MNC', 'Networking', 'Own Firm', 'Public Ltd', 'Private', 'Professionals') DEFAULT NULL,
    occupation_category VARCHAR(100) DEFAULT NULL,
    occu_detail VARCHAR(100) DEFAULT NULL,
    working_firm VARCHAR(100) DEFAULT NULL,
    work_place VARCHAR(100) DEFAULT NULL,
    monthly_income VARCHAR(100) DEFAULT NULL,

    -- Location & Contact Details
    native_place VARCHAR(100) DEFAULT NULL,
    native_district ENUM('Alappuzha', 'Ernakulam', 'Idukki', 'Kannur', 'Kottayam', 'Kollam', 'Kozhikode', 'Kasaragod', 'Malappuram', 'Palakkad', 'Pathanamthitta', 'Thiruvananthapuram', 'Thrissur', 'Wayanad', 'Other') NOT NULL,
    citizenship VARCHAR(100) NOT NULL DEFAULT 'India',
    living_place VARCHAR(100) NOT NULL DEFAULT 'India',
    living_country VARCHAR(100) NOT NULL DEFAULT 'India',
    contact_name VARCHAR(100) NOT NULL,
    contact_relation ENUM('Father', 'Mother', 'Brother', 'Sister', 'Relative') NOT NULL,
    contact_phone VARCHAR(15) NOT NULL,
    alternate_phone VARCHAR(15) DEFAULT NULL,
    com_address TEXT DEFAULT NULL,

    INDEX idx_gender (gender),
    INDEX idx_caste (caste),
    INDEX idx_district (native_district),
    INDEX idx_marital_status (marital_status),
    INDEX idx_is_premium (is_premium),
    INDEX idx_dob (dob),
    INDEX idx_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE user_photos (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNSIGNED NOT NULL,
    file_path VARCHAR(255) NOT NULL,
    thumb_path VARCHAR(255) NOT NULL,
    is_main TINYINT(1) NOT NULL DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_user_photos_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_user_main (user_id, is_main)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE user_videos (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNSIGNED NOT NULL UNIQUE,
    file_path VARCHAR(255) NOT NULL,
    file_type VARCHAR(50) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_user_videos_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE partner_preferences (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNSIGNED NOT NULL UNIQUE,
    pref_age_from SMALLINT UNSIGNED DEFAULT 18,
    pref_age_to SMALLINT UNSIGNED DEFAULT 40,
    pref_height_from SMALLINT UNSIGNED DEFAULT 140,
    pref_height_to SMALLINT UNSIGNED DEFAULT 200,
    pref_marital_status TEXT DEFAULT NULL COMMENT 'Comma-separated or JSON list of acceptable marital statuses',
    pref_complexion VARCHAR(100) DEFAULT NULL,
    pref_body_type VARCHAR(100) DEFAULT NULL,
    pref_physical_status VARCHAR(50) DEFAULT 'Normal',
    pref_districts TEXT DEFAULT NULL COMMENT 'JSON or comma-separated list of districts',
    pref_living_place VARCHAR(100) DEFAULT NULL,
    pref_living_country VARCHAR(100) DEFAULT NULL,
    pref_edu_category VARCHAR(100) DEFAULT NULL,
    pref_employed_in VARCHAR(100) DEFAULT NULL,
    pref_occupation VARCHAR(100) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_partner_pref_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE success_stories (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    groom_name VARCHAR(100) NOT NULL,
    bride_name VARCHAR(100) NOT NULL,
    marriage_date DATE NOT NULL,
    story TEXT NOT NULL,
    image_path VARCHAR(255) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

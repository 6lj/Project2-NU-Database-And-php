CREATE DATABASE IF NOT EXISTS appointment_db;
USE appointment_db;

CREATE TABLE appointments (
    id VARCHAR(20) PRIMARY KEY,
    date DATE NOT NULL,
    time TIME NOT NULL,
    department VARCHAR(255) NOT NULL,
    status VARCHAR(20) NOT NULL
);

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(255) NOT NULL UNIQUE,
    phone VARCHAR(20) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    role ENUM('مريض', 'متدرب', 'مشرف') NOT NULL,
    password VARCHAR(255) NOT NULL,
    verification_code VARCHAR(20) DEFAULT NULL, 
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE trainees (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    phone VARCHAR(20) NOT NULL,
    appointment DATE NOT NULL,
    assigned_to VARCHAR(255),
    accepted TINYINT DEFAULT 0,
    patient_name VARCHAR(255) NOT NULL
);

CREATE TABLE IF NOT EXISTS help_messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    message TEXT,
    date DATE
);
CREATE TABLE appointmentdevkit (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    phone VARCHAR(20) NOT NULL,
    appointment_id VARCHAR(50) NOT NULL,
    date DATE NOT NULL,
    time TIME NOT NULL,
    department VARCHAR(100) NOT NULL,
    status VARCHAR(50) NOT NULL,
    UNIQUE KEY unique_appointment (appointment_id, date, time)  
);

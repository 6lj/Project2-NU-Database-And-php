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
    email VARCHAR(255) NOT NULL,
    username VARCHAR(255) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    phone VARCHAR(20)
);

CREATE TABLE IF NOT EXISTS trainees (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255),
    phone VARCHAR(255),
    appointment DATE,
    trainee VARCHAR(255)
);

CREATE TABLE IF NOT EXISTS help_messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    message TEXT,
    date DATE
);

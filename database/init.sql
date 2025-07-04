CREATE DATABASE IF NOT EXISTS weather_chatbot CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE weather_chatbot;

CREATE TABLE IF NOT EXISTS conversations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_name VARCHAR(100) NOT NULL, 
    user_message TEXT NOT NULL,
    ai_response TEXT NULL,
    api_response TEXT  NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE USER IF NOT EXISTS 'chatbot_user'@'%' IDENTIFIED BY 'root';
GRANT ALL PRIVILEGES ON weather_chatbot.* TO 'chatbot_user'@'%';
FLUSH PRIVILEGES;
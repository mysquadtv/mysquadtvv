-- Спочатку створіть базу даних, якщо вона не існує
CREATE DATABASE IF NOT EXISTS video_platform;

-- Використовуйте цю базу даних
USE video_platform;

-- Створення таблиці `comments` для зберігання коментарів до відео
CREATE TABLE IF NOT EXISTS comments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    video_id INT NOT NULL,
    user_id INT NOT NULL,
    comment TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (video_id) REFERENCES videos(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

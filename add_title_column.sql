-- Файл для додавання стовпця title до таблиці videos

ALTER TABLE videos ADD COLUMN title VARCHAR(255) NOT NULL;

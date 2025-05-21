-- Tạo cơ sở dữ liệu MeandYou và các bảng cần thiết cho ứng dụng
CREATE DATABASE IF NOT EXISTS MeandYou;
USE MeandYou;

ALTER DATABASE MeandYou CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Xóa các bảng cũ nếu tồn tại (lưu ý thứ tự xóa để tránh lỗi khóa ngoại)
DROP TABLE IF EXISTS playlist_music;
DROP TABLE IF EXISTS playlists;
DROP TABLE IF EXISTS login_attempts;
DROP TABLE IF EXISTS user_tokens;
DROP TABLE IF EXISTS password_resets;
DROP TABLE IF EXISTS user_activity_log;
DROP TABLE IF EXISTS songs;
DROP TABLE IF EXISTS users;

-- Tạo bảng người dùng
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    telephone VARCHAR(15) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    first_name VARCHAR(50),
    last_name VARCHAR(50),
    profile_picture VARCHAR(255) DEFAULT 'default_profile.jpg',
    bio TEXT,
    role ENUM('user', 'admin', 'manager') DEFAULT 'user',
    status ENUM('active', 'inactive', 'banned', 'pending') DEFAULT 'active',
    last_login DATETIME,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Create tables for authentication system
CREATE TABLE login_attempts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(100) NOT NULL,
    ip_address VARCHAR(45) NOT NULL,
    attempts INT DEFAULT 1,
    last_attempt TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX (email)
);

CREATE TABLE user_tokens (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    email VARCHAR(100) NOT NULL,
    token VARCHAR(255) NOT NULL,  -- Increased token length
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    expires TIMESTAMP NOT NULL,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    UNIQUE KEY (email, token)
);

CREATE TABLE password_resets (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    email VARCHAR(100) NOT NULL,
    token VARCHAR(255) NOT NULL,  -- Increased token length
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    expires TIMESTAMP NOT NULL,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    UNIQUE KEY (email, token)
);

CREATE TABLE user_activity_log (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    action VARCHAR(50) NOT NULL,
    description TEXT,
    ip_address VARCHAR(45),
    user_agent TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Create the music table
CREATE TABLE songs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    artist VARCHAR(255) NOT NULL,
    album VARCHAR(255),
    genre VARCHAR(50),
    duration INT COMMENT 'Duration in seconds',
    audio_path VARCHAR(255) NOT NULL,  -- Added NOT NULL
    image_path VARCHAR(255) NOT NULL,  -- Added NOT NULL
    play_count INT DEFAULT 0,
    release_date DATE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX (artist),
    INDEX (genre)
);

ALTER TABLE songs CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Create the playlists table
CREATE TABLE playlists (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    cover_image VARCHAR(255),
    is_public BOOLEAN DEFAULT TRUE,
    play_count INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Create the playlist_music table (many-to-many relationship)
CREATE TABLE playlist_music (
    id INT AUTO_INCREMENT PRIMARY KEY,
    playlist_id INT NOT NULL,
    music_id INT NOT NULL,
    position INT COMMENT 'Track order in playlist',
    added_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (playlist_id) REFERENCES playlists(id) ON DELETE CASCADE,
    FOREIGN KEY (music_id) REFERENCES songs(id) ON DELETE CASCADE,
    UNIQUE KEY (playlist_id, music_id)
);

INSERT INTO users (username, email, telephone, password, first_name, last_name, role, status) VALUES
-- Default admin and test user with plain password
('admin', 'admin@meandyou.com','0123456789', 'admin', 'Admin', 'User', 'admin', 'active'),
('testuser', 'test@example.com','0123456789', 'test', 'Test', 'User', 'user', 'active'),
('user1', 'user1@example.com','0123456789', '12345', 'John', 'Doe', 'user', 'active'),
('user2', 'user2@example.com','0123456789', '12345', 'Jane', 'Smith', 'user', 'active'),
('user3', 'user3@example.com','0123456789','12345', 'Robert', 'Johnson', 'user', 'active'),
('user4', 'user4@example.com','0123456789', '12345', 'Emily', 'Williams', 'user', 'active'),
('user5', 'user5@example.com','0123456789', '12345', 'Michael', 'Brown', 'user', 'active');

INSERT INTO songs (title, artist, audio_path, image_path) VALUES
('Chúng Ta Của Hiện Tại', 'Sơn Tùng MTP', 'audio/nhacTre/sontungmtp/ChungTaCuaHienTai-SonTungMTP-6892340.mp3', 'images/sontungmtp/chungtacuhientai-sontungmtp.jfif'),
('Âm Thầm Bên Em', 'Sơn Tùng MTP', 'audio/nhacTre/sontungmtp/AmThamBenEm-SonTungMTP-4066476.mp3', 'images/sontungmtp/amthambenem-sontungmtp.jfif'),
('Chạy Ngay Đi', 'Sơn Tùng MTP', 'audio/nhacTre/sontungmtp/ChayNgayDi-SonTungMTP-5468704.mp3', 'images/sontungmtp/chayngaydi-sontungmtp.jpg'),
('Hãy Trao Cho Anh', 'Sơn Tùng MTP', 'audio/nhacTre/sontungmtp/HayTraoChoAnh-SonTungMTPSnoopDogg-6010660.mp3', 'images/sontungmtp/haytraochoanh-sontungmtp.jpg'),
('Đừng Làm Trái Tim Anh Đau', 'Sơn Tùng MTP', 'audio/nhacTre/sontungmtp/DungLamTraiTimAnhDau-SonTungMTP.mp3', 'images/sontungmtp/dunglamtraitimanhdau-sontungmtp.jpg'),
('Em Của Ngày Hôm Qua', 'Sơn Tùng MTP', 'audio/nhacTre/sontungmtp/emcuangayhomqua-sontungmtp.mp3', 'images/sontungmtp/emcuangayhomqua-sontungmtp.jpg'),
('Nơi Này Có Anh', 'Sơn Tùng MTP', 'audio/nhacTre/sontungmtp/noinaycoanh-sontungmtp.mp3', 'images/sontungmtp/noinaycoanh-sontungmtp.jfif')
;


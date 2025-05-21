-- Tạo Database và sử dụng
CREATE DATABASE IF NOT EXISTS MeandYou;
USE MeandYou;

-- Xóa các bảng cũ (nếu có)
DROP TABLE IF EXISTS playlist_music;
DROP TABLE IF EXISTS playlists;
DROP TABLE IF EXISTS music;
DROP TABLE IF EXISTS users;

-- Tạo bảng người dùng (dưới 10 user)
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,   -- Tên đăng nhập
    email VARCHAR(100) NOT NULL UNIQUE,       -- Email
    password VARCHAR(255) NOT NULL,           -- Mật khẩu (nên mã hóa)
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tạo bảng lưu thông tin bài hát (dưới 50 bài)
CREATE TABLE music (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,              -- Tên bài hát
    artist VARCHAR(255) NOT NULL,             -- Nghệ sĩ
    album VARCHAR(255),                       -- Album (nếu có)
    genre VARCHAR(50),                        -- Thể loại
    duration INT DEFAULT 0,                   -- Thời lượng (giây)
    release_date DATE,                        -- Ngày phát hành
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tạo bảng playlist
CREATE TABLE playlists (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,                     -- Người tạo playlist
    name VARCHAR(255) NOT NULL,               -- Tên playlist
    description TEXT,                         -- Mô tả (nếu có)
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Tạo bảng liên kết bài hát trong playlist
CREATE TABLE playlist_music (
    id INT AUTO_INCREMENT PRIMARY KEY,
    playlist_id INT NOT NULL,
    music_id INT NOT NULL,
    position INT DEFAULT 1,                   -- Thứ tự bài hát trong playlist
    added_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (playlist_id) REFERENCES playlists(id) ON DELETE CASCADE,
    FOREIGN KEY (music_id) REFERENCES music(id) ON DELETE CASCADE,
    UNIQUE KEY (playlist_id, music_id)
);

-- Chèn dữ liệu mẫu cho bảng users (5 user)
INSERT INTO users (username, email, password) VALUES
('admin', 'admin@example.com', 'admin'),
('nguyenvan', 'nguyenvan@example.com', '12345'),
('tranthuy', 'tranthuy@example.com', '12345'),
('lelinh', 'lelinh@example.com', '12345'),
('hoangminh', 'hoangminh@example.com', '12345');

-- Chèn dữ liệu mẫu cho bảng music (10 bài hát)
INSERT INTO music (title, artist, album, genre, duration, release_date) VALUES
('Shape of You', 'Ed Sheeran', '÷', 'Pop', 233, '2017-01-06'),
('Blinding Lights', 'The Weeknd', 'After Hours', 'R&B', 200, '2019-11-29'),
('Dance Monkey', 'Tones and I', 'The Kids Are Coming', 'Pop', 210, '2019-05-10'),
('Rockstar', 'Post Malone ft. 21 Savage', 'Beerbongs & Bentleys', 'Hip-Hop', 218, '2017-09-15'),
('Someone You Loved', 'Lewis Capaldi', 'Divinely Uninspired to a Hellish Extent', 'Pop', 182, '2018-11-08'),
('Bohemian Rhapsody', 'Queen', 'A Night at the Opera', 'Rock', 354, '1975-10-31'),
('Hotel California', 'Eagles', 'Hotel California', 'Rock', 391, '1976-12-08'),
('Sweet Child O'' Mine', 'Guns N'' Roses', 'Appetite for Destruction', 'Rock', 356, '1987-08-17'),
('Imagine', 'John Lennon', 'Imagine', 'Rock', 183, '1971-10-11'),
('Billie Jean', 'Michael Jackson', 'Thriller', 'Pop', 293, '1983-01-02');

-- Chèn dữ liệu mẫu cho bảng playlists (mỗi user tạo 1 playlist)
INSERT INTO playlists (user_id, name, description) VALUES
(1, 'Admin Favorites', 'Những bài hát yêu thích của admin'),
(2, 'Nguyễn Văn Playlist', 'Bộ sưu tập cá nhân của Nguyễn Văn'),
(3, 'Trần Thúy Mix', 'Các bài hát hay của Trần Thúy'),
(4, 'Lê Linh Collection', 'Playlist của Lê Linh'),
(5, 'Hoàng Minh Hits', 'Các ca khúc hit của Hoàng Minh');

-- Thêm vài bài hát vào playlist mẫu
INSERT INTO playlist_music (playlist_id, music_id, position) VALUES
(1, 1, 1),
(1, 2, 2),
(1, 3, 3),
(2, 4, 1),
(2, 5, 2),
(3, 6, 1),
(3, 7, 2),
(4, 8, 1),
(4, 9, 2),
(5, 10, 1);
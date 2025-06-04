-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 04, 2025 at 08:48 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
DROP DATABASE IF EXISTS `meandyou`;
--
-- Database: `meandyou`
--
CREATE DATABASE IF NOT EXISTS `meandyou` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
-- --------------------------------------------------------

--
-- Table structure for table `albums`
--

CREATE TABLE `albums` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `artist` varchar(255) NOT NULL,
  `release_date` date NOT NULL,
  `cover_image` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `albums`
--

INSERT INTO `albums` (`id`, `title`, `artist`, `release_date`, `cover_image`, `created_at`) VALUES
(1, 'Chúng Ta Của Hiện Tại', 'Sơn Tùng M-TP', '2018-11-16', 'images/sontungmtp/chungtacuhientai-sontungmtp.jpg', '2025-06-04 04:31:26'),
(2, 'Chạy Ngay Đi', 'Sơn Tùng M-TP', '2018-10-25', 'images/sontungmtp/chayngaydi-sontungmtp.jpg', '2025-06-04 04:31:26'),
(3, 'Âm Thầm Bên Em', 'Sơn Tùng M-TP', '2017-11-01', 'images/sontungmtp/amthambenem-sontungmtp.jpg', '2025-06-04 04:31:26'),
(4, 'Đừng Làm Trái Tim Anh Đau', 'Sơn Tùng M-TP', '2019-01-01', 'images/sontungmtp/dunglamtraitimanhdau-sontungmtp.jpg', '2025-06-04 04:31:26'),
(5, 'Hãy Trao Cho Anh', 'Sơn Tùng M-TP', '2019-07-01', 'images/sontungmtp/haytraochoanh-sontungmtp.jpg', '2025-06-04 04:31:26'),
(6, 'Em Của Ngày Hôm Qua', 'Sơn Tùng M-TP', '2014-06-01', 'images/sontungmtp/emcuangayhomqua-sontungmtp.jpg', '2025-06-04 04:31:26'),
(7, 'Nơi Này Có Anh', 'Sơn Tùng M-TP', '2016-02-14', 'images/sontungmtp/noinaycoanh-sontungmtp.jpg', '2025-06-04 04:31:26'),
(8, 'Có Chắc Yêu Là Đây', 'Sơn Tùng M-TP', '2020-05-01', 'images/sontungmtp/CoChacYeuLaDay.jpg', '2025-06-04 04:31:26'),
(9, 'Muộn Rồi Mà Sao Còn', 'Sơn Tùng M-TP', '2021-12-31', 'images/sontungmtp/MuonRoiMaSaoCon.jpg', '2025-06-04 04:31:26'),
(10, 'Khuôn Mặt Đáng Thương', 'Sơn Tùng M-TP', '2022-01-01', 'images/sontungmtp/KhuonMatDangThuong.jpg', '2025-06-04 04:31:26'),
(11, 'Theres No One At All ', 'Sơn Tùng M-TP', '2021-09-01', 'images/sontungmtp/NoOneAtAll.jpg', '2025-06-04 04:31:26'),
(12, 'Chăm hoa', 'MONO', '2022-10-28', 'images/Mono/chamhoa-mono.jpg', '2025-06-04 04:31:26'),
(13, 'Ôm em thật lâu', 'MONO', '2022-10-28', 'images/Mono/omemthatlau-mono.jpg', '2025-06-04 04:31:26'),
(14, 'Đi tìm tình yêu', 'MONO', '2022-10-28', 'images/Mono/ditimtinhyeu-mono.jpg', '2025-06-04 04:31:26'),
(15, 'Waiting for you', 'MONO', '2022-10-28', 'images/Mono/waitingforyou-mono.jpg', '2025-06-04 04:31:26'),
(16, 'Em xinh', 'MONO', '2022-10-28', 'images/Mono/emxinh-mono.jpg', '2025-06-04 04:31:26'),
(17, 'Love Is', 'Dangrangto', '2023-01-01', 'images/Dangrangto/LoveIs-Dangrangto.jpg', '2025-06-04 04:31:26'),
(18, 'Lướt trên con sóng', 'Dangrangto', '2023-01-01', 'images/Dangrangto/LuotTrenConSong-Dangrangto.jpg', '2025-06-04 04:31:26'),
(19, 'Môi em', 'Dangrangto', '2023-01-01', 'images/Dangrangto/MoiEm-Dangrangto.jpg', '2025-06-04 04:31:26'),
(20, 'Ngựa Ô', 'Dangrangto', '2023-01-01', 'images/Dangrangto/NguaO-Dangrangto.jpg', '2025-06-04 04:31:26'),
(21, 'Wrong Times', 'Dangrangto', '2023-01-01', 'images/Dangrangto/WrongTimes-Dangrangto.jpg', '2025-06-04 04:31:26'),
(22, 'Anh Nhớ Ra', 'Vũ', '2020-05-15', 'images/Vu/Anhnhora-Vu.jpg', '2025-06-04 04:31:26'),
(23, 'Đông Kiếm Em', 'Vũ', '2020-05-15', 'images/Vu/DongKiemEm-Vu.jpg', '2025-06-04 04:31:26'),
(24, 'Những Lời Hứa Bỏ Quên', 'Vũ', '2020-05-15', 'images/Vu/Nhungloihuaboquen-Vu.jpg', '2025-06-04 04:31:26'),
(25, 'Phút Ban Đầu', 'Vũ', '2020-05-15', 'images/Vu/PhutBanDau-Vu.jpg', '2025-06-04 04:31:26'),
(26, 'Ai Mà Biết Được', 'SooBin Hoàng Sơn', '2018-04-20', 'images/SooBin/AiMaBietDuoc-SooBin.jpg', '2025-06-04 04:31:26'),
(27, 'Anh Đã Quen Với Cô Đơn', 'SooBin Hoàng Sơn', '2018-04-20', 'images/SooBin/AnhDaQuenVoiCoDon-SooBin.jpg', '2025-06-04 04:31:26'),
(28, 'Dancing In The Dark', 'SooBin Hoàng Sơn', '2018-04-20', 'images/SooBin/DancingInTheDark-SooBin.jpg', '2025-06-04 04:31:26'),
(29, 'Đẹp Nhất Là Em', 'SooBin Hoàng Sơn', '2018-04-20', 'images/SooBin/DepNhatLaEm-SooBin.jpg', '2025-06-04 04:31:26');

-- --------------------------------------------------------

--
-- Table structure for table `artists`
--

CREATE TABLE `artists` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `image_path` varchar(255) DEFAULT NULL,
  `profile_url` varchar(255) DEFAULT NULL,
  `bio` text DEFAULT NULL,
  `background_image` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `artists`
--

INSERT INTO `artists` (`id`, `name`, `image_path`, `profile_url`, `bio`, `background_image`) VALUES
(1, 'Sơn Tùng MTP', 'images/sontungmtp/sontung.jpg', 'artist.php?name=Sơn Tùng MTP', 'Nguyễn Thanh Tùng (sinh ngày 5 tháng 7 năm 1994), thường được biết đến với nghệ danh Sơn Tùng M-TP, là một nam ca sĩ kiêm nhạc sĩ sáng tác bài hát, nhà sản xuất thu âm, rapper và diễn viên người Việt Nam.', 'images/sontungmtp/nensontung.jpg'),
(2, 'SooBin Hoàng Sơn', 'images/SooBin/soobinavata2.jpg', 'artist.php?name=SooBin Hoàng Sơn', 'Nguyễn Huỳnh Sơn (sinh ngày 10 tháng 9 năm 1992), thường được biết đến với nghệ danh Soobin hay với tên cũ Soobin Hoàng Sơn (viết cách điệu là SOOBIN), là một nam ca sĩ kiêm nhạc sĩ sáng tác ca khúc người Việt Nam. Anh dành phần lớn sự nghiệp hoạt động của mình gắn bó với tư cách là thành viên của SpaceSpeakers.', 'images/SooBin/anhsoo.jpeg'),
(3, 'Dangrangto', 'images/Dangrangto/dangrangto.jpeg', 'artist.php?name=Dangrangto', 'Trần Hải Đăng, là một rapper đa dạng phong cách đến từ tổ đội WORKAHOLICS và SIMPLEGUYS. Một số nghệ danh khác mọi người vẫn hay gọi: DRT và Trần Lá Lướt.', 'images/Dangrangto/anhdang.jpeg'),
(4, 'Vũ', 'images/Vu/vu.jpg', 'artist.php?name=Vũ', 'Hoàng Thái Vũ (sinh ngày 3 tháng 10 năm 1995), thường được biết đến với nghệ danh Vũ (cách điệu là Vũ.), là một nam ca sĩ kiêm nhạc sĩ sáng tác ca khúc người Việt Nam.', 'images/Vu/Vu123.jpeg'),
(5, 'MONO', 'images/Mono/monoa.jpg', 'artist.php?name=MONO', 'MONO (Nguyen Viet Hoang), who was born in 21/01/2000, is an artist representing dynamic but profound young people. MONO constantly explores new life experiences or perspectives by immersing in these...', 'images/Mono/mono-bg.jpg'),
(6, 'J97', 'images/J97/anhjack.jpg', 'artist.php?name=J97', 'Trịnh Trần Phương Tuấn (sinh ngày 12 tháng 4 năm 1997), thường được biết đến với nghệ danh Jack hoặc Jack – J97, là một nam ca sĩ kiêm sáng tác nhạc, rapper và diễn viên người Việt Nam. Anh bắt đầu được biết đến khi hoạt động trong nhóm nhạc G5R và phát hành bài hát \"Hồng nhan\".', 'images/J97/j97_3.jpg'),
(7, 'Hoà Minzy', 'images/HoaMinzy/hoaminzy.jpg', 'artist-hoaminzy', NULL, NULL),
(8, 'BinZ', 'images/BinZ/BinZ.jpg', 'artist-Binz', NULL, NULL),
(9, 'Erik', 'images/Erik/erik.jpg', 'artist-Erik', NULL, NULL),
(10, 'Đức Phúc', 'images/DucPhuc/ducphuc.jpg', 'artist-DucPhuc', NULL, NULL),
(11, 'Hieuthuhai', 'images/Hieuthuhai/hieuthuhai.jpg', 'artist-Hieuthuhai', NULL, NULL),
(12, 'Trúc Nhân', 'images/TrucNhan/trucnhan.jpg', 'artist-TrucNhan', NULL, 'images/TrucNhan/trucnhan.jpg'),
(13, 'AMEE', 'images/nghesi/amee.jpg', 'artist-Amee', NULL, NULL),
(14, 'Andiez', 'images/nghesi/andiez.jpg', 'artist-Andiez', NULL, NULL),
(15, 'buitruonglinh', 'images/nghesi/buitruonglinh.jpg', 'artist-Buitruonglinh', NULL, 'images/nghesi/buitruonglinh.jpg\n'),
(16, 'Đạt G', 'images/nghesi/datg.jpg', 'artist-DatG', NULL, 'images/nghesi/datg.jpg'),
(17, 'Đen', 'images/nghesi/denvau.jpg', 'artist-Den', NULL, 'images/nghesi/denvau.jpg'),
(18, 'Duong Domic', 'images/nghesi/duongdomic.jpg', 'artist-DuongDomic', NULL, NULL),
(19, 'Hoàng Thuỳ Linh', 'images/nghesi/hoangthuylinh.jpg', 'artist-HoangThuyLinh', NULL, NULL),
(20, 'Lou Hoàng', 'images/nghesi/louhoang.jpg', 'artist-LouHoang', NULL, NULL),
(21, 'Noo Phước Thịnh', 'images/nghesi/noophuocthinh.jpg', 'artist-NooPhuocThinh', NULL, NULL),
(22, 'Orange', 'images/nghesi/orange.jpg', 'artist-Orange', NULL, NULL),
(23, 'Phương Mỹ Chi', 'images/nghesi/phuongmychi.jpg', 'artist-PhuongMyChi', NULL, NULL),
(24, 'tlinh', 'images/nghesi/tlinh.jpg', 'artist-tlinh', NULL, NULL),
(25, 'Vũ Cát Tường', 'images/nghesi/vucattuong.jpg', 'artist-VuCatTuong', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `genres_discover`
--

CREATE TABLE `genres_discover` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `image_path` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `genres_discover`
--

INSERT INTO `genres_discover` (`id`, `name`, `image_path`, `created_at`) VALUES
(1, 'Trending 2018', 'images/anh1.jpg', '2025-06-04 04:31:26'),
(2, 'Rock Tracks', 'images/anh2.avif', '2025-06-04 04:31:26'),
(3, 'Jack Tracks', 'images/anh3.avif', '2025-06-04 04:31:26'),
(4, 'Phong Tracks', 'images/anh4.webp', '2025-06-04 04:31:26');

-- --------------------------------------------------------

--
-- Table structure for table `listening_history`
--

CREATE TABLE `listening_history` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `song_id` int(11) NOT NULL,
  `listened_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `listening_history`
--

INSERT INTO `listening_history` (`id`, `user_id`, `song_id`, `listened_at`) VALUES
(1, 3, 1, '2025-06-03 21:31:26'),
(2, 3, 2, '2025-06-03 21:31:26'),
(3, 3, 3, '2025-06-03 21:31:26'),
(4, 3, 4, '2025-06-03 21:31:26'),
(5, 3, 5, '2025-06-03 21:31:26'),
(6, 3, 3, '2025-06-04 17:19:54'),
(7, 3, 3, '2025-06-04 17:38:18'),
(8, 3, 4, '2025-06-04 17:38:19'),
(9, 3, 1, '2025-06-04 17:38:20'),
(10, 3, 1, '2025-06-04 17:38:42'),
(11, 3, 3, '2025-06-04 17:38:46'),
(12, 3, 2, '2025-06-04 17:39:57'),
(13, 3, 34, '2025-06-04 17:41:00'),
(14, 3, 33, '2025-06-04 17:41:57'),
(15, 3, 1, '2025-06-04 17:44:07'),
(16, 3, 25, '2025-06-04 17:44:20'),
(17, 3, 1, '2025-06-04 17:46:53'),
(18, 3, 1, '2025-06-04 17:47:03'),
(19, 3, 1, '2025-06-04 17:47:05'),
(20, 3, 1, '2025-06-04 17:47:25'),
(21, 3, 1, '2025-06-04 17:47:30'),
(22, 3, 1, '2025-06-04 17:48:08'),
(23, 3, 1, '2025-06-04 17:48:12'),
(24, 3, 1, '2025-06-04 17:48:35'),
(25, 3, 1, '2025-06-04 17:49:30'),
(26, 3, 1, '2025-06-04 17:51:43'),
(27, 3, 1, '2025-06-04 17:52:01'),
(28, 3, 1, '2025-06-04 17:52:10'),
(29, 3, 1, '2025-06-04 17:53:34'),
(30, 3, 1, '2025-06-04 17:55:26'),
(31, 3, 47, '2025-06-04 17:55:50'),
(32, 3, 1, '2025-06-04 17:57:56'),
(33, 3, 1, '2025-06-04 17:58:03'),
(34, 3, 3, '2025-06-04 17:58:10'),
(35, 3, 3, '2025-06-04 17:58:18'),
(36, 3, 3, '2025-06-04 17:58:29'),
(37, 3, 47, '2025-06-04 17:59:18'),
(38, 3, 1, '2025-06-04 18:00:47'),
(39, 3, 1, '2025-06-04 18:05:11'),
(40, 3, 1, '2025-06-04 18:05:20'),
(41, 3, 1, '2025-06-04 18:05:44'),
(42, 3, 1, '2025-06-04 18:07:26'),
(43, 3, 3, '2025-06-04 18:07:45'),
(44, 3, 4, '2025-06-04 18:07:49'),
(45, 3, 1, '2025-06-04 18:08:30'),
(46, 3, 4, '2025-06-04 18:08:42');

--
-- Triggers `listening_history`
--
DELIMITER $$
CREATE TRIGGER `after_history_insert` AFTER INSERT ON `listening_history` FOR EACH ROW BEGIN
    UPDATE `songs` 
    SET `play_count` = COALESCE(`play_count`, 0) + 1,
        `last_played` = NEW.`listened_at`
    WHERE `id` = NEW.`song_id`;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `login_attempts`
--

CREATE TABLE `login_attempts` (
  `id` int(11) NOT NULL,
  `email` varchar(100) NOT NULL,
  `ip_address` varchar(45) NOT NULL,
  `attempts` int(11) DEFAULT 1,
  `last_attempt` timestamp NOT NULL DEFAULT current_timestamp(),
  `blocked_until` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `password_resets`
--

CREATE TABLE `password_resets` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `email` varchar(100) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `expires` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `playlists`
--

CREATE TABLE `playlists` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `cover_image` varchar(255) DEFAULT NULL,
  `is_public` tinyint(1) DEFAULT 1,
  `play_count` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `playlist_music`
--

CREATE TABLE `playlist_music` (
  `id` int(11) NOT NULL,
  `playlist_id` int(11) NOT NULL,
  `music_id` int(11) NOT NULL,
  `position` int(11) DEFAULT NULL COMMENT 'Track order in playlist',
  `added_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(128) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` text DEFAULT NULL,
  `last_activity` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `songs`
--

CREATE TABLE `songs` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `artist` varchar(255) NOT NULL,
  `album` varchar(255) DEFAULT NULL,
  `genre` varchar(50) DEFAULT NULL,
  `duration` int(11) DEFAULT NULL COMMENT 'Duration in seconds',
  `audio_path` varchar(255) NOT NULL,
  `image_path` varchar(255) NOT NULL,
  `play_count` int(11) DEFAULT 0,
  `last_played` timestamp NULL DEFAULT NULL,
  `release_date` date DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `songs`
--

INSERT INTO `songs` (`id`, `title`, `artist`, `album`, `genre`, `duration`, `audio_path`, `image_path`, `play_count`, `last_played`, `release_date`, `created_at`, `updated_at`) VALUES
(1, 'Chúng Ta Của Hiện Tại', 'Sơn Tùng MTP', NULL, 'Trending 2018', NULL, 'audio/nhacTre/sontungmtp/ChungTaCuaHienTai-SonTungMTP-6892340.mp3', 'images/sontungmtp/chungtacuhientai-sontungmtp.jpg', 56, '2025-06-04 18:36:49', NULL, '2025-06-04 04:31:26', '2025-06-04 18:36:49'),
(2, 'Âm Thầm Bên Em', 'Sơn Tùng MTP', NULL, NULL, NULL, 'audio/nhacTre/sontungmtp/AmThamBenEm-SonTungMTP-4066476.mp3', 'images/sontungmtp/amthambenem-sontungmtp.jpg', 3, '2025-06-04 17:39:57', NULL, '2025-06-04 04:31:26', '2025-06-04 17:39:57'),
(3, 'Chạy Ngay Đi', 'Sơn Tùng MTP', NULL, NULL, NULL, 'audio/nhacTre/sontungmtp/ChayNgayDi-SonTungMTP-5468704.mp3', 'images/sontungmtp/chayngaydi-sontungmtp.jpg', 16, '2025-06-04 18:07:45', NULL, '2025-06-04 04:31:26', '2025-06-04 18:07:45'),
(4, 'Hãy Trao Cho Anh', 'Sơn Tùng MTP', NULL, NULL, NULL, 'audio/nhacTre/sontungmtp/HayTraoChoAnh-SonTungMTPSnoopDogg-6010660.mp3', 'images/sontungmtp/haytraochoanh-sontungmtp.jpg', 8, '2025-06-04 18:08:42', NULL, '2025-06-04 04:31:26', '2025-06-04 18:08:42'),
(5, 'Đừng Làm Trái Tim Anh Đau', 'Sơn Tùng MTP', NULL, NULL, NULL, 'audio/nhacTre/sontungmtp/DungLamTraiTimAnhDau-SonTungMTP.mp3', 'images/sontungmtp/dunglamtraitimanhdau-sontungmtp.jpg', 1, '2025-06-03 21:31:26', NULL, '2025-06-04 04:31:26', '2025-06-04 16:31:10'),
(6, 'Em Của Ngày Hôm Qua', 'Sơn Tùng MTP', NULL, NULL, NULL, 'audio/nhacTre/sontungmtp/emcuangayhomqua-sontungmtp.mp3', 'images/sontungmtp/emcuangayhomqua-sontungmtp.jpg', 0, NULL, NULL, '2025-06-04 04:31:26', '2025-06-04 04:31:26'),
(7, 'Nơi Này Có Anh', 'Sơn Tùng MTP', NULL, NULL, NULL, 'audio/nhacTre/sontungmtp/noinaycoanh-sontungmtp.mp3', 'images/sontungmtp/noinaycoanh-sontungmtp.jpg', 0, NULL, NULL, '2025-06-04 04:31:26', '2025-06-04 04:31:26'),
(8, 'Có Chắc Yêu Là Đây', 'Sơn Tùng MTP', NULL, NULL, NULL, 'audio/nhacTre/sontungmtp/CoChacYeuLaDay.mp3', 'images/sontungmtp/CoChacYeuLaDay.jpg', 0, NULL, NULL, '2025-06-04 04:31:26', '2025-06-04 04:31:26'),
(9, 'Muộn Rồi Mà Sao Còn', 'Sơn Tùng MTP', NULL, NULL, NULL, 'audio/nhacTre/sontungmtp/MuonRoiMaSaoCon.mp3', 'images/sontungmtp/MuonRoiMaSaoCon.jpg', 0, NULL, NULL, '2025-06-04 04:31:26', '2025-06-04 04:31:26'),
(10, 'Chúng Ta Của Tương Lai', 'Sơn Tùng MTP', NULL, NULL, NULL, 'audio/nhacTre/sontungmtp/ChungTaCuaTuongLai.mp3', 'images/sontungmtp/ChungTaCuaTuongLai.jpg', 0, NULL, NULL, '2025-06-04 04:31:26', '2025-06-04 04:31:26'),
(11, 'Khuôn Mặt Đáng Thương', 'Sơn Tùng MTP', NULL, NULL, NULL, 'audio/nhacTre/sontungmtp/KhuonMatDangThuong.mp3', 'images/sontungmtp/KhuonMatDangThuong.jpg', 0, NULL, NULL, '2025-06-04 04:31:26', '2025-06-04 04:31:26'),
(12, 'Theres No One At All ', 'Sơn Tùng MTP', NULL, NULL, NULL, 'audio/nhacTre/sontungmtp/NoOneAtAll.mp3', 'images/sontungmtp/NoOneAtAll.jpg', 0, NULL, NULL, '2025-06-04 04:31:26', '2025-06-04 04:31:26'),
(13, 'Chăm hoa', 'MONO', NULL, NULL, NULL, 'audio/nhacTre/MONO/ChamHoa-Mono.mp3', 'images/Mono/chamhoa-mono.jpg', 0, NULL, NULL, '2025-06-04 04:31:26', '2025-06-04 04:31:26'),
(14, 'Ôm em thật lâu', 'MONO', NULL, NULL, NULL, 'audio/nhacTre/MONO/OmEmThatLau-Mono.mp3', 'images/Mono/omemthatlau-mono.jpg', 0, NULL, NULL, '2025-06-04 04:31:26', '2025-06-04 04:31:26'),
(15, 'Đi tìm tình yêu', 'MONO', NULL, NULL, NULL, 'audio/nhacTre/MONO/DiTimTinhYeu-Mono.mp3', 'images/Mono/ditimtinhyeu-mono.jpg', 0, NULL, NULL, '2025-06-04 04:31:26', '2025-06-04 04:31:26'),
(16, 'Waiting for you', 'MONO', NULL, NULL, NULL, 'audio/nhacTre/MONO/WaitingForYou-Mono.mp3', 'images/Mono/waitingforyou-mono.jpg', 0, NULL, NULL, '2025-06-04 04:31:26', '2025-06-04 04:31:26'),
(17, 'Em xinh', 'MONO', NULL, NULL, NULL, 'audio/nhacTre/MONO/Emxinh-Mono.mp3', 'images/Mono/emxinh-mono.jpg', 0, NULL, NULL, '2025-06-04 04:31:26', '2025-06-04 04:31:26'),
(18, 'Love Is', 'Dangrangto', NULL, NULL, NULL, 'audio/nhacTre/Dangrangto/LoveIs-Dangrangto.mp3', 'images/Dangrangto/LoveIs-Dangrangto.jpg', 0, NULL, NULL, '2025-06-04 04:31:26', '2025-06-04 04:31:26'),
(19, 'Lướt trên con sóng', 'Dangrangto', NULL, NULL, NULL, 'audio/nhacTre/Dangrangto/LuotTrenConSong-Dangrangto.mp3', 'images/Dangrangto/LuotTrenConSong-Dangrangto.jpg', 0, NULL, NULL, '2025-06-04 04:31:26', '2025-06-04 04:31:26'),
(20, 'Môi em', 'Dangrangto', NULL, NULL, NULL, 'audio/nhacTre/Dangrangto/MoiEm-Dangrangto.mp3', 'images/Dangrangto/MoiEm-Dangrangto.jpg', 0, NULL, NULL, '2025-06-04 04:31:26', '2025-06-04 04:31:26'),
(21, 'Ngựa Ô', 'Dangrangto', NULL, NULL, NULL, 'audio/nhacTre/Dangrangto/NguaO-Dangrangto.mp3', 'images/Dangrangto/NguaO-Dangrangto.jpg', 0, NULL, NULL, '2025-06-04 04:31:26', '2025-06-04 04:31:26'),
(22, 'Wrong Times', 'Dangrangto', NULL, NULL, NULL, 'audio/nhacTre/Dangrangto/WrongTimes-Dangrangto.mp3', 'images/Dangrangto/WrongTimes-Dangrangto.jpg', 0, NULL, NULL, '2025-06-04 04:31:26', '2025-06-04 04:31:26'),
(23, 'Anh Nhớ Ra', 'Vũ', NULL, NULL, NULL, 'audio/nhacTre/Vu/AnhNhoRa-Vu.mp3', 'images/Vu/Anhnhora-Vu.jpg', 0, NULL, NULL, '2025-06-04 04:31:26', '2025-06-04 04:31:26'),
(24, 'Đông Kiếm Em', 'Vũ', NULL, NULL, NULL, 'audio/nhacTre/Vu/DongKiemEm-Vu.mp3', 'images/Vu/DongKiemEm-Vu.jpg', 0, NULL, NULL, '2025-06-04 04:31:26', '2025-06-04 04:31:26'),
(25, 'Những Lời Hứa Bỏ Quên', 'Vũ', NULL, NULL, NULL, 'audio/nhacTre/Vu/Nhungloihuaboquen-Vu.mp3', 'images/Vu/Nhungloihuaboquen-Vu.jpg', 2, '2025-06-04 17:44:20', NULL, '2025-06-04 04:31:26', '2025-06-04 17:44:20'),
(26, 'Phút Ban Đầu', 'Vũ', NULL, NULL, NULL, 'audio/nhacTre/Vu/PhutBanDau-Vu.mp3', 'images/Vu/PhutBanDau-Vu.jpg', 0, NULL, NULL, '2025-06-04 04:31:26', '2025-06-04 04:31:26'),
(27, 'Ai Mà Biết Được', 'SooBin Hoàng Sơn', NULL, NULL, NULL, 'audio/nhacTre/Soobin/AiMABietDuoc.mp3', 'images/SooBin/AiMaBietDuoc-SooBin.jpg', 0, NULL, NULL, '2025-06-04 04:31:26', '2025-06-04 04:31:26'),
(28, 'Anh Đã Quen Với Cô Đơn', 'SooBin Hoàng Sơn', NULL, NULL, NULL, 'audio/nhacTre/Soobin/AnhDaQuenVoiCoDon.mp3', 'images/SooBin/AnhDaQuenVoiCoDon-SooBin.jpg', 0, NULL, NULL, '2025-06-04 04:31:26', '2025-06-04 04:31:26'),
(29, 'Dancing In The Dark', 'SooBin Hoàng Sơn', NULL, NULL, NULL, 'audio/nhacTre/Soobin/DancingInTheDark.mp3', 'images/SooBin/DancingInTheDark-SooBin.jpg', 0, NULL, NULL, '2025-06-04 04:31:26', '2025-06-04 04:31:26'),
(30, 'Đẹp Nhất Là Em', 'SooBin Hoàng Sơn', NULL, NULL, NULL, 'audio/nhacTre/Soobin/DepNhatLaEm.mp3', 'images/SooBin/DepNhatLaEm-SooBin.jpg', 0, NULL, NULL, '2025-06-04 04:31:26', '2025-06-04 04:31:26'),
(31, 'Đi Để Trở Về', 'SooBin Hoàng Sơn', NULL, NULL, NULL, 'audio/nhacTre/Soobin/DiDeTroVe.mp3', 'images/SooBin/DiDeTroVe-SooBin.jpg', 0, NULL, NULL, '2025-06-04 04:31:26', '2025-06-04 04:31:26'),
(32, 'Đi Để Trở Về 3', 'SooBin Hoàng Sơn', NULL, NULL, NULL, 'audio/nhacTre/Soobin/DiDeTroVe3.mp3', 'images/SooBin/DiDeTroVe3-SooBin.jpg', 0, NULL, NULL, '2025-06-04 04:31:26', '2025-06-04 04:31:26'),
(33, 'Giá Như', 'SooBin Hoàng Sơn', NULL, NULL, NULL, 'audio/nhacTre/Soobin/GiaNhu.mp3', 'images/Soobin/GiaNhu-SooBin.jpg', 2, '2025-06-04 17:41:57', NULL, '2025-06-04 04:31:26', '2025-06-04 17:41:57'),
(34, 'Nếu Ngày Ấy', 'SooBin Hoàng Sơn', NULL, NULL, NULL, 'audio/nhacTre/Soobin/NeuNgayAy.mp3', 'images//SooBin/NeuNgayAy-SooBin.jpg', 2, '2025-06-04 17:41:00', NULL, '2025-06-04 04:31:26', '2025-06-04 17:41:00'),
(35, 'Phía Sau Một Cô Gái', 'SooBin Hoàng Sơn', NULL, NULL, NULL, 'audio/nhacTre/Soobin/PhiaSauMotCoGai.mp3', 'images/Soobin/PhiaSauMotCoGai-SooBin.jpg', 0, NULL, NULL, '2025-06-04 04:31:26', '2025-06-04 04:31:26'),
(36, 'Tháng Năm', 'SooBin Hoàng Sơn', NULL, NULL, NULL, 'audio/nhacTre/Soobin/ThangNam.mp3', 'images/SooBin/ThangNam-SooBin.jpg', 0, NULL, NULL, '2025-06-04 04:31:26', '2025-06-04 04:31:26'),
(37, 'ThePlayah', 'SooBin Hoàng Sơn', NULL, NULL, NULL, 'audio/nhacTre/Soobin/ThePlayah.mp3', 'images/SooBin/ThePlayah-SooBin.jpg', 0, NULL, NULL, '2025-06-04 04:31:26', '2025-06-04 04:31:26'),
(38, 'Và Thế Là Hết', 'SooBin Hoàng Sơn', NULL, NULL, NULL, 'audio/nhacTre/Soobin/VaTheLaHet.mp3', 'images/SooBin/VaTheLaHet-SooBin.jpg', 0, NULL, NULL, '2025-06-04 04:31:26', '2025-06-04 04:31:26'),
(39, '01 Ngoại Lệ', 'J97', NULL, NULL, NULL, 'audio/nhacTre/J97/01NgoaiLe-J97.mp3', 'images/J97/01NgoaiLe-J97.jpg', 0, NULL, NULL, '2025-06-04 04:31:26', '2025-06-04 04:31:26'),
(40, 'Hoa Hải Đường', 'J97', NULL, NULL, NULL, 'audio/nhacTre/J97/HoaHaiDuong-J97.mp3', 'images/J97/HoaHaiDuong-J97.jpg', 0, NULL, NULL, '2025-06-04 04:31:26', '2025-06-04 04:31:26'),
(41, 'Là Một Thằng Con Trai', 'J97', NULL, NULL, NULL, 'audio/nhacTre/J97/LaMotThangConTrai-J97.mp3', 'images/J97/LaMotThangConTrai-J97.jpg', 0, NULL, NULL, '2025-06-04 04:31:26', '2025-06-04 04:31:26'),
(42, 'Hoa Vô Sắc', 'J97', NULL, NULL, NULL, 'audio/nhacTre/J97/HoaVoSac-J97.mp3', 'images/J97/HoaVoSac-J97.jpg', 0, NULL, NULL, '2025-06-04 04:31:26', '2025-06-04 04:31:26'),
(43, 'LayLaLay', 'J97', NULL, NULL, NULL, 'audio/nhacTre/J97/LayLaLay-J97.mp3', 'images/J97/LayLaLay-J97.jpg', 0, NULL, NULL, '2025-06-04 04:31:26', '2025-06-04 04:31:26'),
(44, 'Giai Điệu Miền Tây', 'J97', NULL, NULL, NULL, 'audio/nhacTre/J97/GiaiDieuMienTay-J97.mp3', 'images/J97/GiaiDieuMienTay-J97.jpg', 0, NULL, NULL, '2025-06-04 04:31:26', '2025-06-04 04:31:26'),
(45, 'Chúng Ta Rồi Sẽ Hạnh Phúc', 'J97', NULL, NULL, NULL, 'audio/nhacTre/J97/ChungTaRoiSeHanhPhuc-J97.mp3', 'images/J97/ChungTaRoiSeHanhPhuc-J97.jpg', 0, NULL, NULL, '2025-06-04 04:31:26', '2025-06-04 04:31:26'),
(46, 'Thiên Lý Ơi', 'J97', NULL, NULL, NULL, 'audio/nhacTre/J97/ThienLyOi-J97.mp3', 'images/J97/ThienLyOi-J97.jpg', 0, NULL, NULL, '2025-06-04 04:31:26', '2025-06-04 04:31:26'),
(47, 'Bạc Phận', 'J97', NULL, NULL, NULL, 'audio/nhacTre/J97/BacPhan-J97.mp3', 'images/J97/BacPhan-J97.jpg', 4, '2025-06-04 17:59:18', NULL, '2025-06-04 04:31:26', '2025-06-04 17:59:18'),
(48, 'Hồng Nhan', 'J97', NULL, NULL, NULL, 'audio/nhacTre/J97/HongNhan-J97.mp3', 'images/J97/HongNhan-J97.jpg', 0, NULL, NULL, '2025-06-04 04:31:26', '2025-06-04 04:31:26'),
(49, 'Đom Đóm', 'J97', NULL, NULL, NULL, 'audio/nhacTre/J97/DomDom-J97.mp3', 'images/J97/DomDom-J97.jpg', 0, NULL, NULL, '2025-06-04 04:31:26', '2025-06-04 04:31:26'),
(50, 'Mẹ Ơi 2', 'J97', NULL, NULL, NULL, 'audio/nhacTre/J97/MeOi2-J97.mp3', 'images/J97/MeOi2-J97.jpg', 0, NULL, NULL, '2025-06-04 04:31:26', '2025-06-04 04:31:26'),
(51, 'Cao Ốc 20', 'ĐạtG', NULL, NULL, NULL, 'audio/nhacTre/CaoOc20-BRayDatGMasewKICM-6008352.mp3', 'images/nghesi/DatG.jpg', 0, NULL, NULL, '2025-06-04 04:31:26', '2025-06-04 04:31:26');
;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `telephone` varchar(15) NOT NULL,
  `password` varchar(255) NOT NULL,
  `first_name` varchar(50) DEFAULT NULL,
  `last_name` varchar(50) DEFAULT NULL,
  `profile_picture` varchar(255) DEFAULT 'default_profile.jpg',
  `bio` text DEFAULT NULL,
  `role` enum('user','admin','manager') DEFAULT 'user',
  `status` enum('active','inactive','banned','pending') DEFAULT 'active',
  `login_attempts` int(11) DEFAULT 0,
  `last_attempt` timestamp NULL DEFAULT NULL,
  `locked_until` timestamp NULL DEFAULT NULL,
  `remember_token` varchar(255) DEFAULT NULL,
  `remember_expires` timestamp NULL DEFAULT NULL,
  `last_login` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `telephone`, `password`, `first_name`, `last_name`, `profile_picture`, `bio`, `role`, `status`, `login_attempts`, `last_attempt`, `locked_until`, `remember_token`, `remember_expires`, `last_login`, `created_at`, `updated_at`) VALUES
(3, 'admin', 'admin@meandyou.com', '123123123123', '$2y$10$cokHbIGBPgkxELMdrDJy8usOlZB7ngRBupbHj18Qtif89FvH7BkfS', 'Công Thịnh', 'Tu', 'uploads/avatars/CongThinh.jpg', NULL, 'admin', 'active', 0, NULL, NULL, NULL, NULL, '2025-06-05 00:18:43', '2025-06-04 04:42:16', '2025-06-04 17:18:43');

-- --------------------------------------------------------

--
-- Table structure for table `user_tokens`
--

CREATE TABLE `user_tokens` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `email` varchar(100) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `expires` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `artists`
--
ALTER TABLE `artists`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `listening_history`
--
ALTER TABLE `listening_history`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_user_listened` (`user_id`,`listened_at`),
  ADD KEY `idx_song_listened` (`song_id`,`listened_at`);

--
-- Indexes for table `songs`
--
ALTER TABLE `songs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `artists`
--
ALTER TABLE `artists`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT for table `listening_history`
--
ALTER TABLE `listening_history`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=47;

--
-- AUTO_INCREMENT for table `songs`
--
ALTER TABLE `songs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=51;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `listening_history`
--
ALTER TABLE `listening_history`
  ADD CONSTRAINT `fk_listening_song` FOREIGN KEY (`song_id`) REFERENCES `songs` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_listening_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

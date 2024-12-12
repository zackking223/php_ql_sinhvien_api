-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Máy chủ: 127.0.0.1
-- Thời gian đã tạo: Th12 12, 2024 lúc 01:38 PM
-- Phiên bản máy phục vụ: 10.4.22-MariaDB
-- Phiên bản PHP: 7.4.27

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Cơ sở dữ liệu: `ql_sinhvien`
--

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `chamdiem`
--

CREATE TABLE `chamdiem` (
  `MaSinhVien` varchar(255) NOT NULL,
  `MaMonHoc` varchar(255) NOT NULL,
  `DiemChuyenCan` float NOT NULL DEFAULT 10,
  `DiemGiuaKy` float NOT NULL DEFAULT 10,
  `DiemThi` float NOT NULL DEFAULT 10,
  `TongKet` float NOT NULL DEFAULT 10
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Đang đổ dữ liệu cho bảng `chamdiem`
--

INSERT INTO `chamdiem` (`MaSinhVien`, `MaMonHoc`, `DiemChuyenCan`, `DiemGiuaKy`, `DiemThi`, `TongKet`) VALUES
('SV001', 'MH001', 10, 8, 9, 8.8),
('SV001', 'MH002', 10, 9, 9, 9.1),
('SV002', 'MH001', 9, 8, 7, 7.5),
('SV003', 'MH001', 10, 8, 8.5, 8.5),
('SV004', 'MH001', 0, 0, 0, 0),
('SV005', 'MH001', 0, 0, 0, 0),
('SV007', 'MH005', 0, 0, 0, 0),
('SV028', 'MH005', 0, 0, 0, 0);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `giangvien`
--

CREATE TABLE `giangvien` (
  `MaGiangVien` varchar(10) NOT NULL,
  `HoTen` varchar(100) NOT NULL,
  `Email` varchar(100) DEFAULT NULL,
  `SoDienThoai` varchar(15) DEFAULT NULL,
  `DiaChi` varchar(255) DEFAULT NULL,
  `ImageUrl` text NOT NULL DEFAULT '/ql_sinhvien/giangvienavatars/teacher.png'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Đang đổ dữ liệu cho bảng `giangvien`
--

INSERT INTO `giangvien` (`MaGiangVien`, `HoTen`, `Email`, `SoDienThoai`, `DiaChi`, `ImageUrl`) VALUES
('GV001', 'Nguyen Thi K', 'nguyenthik@example.com', '0987654321', '123 Đường ABC, TP.HCM', '/ql_sinhvien/giangvienavatars/GV001_avatar.png'),
('GV002', 'Le Van L', 'levanl@example.com', '0976543210', '234 Đường DEF, TP.HCM', '/ql_sinhvien/giangvienavatars/GV002_avatar.png'),
('GV003', 'Pham Thi M', 'phamthim@example.com', '0965432109', '345 Đường GHI, TP.HCM', '/ql_sinhvien/giangvienavatars/GV003_avatar.png'),
('GV004', 'Tran Thi N', 'tranthin@example.com', '0954321098', '456 Đường JKL, TP.HCM', '/ql_sinhvien/giangvienavatars/GV004_avatar.png'),
('GV005', 'Nguyen Van A', 'gv005@example.com', '0912345678', '123 Duong 1, Quan 1, TP.HCM', '/ql_sinhvien/giangvienavatars/GV005_avatar.png'),
('GV006', 'Tran Thi B', 'gv006@example.com', '0923456789', '456 Duong 2, Quan 2, TP.HCM', '/ql_sinhvien/giangvienavatars/GV006_avatar.png'),
('GV007', 'Le Van C', 'gv007@example.com', '0934567890', '789 Duong 3, Quan 3, TP.HCM', '/ql_sinhvien/giangvienavatars/GV007_avatar.png'),
('GV008', 'Pham Thi D', 'gv008@example.com', '0945678901', '12 Duong 4, Quan 4, TP.HCM', '/ql_sinhvien/giangvienavatars/GV008_avatar.png'),
('GV009', 'Hoang Van E', 'gv009@example.com', '0956789012', '34 Duong 5, Quan 5, TP.HCM', '/ql_sinhvien/giangvienavatars/GV009_ali-jr.png'),
('GV010', 'Do Thi F', 'gv010@example.com', '0967890123', '56 Duong 6, Quan 6, TP.HCM', '/ql_sinhvien/giangvienavatars/GV010_ali-jr.png'),
('GV011', 'Nguyen Van G', 'gv011@example.com', '0978901234', '78 Duong 7, Quan 7, TP.HCM', '/ql_sinhvien/giangvienavatars/GV011_avatar.png'),
('GV012', 'Tran Thi H', 'gv012@example.com', '0989012345', '90 Duong 8, Quan 8, TP.HCM', '/ql_sinhvien/giangvienavatars/GV012_avatar.png'),
('GV013', 'Le Van I', 'gv013@example.com', '0990123456', '123 Duong 9, Quan 9, TP.HCM', '/ql_sinhvien/giangvienavatars/GV013_avatar.png'),
('GV014', 'Pham Thi K', 'gv014@example.com', '0901234567', '456 Duong 10, Quan 10, TP.HCM', '/ql_sinhvien/giangvienavatars/GV014_avatar.png'),
('GV015', 'Hoang Van L', 'gv015@example.com', '0912345679', '789 Duong 11, Quan 11, TP.HCM', '/ql_sinhvien/giangvienavatars/GV015_avatar.png'),
('GV016', 'Do Thi M', 'gv016@example.com', '0923456780', '12 Duong 12, Quan 12, TP.HCM', '/ql_sinhvien/giangvienavatars/GV016_avatar.png'),
('GV017', 'Nguyen Van N', 'gv017@example.com', '0934567891', '34 Duong 13, Quan 1, TP.HCM', '/ql_sinhvien/giangvienavatars/GV017_avatar.png'),
('GV018', 'Tran Thi O', 'gv018@example.com', '0945678902', '56 Duong 14, Quan 2, TP.HCM', '/ql_sinhvien/giangvienavatars/GV018_avatar.png'),
('GV019', 'Le Van P', 'gv019@example.com', '0956789013', '78 Duong 15, Quan 3, TP.HCM', '/ql_sinhvien/giangvienavatars/GV019_avatar.png'),
('GV020', 'Pham Thi Q', 'gv020@example.com', '0967890124', '90 Duong 16, Quan 4, TP.HCM', '/ql_sinhvien/giangvienavatars/GV020_avatar.png'),
('GV021', 'Hoang Van R', 'gv021@example.com', '0978901235', '123 Duong 17, Quan 5, TP.HCM', '/ql_sinhvien/giangvienavatars/GV021_avatar.png'),
('GV022', 'Do Thi S', 'gv022@example.com', '0989012346', '456 Duong 18, Quan 6, TP.HCM', '/ql_sinhvien/giangvienavatars/GV022_avatar.png'),
('GV023', 'Nguyen Van T', 'gv023@example.com', '0990123457', '789 Duong 19, Quan 7, TP.HCM', '/ql_sinhvien/giangvienavatars/GV023_avatar.png'),
('GV024', 'Tran Thi U', 'gv024@example.com', '0901234568', '12 Duong 20, Quan 8, TP.HCM', '/ql_sinhvien/giangvienavatars/GV024_avatar.png'),
('GV025', 'test02', 'test02@email.com', NULL, NULL, '/ql_sinhvien/giangvienavatars/GV025_ali-jr.png'),
('GV026', 'test03', 'test03@email.com', NULL, NULL, '/ql_sinhvien/giangvienavatars/GV026_ali-jr.png'),
('GV027', 'test03', 'test04@email.com', NULL, NULL, '/ql_sinhvien/giangvienavatars/GV027_ali-jr.png');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `lophoc`
--

CREATE TABLE `lophoc` (
  `MaLopHoc` varchar(10) NOT NULL,
  `TenLopHoc` varchar(100) NOT NULL,
  `MaGiangVien` varchar(100) DEFAULT NULL,
  `MaMonHoc` varchar(10) DEFAULT NULL,
  `ThoiKhoaBieu` enum('Thứ 2 - Ca 1','Thứ 2 - Ca 2','Thứ 2 - Ca 3','Thứ 2 - Ca 4','Thứ 2 - Ca 5','Thứ 3 - Ca 1','Thứ 3 - Ca 2','Thứ 3 - Ca 3','Thứ 3 - Ca 4','Thứ 3 - Ca 5','Thứ 4 - Ca 1','Thứ 4 - Ca 2','Thứ 4 - Ca 3','Thứ 4 - Ca 4','Thứ 4 - Ca 5','Thứ 5 - Ca 1','Thứ 5 - Ca 2','Thứ 5 - Ca 3','Thứ 5 - Ca 4','Thứ 5 - Ca 5','Thứ 6 - Ca 1','Thứ 6 - Ca 2','Thứ 6 - Ca 3','Thứ 6 - Ca 4','Thứ 6 - Ca 5','Thứ 7 - Ca 1','Thứ 7 - Ca 2','Thứ 7 - Ca 3','Thứ 7 - Ca 4','Thứ 7 - Ca 5','Chủ Nhật - Ca 1','Chủ Nhật - Ca 2','Chủ Nhật - Ca 3','Chủ Nhật - Ca 4','Chủ Nhật - Ca 5') DEFAULT NULL,
  `SinhVienToiDa` int(11) NOT NULL DEFAULT 30,
  `SinhVienDangKy` int(11) NOT NULL DEFAULT 0,
  `NgayBatDau` date NOT NULL DEFAULT current_timestamp(),
  `NgayKetThuc` date NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Đang đổ dữ liệu cho bảng `lophoc`
--

INSERT INTO `lophoc` (`MaLopHoc`, `TenLopHoc`, `MaGiangVien`, `MaMonHoc`, `ThoiKhoaBieu`, `SinhVienToiDa`, `SinhVienDangKy`, `NgayBatDau`, `NgayKetThuc`) VALUES
('LH001', 'Lớp 1 - Quản trị Doanh nghiệp', 'GV005', 'MH001', 'Thứ 2 - Ca 1', 14, 3, '2024-10-02', '2024-12-30'),
('LH002', 'Lớp 2 - Marketing căn bản', 'GV006', 'MH002', 'Thứ 3 - Ca 2', 10, 1, '2024-10-03', '2024-12-31'),
('LH003', 'Lớp 3 - Tài chính doanh nghiệp', 'GV007', 'MH003', 'Thứ 4 - Ca 3', 5, 0, '2024-10-04', '2024-12-30'),
('LH004', 'Lớp 4 - Kế toán tài chính', 'GV008', 'MH004', 'Thứ 5 - Ca 4', 15, 0, '2024-10-05', '2024-12-30'),
('LH005', 'Lớp 5 - Kỹ thuật xây dựng cơ bản', 'GV009', 'MH005', 'Thứ 6 - Ca 5', 30, 2, '2024-10-06', '2024-12-31');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `monhoc`
--

CREATE TABLE `monhoc` (
  `MaMonHoc` varchar(50) NOT NULL,
  `TenMonHoc` varchar(100) NOT NULL,
  `SoTinChi` int(11) NOT NULL,
  `GiaTien` bigint(20) NOT NULL DEFAULT 900000,
  `DanhMuc` enum('Quản trị Kinh doanh','Marketing','Tài chính - Ngân hàng','Kế toán','Kỹ thuật xây dựng','Công nghệ Chế tạo máy','Công nghệ thông tin','CNKT Điện - Điện tử','CNKT Điều khiển - Tự động hóa','CNKT Ô tô','Khoa Du lịch','Khoa học cơ bản','Ngoại ngữ','Khoa Luật') DEFAULT 'Công nghệ thông tin'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Đang đổ dữ liệu cho bảng `monhoc`
--

INSERT INTO `monhoc` (`MaMonHoc`, `TenMonHoc`, `SoTinChi`, `GiaTien`, `DanhMuc`) VALUES
('MH001', 'Quản trị Doanh nghiệp', 3, 1200000, 'Quản trị Kinh doanh'),
('MH002', 'Marketing căn bản', 3, 1100000, 'Marketing'),
('MH003', 'Tài chính doanh nghiệp', 4, 1400000, 'Tài chính - Ngân hàng'),
('MH004', 'Kế toán tài chính', 3, 1150000, 'Kế toán'),
('MH005', 'Kỹ thuật xây dựng cơ bản', 4, 1500000, 'Kỹ thuật xây dựng'),
('MH006', 'Chế tạo máy móc', 4, 1450000, 'Công nghệ Chế tạo máy'),
('MH007', 'Lập trình Java 1', 3, 1300000, 'Công nghệ thông tin'),
('MH008', 'Điện tử căn bản', 4, 1350000, 'CNKT Điện - Điện tử'),
('MH009', 'Hệ thống điều khiển tự động', 3, 1250000, 'CNKT Điều khiển - Tự động hóa'),
('MH010', 'Công nghệ ô tô', 4, 1500000, 'CNKT Ô tô'),
('MH011', 'Du lịch quốc tế', 3, 1250000, 'Khoa Du lịch'),
('MH012', 'Toán cao cấp', 3, 1200000, 'Khoa học cơ bản'),
('MH013', 'Tiếng Anh thương mại', 3, 1100000, 'Ngoại ngữ'),
('MH014', 'Luật kinh tế', 3, 1150000, 'Khoa Luật'),
('MH015', 'Quản lý dự án', 3, 1300000, 'Quản trị Kinh doanh'),
('MH016', 'Chiến lược Marketing', 3, 1200000, 'Marketing'),
('MH017', 'Phân tích tài chính', 4, 1400000, 'Tài chính - Ngân hàng'),
('MH018', 'Kế toán quản trị', 3, 1150000, 'Kế toán'),
('MH019', 'Kết cấu xây dựng', 4, 1500000, 'Kỹ thuật xây dựng'),
('MH020', 'Chế tạo robot', 4, 1450000, 'Công nghệ Chế tạo máy'),
('MH021', 'Lập trình web', 3, 1300000, 'Công nghệ thông tin'),
('MH022', 'Điện tử số', 4, 1350000, 'CNKT Điện - Điện tử'),
('MH023', 'Tự động hóa sản xuất', 3, 1250000, 'CNKT Điều khiển - Tự động hóa'),
('MH024', 'Kỹ thuật động cơ ô tô', 4, 1500000, 'CNKT Ô tô'),
('MH025', 'Quản lý nhà hàng khách sạn', 3, 1250000, 'Khoa Du lịch'),
('MH026', 'Vật lý đại cương', 3, 1200000, 'Khoa học cơ bản'),
('MH027', 'Tiếng Nhật giao tiếp', 3, 1100000, 'Ngoại ngữ'),
('MH028', 'Luật lao động', 3, 1150000, 'Khoa Luật'),
('MH029', 'Quản trị nhân sự', 3, 1300000, 'Quản trị Kinh doanh'),
('MH030', 'Nghiên cứu thị trường', 3, 1200000, 'Marketing'),
('MH031', 'Ngân hàng thương mại', 4, 1400000, 'Tài chính - Ngân hàng'),
('MH032', 'Kế toán quốc tế', 3, 1150000, 'Kế toán'),
('MH033', 'Vật liệu xây dựng', 4, 1500000, 'Kỹ thuật xây dựng'),
('MH034', 'Công nghệ chế tạo tiên tiến', 4, 1450000, 'Công nghệ Chế tạo máy'),
('MH035', 'An ninh mạng', 3, 1300000, 'Công nghệ thông tin'),
('MH036', 'Mạch điện tử', 4, 1350000, 'CNKT Điện - Điện tử'),
('MH037', 'Điều khiển thông minh', 3, 1250000, 'CNKT Điều khiển - Tự động hóa'),
('MH038', 'Hệ thống ô tô hiện đại', 4, 1500000, 'CNKT Ô tô'),
('MH039', 'Du lịch sinh thái', 3, 1250000, 'Khoa Du lịch'),
('MH040', 'Hoá học đại cương', 3, 1200000, 'Khoa học cơ bản'),
('MH041', 'Tiếng Trung thương mại', 3, 1100000, 'Ngoại ngữ'),
('MH042', 'Luật quốc tế', 3, 1150000, 'Khoa Luật'),
('MH043', 'Quản lý sản xuất', 3, 1300000, 'Quản trị Kinh doanh'),
('MH044', 'Truyền thông marketing', 3, 1200000, 'Marketing'),
('MH045', 'Quản lý ngân hàng', 4, 1400000, 'Tài chính - Ngân hàng'),
('MH046', 'Kế toán tài chính nâng cao', 3, 1150000, 'Kế toán'),
('MH047', 'Thiết kế kiến trúc', 4, 1500000, 'Kỹ thuật xây dựng'),
('MH048', 'Công nghệ hàn', 4, 1450000, 'Công nghệ Chế tạo máy'),
('MH049', 'Phát triển phần mềm', 3, 1300000, 'Công nghệ thông tin'),
('MH050', 'Hệ thống nhúng', 4, 1350000, 'CNKT Điện - Điện tử');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `phanconglophoc`
--

CREATE TABLE `phanconglophoc` (
  `MaSinhVien` varchar(10) NOT NULL,
  `MaLopHoc` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Đang đổ dữ liệu cho bảng `phanconglophoc`
--

INSERT INTO `phanconglophoc` (`MaSinhVien`, `MaLopHoc`) VALUES
('SV001', 'LH001'),
('SV001', 'LH002'),
('SV002', 'LH001'),
('SV004', 'LH001'),
('SV007', 'LH005'),
('SV028', 'LH005');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `sinhvien`
--

CREATE TABLE `sinhvien` (
  `MaSinhVien` varchar(10) NOT NULL,
  `HoTen` varchar(100) NOT NULL,
  `NgaySinh` date DEFAULT NULL,
  `GioiTinh` enum('Nam','Nữ') NOT NULL,
  `DiaChi` varchar(255) DEFAULT NULL,
  `SoDienThoai` varchar(15) DEFAULT NULL,
  `Email` varchar(100) DEFAULT NULL,
  `TinChiTichLuy` int(11) NOT NULL DEFAULT 0,
  `HocLuc` enum('Yếu','Trung Bình','Khá','Giỏi','Xuất sắc') NOT NULL DEFAULT 'Xuất sắc',
  `DiemTBC` float NOT NULL DEFAULT 10,
  `ImageUrl` text NOT NULL DEFAULT '/ql_sinhvien/sinhvienavatars/blasphemous.webp'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Đang đổ dữ liệu cho bảng `sinhvien`
--

INSERT INTO `sinhvien` (`MaSinhVien`, `HoTen`, `NgaySinh`, `GioiTinh`, `DiaChi`, `SoDienThoai`, `Email`, `TinChiTichLuy`, `HocLuc`, `DiemTBC`, `ImageUrl`) VALUES
('SV001', 'Nguyen Van ABC', '2000-01-01', 'Nam', '123 Đường ABC, TP.HCM', '0912345678', 'nguyenvana@example.com', 6, 'Giỏi', 8.95, '/ql_sinhvien/sinhvienavatars/SV001_ali-jr.png'),
('SV002', 'Le Thi B', '1999-02-15', 'Nữ', '234 Đường DEF, TP.HCM', '0923456789', 'lethib@example.com', 3, 'Khá', 7.5, '/ql_sinhvien/sinhvienavatars/SV002_avatars.webp'),
('SV004', 'Hoang Thi D', '2000-04-20', 'Nữ', '456 Đường JKL, TP.HCM', '0945678901', 'hoangthid@example.com', 0, 'Yếu', 0, '/ql_sinhvien/sinhvienavatars/SV004_avatars.webp'),
('SV005', 'Vu Van E', '1998-05-25', 'Nam', '567 Đường MNO, TP.HCM', '0956789012', 'vuvane@example.com', 0, 'Yếu', 0, '/ql_sinhvien/sinhvienavatars/SV005_avatars.webp'),
('SV006', 'Phan Thi F', '1999-06-30', 'Nữ', '678 Đường PQR, TP.HCM', '0967890123', 'phanthif@example.com', 0, 'Xuất sắc', 10, '/ql_sinhvien/sinhvienavatars/SV006_avatars.webp'),
('SV007', 'Nguyen Thi G', '2000-07-15', 'Nữ', '789 Đường STU, TP.HCM', '0978901234', 'nguyenthig@example.com', 0, 'Yếu', 0, '/ql_sinhvien/sinhvienavatars/SV007_avatars.webp'),
('SV008', 'Dao Van H', '2001-08-10', 'Nam', '890 Đường VWX, TP.HCM', '0989012345', 'daovanh@example.com', 0, 'Xuất sắc', 10, '/ql_sinhvien/sinhvienavatars/SV008_avatars.webp'),
('SV009', 'Muhammad Ali Jr', '2000-04-27', 'Nam', 'USA', '89999912', 'ali@email.com', 0, 'Xuất sắc', 10, '/ql_sinhvien/sinhvienavatars/SV009_avatars.webp'),
('SV010', 'Nguyen Van Tho', '2000-01-01', 'Nữ', 'Ha Noi', '0900000010', 'email10@example.com', 0, 'Xuất sắc', 10, '/ql_sinhvien/sinhvienavatars/SV010_avatars.webp'),
('SV011', 'Tran Thi B', '2000-02-01', 'Nữ', 'Ha Noi', '0900000011', 'email11@example.com', 0, 'Xuất sắc', 10, '/ql_sinhvien/sinhvienavatars/SV011_ali-jr.png'),
('SV012', 'Le Van C', '2000-03-01', 'Nam', 'Ha Noi', '0900000012', 'email12@example.com', 0, 'Xuất sắc', 10, '/ql_sinhvien/sinhvienavatars/SV012_avatars.webp'),
('SV013', 'Pham Thi D', '2000-04-01', 'Nữ', 'Ha Noi', '0900000013', 'email13@example.com', 0, 'Xuất sắc', 10, '/ql_sinhvien/sinhvienavatars/SV013_avatars.webp'),
('SV014', 'Hoang Van E', '2000-05-01', 'Nam', 'Ha Noi', '0900000014', 'email14@example.com', 0, 'Xuất sắc', 10, '/ql_sinhvien/sinhvienavatars/SV014_avatars.webp'),
('SV015', 'Do Thi F', '2000-06-01', 'Nữ', 'Ha Noi', '0900000015', 'email15@example.com', 0, 'Xuất sắc', 10, '/ql_sinhvien/sinhvienavatars/SV015_avatars.webp'),
('SV016', 'Nguyen Van G', '2000-07-01', 'Nam', 'Ha Noi', '0900000016', 'email16@example.com', 0, 'Xuất sắc', 10, '/ql_sinhvien/sinhvienavatars/SV016_avatars.webp'),
('SV017', 'Tran Thi H', '2000-08-01', 'Nữ', 'Ha Noi', '0900000017', 'email17@example.com', 0, 'Xuất sắc', 10, '/ql_sinhvien/sinhvienavatars/SV017_avatars.webp'),
('SV018', 'Le Van I', '2000-09-01', 'Nam', 'Ha Noi', '0900000018', 'email18@example.com', 0, 'Xuất sắc', 10, '/ql_sinhvien/sinhvienavatars/SV018_avatars.webp'),
('SV019', 'Pham Thi K', '2000-10-01', 'Nữ', 'Ha Noi', '0900000019', 'email19@example.com', 0, 'Xuất sắc', 10, '/ql_sinhvien/sinhvienavatars/SV019_avatars.webp'),
('SV020', 'Hoang Van L', '2000-11-01', 'Nam', 'Ha Noi', '0900000020', 'email20@example.com', 0, 'Xuất sắc', 10, '/ql_sinhvien/sinhvienavatars/SV020_avatars.webp'),
('SV021', 'Do Thi M', '2000-12-01', 'Nữ', 'Ha Noi', '0900000021', 'email21@example.com', 0, 'Xuất sắc', 10, '/ql_sinhvien/sinhvienavatars/SV021_avatars.webp'),
('SV022', 'Nguyen Van N', '2000-01-02', 'Nam', 'Ha Noi', '0900000022', 'email22@example.com', 0, 'Xuất sắc', 10, '/ql_sinhvien/sinhvienavatars/SV022_avatars.webp'),
('SV023', 'Tran Thi O', '2000-02-02', 'Nữ', 'Ha Noi', '0900000023', 'email23@example.com', 0, 'Xuất sắc', 10, '/ql_sinhvien/sinhvienavatars/SV023_avatars.webp'),
('SV024', 'Le Van P', '2000-03-02', 'Nam', 'Ha Noi', '0900000024', 'email24@example.com', 0, 'Xuất sắc', 10, '/ql_sinhvien/sinhvienavatars/SV024_avatars.webp'),
('SV025', 'Pham Thi Q', '2000-04-02', 'Nữ', 'Ha Noi', '0900000025', 'email25@example.com', 0, 'Xuất sắc', 10, '/ql_sinhvien/sinhvienavatars/SV025_avatars.webp'),
('SV026', 'Hoang Van R', '2000-05-02', 'Nam', 'Ha Noi', '0900000026', 'email26@example.com', 0, 'Xuất sắc', 10, '/ql_sinhvien/sinhvienavatars/SV026_avatars.webp'),
('SV027', 'Do Thi S', '2000-06-02', 'Nữ', 'Ha Noi', '0900000027', 'email27@example.com', 0, 'Xuất sắc', 10, '/ql_sinhvien/sinhvienavatars/SV027_avatars.webp'),
('SV028', 'Nguyen Van T', '2000-07-02', 'Nam', 'Ha Noi', '0900000028', 'email28@example.com', 0, 'Yếu', 0, '/ql_sinhvien/sinhvienavatars/SV028_avatars.webp'),
('SV029', 'Tran Thi U', '2000-08-02', 'Nữ', 'Ha Noi', '0900000029', 'email29@example.com', 0, 'Xuất sắc', 10, '/ql_sinhvien/sinhvienavatars/SV029_avatars.webp'),
('SV030', 'Le Van V', '2000-09-02', 'Nam', 'Ha Noi', '0900000030', 'email30@example.com', 0, 'Xuất sắc', 10, '/ql_sinhvien/sinhvienavatars/SV030_avatars.webp'),
('SV031', 'Pham Thi W', '2000-10-02', 'Nữ', 'Ha Noi', '0900000031', 'email31@example.com', 0, 'Xuất sắc', 10, '/ql_sinhvien/sinhvienavatars/SV031_avatars.webp'),
('SV032', 'Hoang Van X', '2000-11-02', 'Nam', 'Ha Noi', '0900000032', 'email32@example.com', 0, 'Xuất sắc', 10, '/ql_sinhvien/sinhvienavatars/SV032_avatars.webp'),
('SV033', 'Do Thi Y', '2000-12-02', 'Nữ', 'Ha Noi', '0900000033', 'email33@example.com', 0, 'Xuất sắc', 10, '/ql_sinhvien/sinhvienavatars/SV033_avatars.webp'),
('SV034', 'Nguyen Van Z', '2000-01-03', 'Nam', 'Ha Noi', '0900000034', 'email34@example.com', 0, 'Xuất sắc', 10, '/ql_sinhvien/sinhvienavatars/SV034_avatars.webp'),
('SV035', 'Tran Thi AA', '2000-02-03', 'Nữ', 'Ha Noi', '0900000035', 'email35@example.com', 0, 'Xuất sắc', 10, '/ql_sinhvien/sinhvienavatars/SV035_avatars.webp'),
('SV036', 'Le Van BB', '2000-03-03', 'Nam', 'Ha Noi', '0900000036', 'email36@example.com', 0, 'Xuất sắc', 10, '/ql_sinhvien/sinhvienavatars/SV036_avatars.webp'),
('SV037', 'Pham Thi CC', '2000-04-03', 'Nữ', 'Ha Noi', '0900000037', 'email37@example.com', 0, 'Xuất sắc', 10, '/ql_sinhvien/sinhvienavatars/SV037_avatars.webp'),
('SV038', 'Hoang Van DD', '2000-05-03', 'Nam', 'Ha Noi', '0900000038', 'email38@example.com', 0, 'Xuất sắc', 10, '/ql_sinhvien/sinhvienavatars/SV038_avatars.webp'),
('SV039', 'Do Thi EE', '2000-06-03', 'Nữ', 'Ha Noi', '0900000039', 'email39@example.com', 0, 'Xuất sắc', 10, '/ql_sinhvien/sinhvienavatars/SV039_avatars.webp'),
('SV040', 'Nguyen Van FF', '2000-07-03', 'Nam', 'Ha Noi', '0900000040', 'email40@example.com', 0, 'Xuất sắc', 10, '/ql_sinhvien/sinhvienavatars/SV040_avatars.webp'),
('SV041', 'Tran Thi GG', '2000-08-03', 'Nữ', 'Ha Noi', '0900000041', 'email41@example.com', 0, 'Xuất sắc', 10, '/ql_sinhvien/sinhvienavatars/SV041_avatars.webp'),
('SV042', 'Le Van HH', '2000-09-03', 'Nam', 'Ha Noi', '0900000042', 'email42@example.com', 0, 'Xuất sắc', 10, '/ql_sinhvien/sinhvienavatars/SV042_avatars.webp'),
('SV043', 'Pham Thi II', '2000-10-03', 'Nữ', 'Ha Noi', '0900000043', 'email43@example.com', 0, 'Xuất sắc', 10, '/ql_sinhvien/sinhvienavatars/SV043_avatars.webp'),
('SV044', 'Hoang Van JJ', '2000-11-03', 'Nam', 'Ha Noi', '0900000044', 'email44@example.com', 0, 'Xuất sắc', 10, '/ql_sinhvien/sinhvienavatars/SV044_avatars.webp'),
('SV045', 'Do Thi KK', '2000-12-03', 'Nữ', 'Ha Noi', '0900000045', 'email45@example.com', 0, 'Xuất sắc', 10, '/ql_sinhvien/sinhvienavatars/SV045_avatars.webp'),
('SV046', 'Nguyen Van LL', '2000-01-04', 'Nam', 'Ha Noi', '0900000046', 'email46@example.com', 0, 'Xuất sắc', 10, '/ql_sinhvien/sinhvienavatars/SV046_avatars.webp'),
('SV047', 'Tran Thi MM', '2000-02-04', 'Nữ', 'Ha Noi', '0900000047', 'email47@example.com', 0, 'Xuất sắc', 10, '/ql_sinhvien/sinhvienavatars/SV047_avatars.webp'),
('SV048', 'Le Van NN', '2000-03-04', 'Nam', 'Ha Noi', '0900000048', 'email48@example.com', 0, 'Xuất sắc', 10, '/ql_sinhvien/sinhvienavatars/SV048_avatars.webp'),
('SV049', 'Pham Thi OO', '2000-04-04', 'Nữ', 'Ha Noi', '0900000049', 'email49@example.com', 0, 'Xuất sắc', 10, '/ql_sinhvien/sinhvienavatars/SV049_avatars.webp'),
('SV050', 'Hoang Van PP', '2000-05-04', 'Nam', 'Ha Noi', '0900000050', 'email50@example.com', 0, 'Xuất sắc', 10, '/ql_sinhvien/sinhvienavatars/SV050_avatars.webp'),
('SV051', 'Nguyen Van Test', '2000-01-19', 'Nam', 'TEST', '09123123', 'sinhvienmoi@email.com', 0, 'Xuất sắc', 10, '/ql_sinhvien/sinhvienavatars/SV051_avatar.png'),
('SV052', 'test', '2000-01-01', 'Nam', 'test', '099866785', 'test5@email.com', 0, 'Xuất sắc', 10, '/ql_sinhvien/sinhvienavatars/SV052_avatar.png');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `taikhoan`
--

CREATE TABLE `taikhoan` (
  `id` int(11) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` text NOT NULL DEFAULT '$2y$10$mlHaq1/3RBGGzTkRWGsF3.GrHdjIjHCRwZGs.2bAwo7b9FFNt03yO',
  `apiKey` text DEFAULT NULL,
  `LoaiTaiKhoan` enum('SinhVien','GiangVien','QuanTriVien') NOT NULL,
  `MaSinhVien` varchar(10) DEFAULT NULL,
  `MaGiangVien` varchar(10) DEFAULT NULL,
  `name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Đang đổ dữ liệu cho bảng `taikhoan`
--

INSERT INTO `taikhoan` (`id`, `email`, `password`, `apiKey`, `LoaiTaiKhoan`, `MaSinhVien`, `MaGiangVien`, `name`) VALUES
(1, 'nguyenvana@example.com', '$2y$10$mlHaq1/3RBGGzTkRWGsF3.GrHdjIjHCRwZGs.2bAwo7b9FFNt03yO', '', 'SinhVien', 'SV001', NULL, 'Nguyen Van A'),
(2, 'lethib@example.com', '$2y$10$mlHaq1/3RBGGzTkRWGsF3.GrHdjIjHCRwZGs.2bAwo7b9FFNt03yO', 'apiKey2', 'SinhVien', 'SV002', NULL, 'Le Thi B'),
(4, 'hoangthid@example.com', '$2y$10$mlHaq1/3RBGGzTkRWGsF3.GrHdjIjHCRwZGs.2bAwo7b9FFNt03yO', 'apiKey4', 'SinhVien', 'SV004', NULL, 'Hoang Thi D'),
(5, 'vuvane@example.com', '$2y$10$mlHaq1/3RBGGzTkRWGsF3.GrHdjIjHCRwZGs.2bAwo7b9FFNt03yO', 'apiKey5', 'SinhVien', 'SV005', NULL, 'Vu Van E'),
(6, 'phanthif@example.com', '$2y$10$mlHaq1/3RBGGzTkRWGsF3.GrHdjIjHCRwZGs.2bAwo7b9FFNt03yO', 'apiKey6', 'SinhVien', 'SV006', NULL, 'Phan Thi F'),
(7, 'nguyenthig@example.com', '$2y$10$mlHaq1/3RBGGzTkRWGsF3.GrHdjIjHCRwZGs.2bAwo7b9FFNt03yO', 'apiKey7', 'SinhVien', 'SV007', NULL, 'Nguyen Thi G'),
(8, 'daovanh@example.com', '$2y$10$mlHaq1/3RBGGzTkRWGsF3.GrHdjIjHCRwZGs.2bAwo7b9FFNt03yO', 'apiKey8', 'SinhVien', 'SV008', NULL, 'Dao Van H'),
(11, 'nguyenthik@example.com', '$2y$10$mlHaq1/3RBGGzTkRWGsF3.GrHdjIjHCRwZGs.2bAwo7b9FFNt03yO', 'apiKey11', 'GiangVien', NULL, 'GV001', 'Nguyen Thi K'),
(12, 'levanl@example.com', '$2y$10$mlHaq1/3RBGGzTkRWGsF3.GrHdjIjHCRwZGs.2bAwo7b9FFNt03yO', 'apiKey12', 'GiangVien', NULL, 'GV002', 'Le Van L'),
(13, 'phamthim@example.com', '$2y$10$mlHaq1/3RBGGzTkRWGsF3.GrHdjIjHCRwZGs.2bAwo7b9FFNt03yO', 'apiKey13', 'GiangVien', NULL, 'GV003', 'Pham Thi M'),
(14, 'tranthin@example.com', '$2y$10$mlHaq1/3RBGGzTkRWGsF3.GrHdjIjHCRwZGs.2bAwo7b9FFNt03yO', 'apiKey14', 'GiangVien', NULL, 'GV004', 'Tran Thi N'),
(15, 'test1@email.com', '$2y$10$0Dg1C6zqsluqecD29ITgUOQIi4Jf9Q5pN32RYXV8AHNSUZ4Dh91/2', '4f017495ecdbb55518ed909aed14f4d59cd93d31eabf6d', 'QuanTriVien', NULL, NULL, 'test1'),
(20, 'ali@email.com', '$2y$10$mlHaq1/3RBGGzTkRWGsF3.GrHdjIjHCRwZGs.2bAwo7b9FFNt03yO', '', 'SinhVien', 'SV009', NULL, 'Muhammad Ali Jr'),
(21, 'email10@example.com', '$2y$10$mlHaq1/3RBGGzTkRWGsF3.GrHdjIjHCRwZGs.2bAwo7b9FFNt03yO', NULL, 'SinhVien', 'SV010', NULL, 'Nguyen Van A'),
(22, 'email11@example.com', '$2y$10$mlHaq1/3RBGGzTkRWGsF3.GrHdjIjHCRwZGs.2bAwo7b9FFNt03yO', NULL, 'SinhVien', 'SV011', NULL, 'Tran Thi B'),
(23, 'email12@example.com', '$2y$10$mlHaq1/3RBGGzTkRWGsF3.GrHdjIjHCRwZGs.2bAwo7b9FFNt03yO', NULL, 'SinhVien', 'SV012', NULL, 'Le Van C'),
(24, 'email13@example.com', '$2y$10$mlHaq1/3RBGGzTkRWGsF3.GrHdjIjHCRwZGs.2bAwo7b9FFNt03yO', NULL, 'SinhVien', 'SV013', NULL, 'Pham Thi D'),
(25, 'email14@example.com', '$2y$10$mlHaq1/3RBGGzTkRWGsF3.GrHdjIjHCRwZGs.2bAwo7b9FFNt03yO', NULL, 'SinhVien', 'SV014', NULL, 'Hoang Van E'),
(26, 'email15@example.com', '$2y$10$mlHaq1/3RBGGzTkRWGsF3.GrHdjIjHCRwZGs.2bAwo7b9FFNt03yO', NULL, 'SinhVien', 'SV015', NULL, 'Do Thi F'),
(27, 'email16@example.com', '$2y$10$mlHaq1/3RBGGzTkRWGsF3.GrHdjIjHCRwZGs.2bAwo7b9FFNt03yO', NULL, 'SinhVien', 'SV016', NULL, 'Nguyen Van G'),
(28, 'email17@example.com', '$2y$10$mlHaq1/3RBGGzTkRWGsF3.GrHdjIjHCRwZGs.2bAwo7b9FFNt03yO', NULL, 'SinhVien', 'SV017', NULL, 'Tran Thi H'),
(29, 'email18@example.com', '$2y$10$mlHaq1/3RBGGzTkRWGsF3.GrHdjIjHCRwZGs.2bAwo7b9FFNt03yO', NULL, 'SinhVien', 'SV018', NULL, 'Le Van I'),
(30, 'email19@example.com', '$2y$10$mlHaq1/3RBGGzTkRWGsF3.GrHdjIjHCRwZGs.2bAwo7b9FFNt03yO', NULL, 'SinhVien', 'SV019', NULL, 'Pham Thi K'),
(31, 'email20@example.com', '$2y$10$mlHaq1/3RBGGzTkRWGsF3.GrHdjIjHCRwZGs.2bAwo7b9FFNt03yO', NULL, 'SinhVien', 'SV020', NULL, 'Hoang Van L'),
(32, 'email21@example.com', '$2y$10$mlHaq1/3RBGGzTkRWGsF3.GrHdjIjHCRwZGs.2bAwo7b9FFNt03yO', NULL, 'SinhVien', 'SV021', NULL, 'Do Thi M'),
(33, 'email22@example.com', '$2y$10$mlHaq1/3RBGGzTkRWGsF3.GrHdjIjHCRwZGs.2bAwo7b9FFNt03yO', NULL, 'SinhVien', 'SV022', NULL, 'Nguyen Van N'),
(34, 'email23@example.com', '$2y$10$mlHaq1/3RBGGzTkRWGsF3.GrHdjIjHCRwZGs.2bAwo7b9FFNt03yO', NULL, 'SinhVien', 'SV023', NULL, 'Tran Thi O'),
(35, 'email24@example.com', '$2y$10$mlHaq1/3RBGGzTkRWGsF3.GrHdjIjHCRwZGs.2bAwo7b9FFNt03yO', NULL, 'SinhVien', 'SV024', NULL, 'Le Van P'),
(36, 'email25@example.com', '$2y$10$mlHaq1/3RBGGzTkRWGsF3.GrHdjIjHCRwZGs.2bAwo7b9FFNt03yO', NULL, 'SinhVien', 'SV025', NULL, 'Pham Thi Q'),
(37, 'email26@example.com', '$2y$10$mlHaq1/3RBGGzTkRWGsF3.GrHdjIjHCRwZGs.2bAwo7b9FFNt03yO', NULL, 'SinhVien', 'SV026', NULL, 'Hoang Van R'),
(38, 'email27@example.com', '$2y$10$mlHaq1/3RBGGzTkRWGsF3.GrHdjIjHCRwZGs.2bAwo7b9FFNt03yO', NULL, 'SinhVien', 'SV027', NULL, 'Do Thi S'),
(39, 'email28@example.com', '$2y$10$mlHaq1/3RBGGzTkRWGsF3.GrHdjIjHCRwZGs.2bAwo7b9FFNt03yO', NULL, 'SinhVien', 'SV028', NULL, 'Nguyen Van T'),
(40, 'email29@example.com', '$2y$10$mlHaq1/3RBGGzTkRWGsF3.GrHdjIjHCRwZGs.2bAwo7b9FFNt03yO', NULL, 'SinhVien', 'SV029', NULL, 'Tran Thi U'),
(41, 'email30@example.com', '$2y$10$mlHaq1/3RBGGzTkRWGsF3.GrHdjIjHCRwZGs.2bAwo7b9FFNt03yO', NULL, 'SinhVien', 'SV030', NULL, 'Le Van V'),
(42, 'email31@example.com', '$2y$10$mlHaq1/3RBGGzTkRWGsF3.GrHdjIjHCRwZGs.2bAwo7b9FFNt03yO', NULL, 'SinhVien', 'SV031', NULL, 'Pham Thi W'),
(43, 'email32@example.com', '$2y$10$mlHaq1/3RBGGzTkRWGsF3.GrHdjIjHCRwZGs.2bAwo7b9FFNt03yO', NULL, 'SinhVien', 'SV032', NULL, 'Hoang Van X'),
(44, 'email33@example.com', '$2y$10$mlHaq1/3RBGGzTkRWGsF3.GrHdjIjHCRwZGs.2bAwo7b9FFNt03yO', NULL, 'SinhVien', 'SV033', NULL, 'Do Thi Y'),
(45, 'email34@example.com', '$2y$10$mlHaq1/3RBGGzTkRWGsF3.GrHdjIjHCRwZGs.2bAwo7b9FFNt03yO', NULL, 'SinhVien', 'SV034', NULL, 'Nguyen Van Z'),
(46, 'email35@example.com', '$2y$10$mlHaq1/3RBGGzTkRWGsF3.GrHdjIjHCRwZGs.2bAwo7b9FFNt03yO', NULL, 'SinhVien', 'SV035', NULL, 'Tran Thi AA'),
(47, 'email36@example.com', '$2y$10$mlHaq1/3RBGGzTkRWGsF3.GrHdjIjHCRwZGs.2bAwo7b9FFNt03yO', NULL, 'SinhVien', 'SV036', NULL, 'Le Van BB'),
(48, 'email37@example.com', '$2y$10$mlHaq1/3RBGGzTkRWGsF3.GrHdjIjHCRwZGs.2bAwo7b9FFNt03yO', NULL, 'SinhVien', 'SV037', NULL, 'Pham Thi CC'),
(49, 'email38@example.com', '$2y$10$mlHaq1/3RBGGzTkRWGsF3.GrHdjIjHCRwZGs.2bAwo7b9FFNt03yO', NULL, 'SinhVien', 'SV038', NULL, 'Hoang Van DD'),
(50, 'email39@example.com', '$2y$10$mlHaq1/3RBGGzTkRWGsF3.GrHdjIjHCRwZGs.2bAwo7b9FFNt03yO', NULL, 'SinhVien', 'SV039', NULL, 'Do Thi EE'),
(51, 'email40@example.com', '$2y$10$mlHaq1/3RBGGzTkRWGsF3.GrHdjIjHCRwZGs.2bAwo7b9FFNt03yO', NULL, 'SinhVien', 'SV040', NULL, 'Nguyen Van FF'),
(52, 'email41@example.com', '$2y$10$mlHaq1/3RBGGzTkRWGsF3.GrHdjIjHCRwZGs.2bAwo7b9FFNt03yO', NULL, 'SinhVien', 'SV041', NULL, 'Tran Thi GG'),
(53, 'email42@example.com', '$2y$10$mlHaq1/3RBGGzTkRWGsF3.GrHdjIjHCRwZGs.2bAwo7b9FFNt03yO', NULL, 'SinhVien', 'SV042', NULL, 'Le Van HH'),
(54, 'email43@example.com', '$2y$10$mlHaq1/3RBGGzTkRWGsF3.GrHdjIjHCRwZGs.2bAwo7b9FFNt03yO', NULL, 'SinhVien', 'SV043', NULL, 'Pham Thi II'),
(55, 'email44@example.com', '$2y$10$mlHaq1/3RBGGzTkRWGsF3.GrHdjIjHCRwZGs.2bAwo7b9FFNt03yO', NULL, 'SinhVien', 'SV044', NULL, 'Hoang Van JJ'),
(56, 'email45@example.com', '$2y$10$mlHaq1/3RBGGzTkRWGsF3.GrHdjIjHCRwZGs.2bAwo7b9FFNt03yO', NULL, 'SinhVien', 'SV045', NULL, 'Do Thi KK'),
(57, 'email46@example.com', '$2y$10$mlHaq1/3RBGGzTkRWGsF3.GrHdjIjHCRwZGs.2bAwo7b9FFNt03yO', NULL, 'SinhVien', 'SV046', NULL, 'Nguyen Van LL'),
(58, 'email47@example.com', '$2y$10$mlHaq1/3RBGGzTkRWGsF3.GrHdjIjHCRwZGs.2bAwo7b9FFNt03yO', NULL, 'SinhVien', 'SV047', NULL, 'Tran Thi MM'),
(59, 'email48@example.com', '$2y$10$mlHaq1/3RBGGzTkRWGsF3.GrHdjIjHCRwZGs.2bAwo7b9FFNt03yO', NULL, 'SinhVien', 'SV048', NULL, 'Le Van NN'),
(60, 'email49@example.com', '$2y$10$mlHaq1/3RBGGzTkRWGsF3.GrHdjIjHCRwZGs.2bAwo7b9FFNt03yO', NULL, 'SinhVien', 'SV049', NULL, 'Pham Thi OO'),
(61, 'email50@example.com', '$2y$10$mlHaq1/3RBGGzTkRWGsF3.GrHdjIjHCRwZGs.2bAwo7b9FFNt03yO', NULL, 'SinhVien', 'SV050', NULL, 'Hoang Van PP'),
(62, 'gv005@example.com', '$2y$10$mlHaq1/3RBGGzTkRWGsF3.GrHdjIjHCRwZGs.2bAwo7b9FFNt03yO', NULL, 'GiangVien', NULL, 'GV005', 'Nguyen Van A'),
(63, 'gv006@example.com', '$2y$10$mlHaq1/3RBGGzTkRWGsF3.GrHdjIjHCRwZGs.2bAwo7b9FFNt03yO', NULL, 'GiangVien', NULL, 'GV006', 'Tran Thi B'),
(64, 'gv007@example.com', '$2y$10$mlHaq1/3RBGGzTkRWGsF3.GrHdjIjHCRwZGs.2bAwo7b9FFNt03yO', NULL, 'GiangVien', NULL, 'GV007', 'Le Van C'),
(65, 'gv008@example.com', '$2y$10$mlHaq1/3RBGGzTkRWGsF3.GrHdjIjHCRwZGs.2bAwo7b9FFNt03yO', NULL, 'GiangVien', NULL, 'GV008', 'Pham Thi D'),
(66, 'gv009@example.com', '$2y$10$mlHaq1/3RBGGzTkRWGsF3.GrHdjIjHCRwZGs.2bAwo7b9FFNt03yO', NULL, 'GiangVien', NULL, 'GV009', 'Hoang Van E'),
(67, 'gv010@example.com', '$2y$10$mlHaq1/3RBGGzTkRWGsF3.GrHdjIjHCRwZGs.2bAwo7b9FFNt03yO', NULL, 'GiangVien', NULL, 'GV010', 'Do Thi F'),
(68, 'gv011@example.com', '$2y$10$mlHaq1/3RBGGzTkRWGsF3.GrHdjIjHCRwZGs.2bAwo7b9FFNt03yO', NULL, 'GiangVien', NULL, 'GV011', 'Nguyen Van G'),
(69, 'gv012@example.com', '$2y$10$mlHaq1/3RBGGzTkRWGsF3.GrHdjIjHCRwZGs.2bAwo7b9FFNt03yO', NULL, 'GiangVien', NULL, 'GV012', 'Tran Thi H'),
(70, 'gv013@example.com', '$2y$10$mlHaq1/3RBGGzTkRWGsF3.GrHdjIjHCRwZGs.2bAwo7b9FFNt03yO', NULL, 'GiangVien', NULL, 'GV013', 'Le Van I'),
(71, 'gv014@example.com', '$2y$10$mlHaq1/3RBGGzTkRWGsF3.GrHdjIjHCRwZGs.2bAwo7b9FFNt03yO', NULL, 'GiangVien', NULL, 'GV014', 'Pham Thi K'),
(72, 'gv015@example.com', '$2y$10$mlHaq1/3RBGGzTkRWGsF3.GrHdjIjHCRwZGs.2bAwo7b9FFNt03yO', NULL, 'GiangVien', NULL, 'GV015', 'Hoang Van L'),
(73, 'gv016@example.com', '$2y$10$mlHaq1/3RBGGzTkRWGsF3.GrHdjIjHCRwZGs.2bAwo7b9FFNt03yO', NULL, 'GiangVien', NULL, 'GV016', 'Do Thi M'),
(74, 'gv017@example.com', '$2y$10$mlHaq1/3RBGGzTkRWGsF3.GrHdjIjHCRwZGs.2bAwo7b9FFNt03yO', NULL, 'GiangVien', NULL, 'GV017', 'Nguyen Van N'),
(75, 'gv018@example.com', '$2y$10$mlHaq1/3RBGGzTkRWGsF3.GrHdjIjHCRwZGs.2bAwo7b9FFNt03yO', NULL, 'GiangVien', NULL, 'GV018', 'Tran Thi O'),
(76, 'gv019@example.com', '$2y$10$mlHaq1/3RBGGzTkRWGsF3.GrHdjIjHCRwZGs.2bAwo7b9FFNt03yO', NULL, 'GiangVien', NULL, 'GV019', 'Le Van P'),
(77, 'gv020@example.com', '$2y$10$mlHaq1/3RBGGzTkRWGsF3.GrHdjIjHCRwZGs.2bAwo7b9FFNt03yO', NULL, 'GiangVien', NULL, 'GV020', 'Pham Thi Q'),
(78, 'gv021@example.com', '$2y$10$mlHaq1/3RBGGzTkRWGsF3.GrHdjIjHCRwZGs.2bAwo7b9FFNt03yO', NULL, 'GiangVien', NULL, 'GV021', 'Hoang Van R'),
(79, 'gv022@example.com', '$2y$10$mlHaq1/3RBGGzTkRWGsF3.GrHdjIjHCRwZGs.2bAwo7b9FFNt03yO', NULL, 'GiangVien', NULL, 'GV022', 'Do Thi S'),
(80, 'gv023@example.com', '$2y$10$mlHaq1/3RBGGzTkRWGsF3.GrHdjIjHCRwZGs.2bAwo7b9FFNt03yO', NULL, 'GiangVien', NULL, 'GV023', 'Nguyen Van T'),
(81, 'gv024@example.com', '$2y$10$mlHaq1/3RBGGzTkRWGsF3.GrHdjIjHCRwZGs.2bAwo7b9FFNt03yO', NULL, 'GiangVien', NULL, 'GV024', 'Tran Thi U'),
(82, 'test02@email.com', '$2y$10$g.eCrVdi0paYkThIAsnPPOptiq9f9pQaqhSgdlzRVcew0bYRQ8/Ke', '', 'GiangVien', NULL, NULL, 'test02'),
(83, 'test03@email.com', '$2y$10$HZj6k1cnnWio1Qay8ZpBTO6AcWLQ.9TdBEektIsY6.UjZxUeZ/giq', '', 'GiangVien', NULL, NULL, 'test03'),
(85, 'sinhvienmoi@email.com', '$2y$10$mlHaq1/3RBGGzTkRWGsF3.GrHdjIjHCRwZGs.2bAwo7b9FFNt03yO', '', 'SinhVien', 'SV051', NULL, 'Nguyen Van Test'),
(86, 'test5@email.com', '$2y$10$mlHaq1/3RBGGzTkRWGsF3.GrHdjIjHCRwZGs.2bAwo7b9FFNt03yO', '', 'SinhVien', 'SV052', NULL, 'test');

--
-- Chỉ mục cho các bảng đã đổ
--

--
-- Chỉ mục cho bảng `chamdiem`
--
ALTER TABLE `chamdiem`
  ADD PRIMARY KEY (`MaSinhVien`,`MaMonHoc`);

--
-- Chỉ mục cho bảng `giangvien`
--
ALTER TABLE `giangvien`
  ADD PRIMARY KEY (`MaGiangVien`),
  ADD UNIQUE KEY `Email` (`Email`);

--
-- Chỉ mục cho bảng `lophoc`
--
ALTER TABLE `lophoc`
  ADD PRIMARY KEY (`MaLopHoc`),
  ADD KEY `fk_lophoc_giangvien` (`MaGiangVien`),
  ADD KEY `fk_lophoc_monhoc` (`MaMonHoc`);

--
-- Chỉ mục cho bảng `monhoc`
--
ALTER TABLE `monhoc`
  ADD PRIMARY KEY (`MaMonHoc`);

--
-- Chỉ mục cho bảng `phanconglophoc`
--
ALTER TABLE `phanconglophoc`
  ADD PRIMARY KEY (`MaSinhVien`,`MaLopHoc`),
  ADD KEY `fk_phanconglophoc_lophoc` (`MaLopHoc`);

--
-- Chỉ mục cho bảng `sinhvien`
--
ALTER TABLE `sinhvien`
  ADD PRIMARY KEY (`MaSinhVien`),
  ADD UNIQUE KEY `Email` (`Email`);

--
-- Chỉ mục cho bảng `taikhoan`
--
ALTER TABLE `taikhoan`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `fk_taikhoan_sinhvien` (`MaSinhVien`),
  ADD KEY `fk_taikhoan_giangvien` (`MaGiangVien`);

--
-- AUTO_INCREMENT cho các bảng đã đổ
--

--
-- AUTO_INCREMENT cho bảng `taikhoan`
--
ALTER TABLE `taikhoan`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=87;

--
-- Các ràng buộc cho các bảng đã đổ
--

--
-- Các ràng buộc cho bảng `lophoc`
--
ALTER TABLE `lophoc`
  ADD CONSTRAINT `fk_lophoc_giangvien` FOREIGN KEY (`MaGiangVien`) REFERENCES `giangvien` (`MaGiangVien`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_lophoc_monhoc` FOREIGN KEY (`MaMonHoc`) REFERENCES `monhoc` (`MaMonHoc`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `phanconglophoc`
--
ALTER TABLE `phanconglophoc`
  ADD CONSTRAINT `fk_phanconglophoc_lophoc` FOREIGN KEY (`MaLopHoc`) REFERENCES `lophoc` (`MaLopHoc`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_phanconglophoc_sinhvien` FOREIGN KEY (`MaSinhVien`) REFERENCES `sinhvien` (`MaSinhVien`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `taikhoan`
--
ALTER TABLE `taikhoan`
  ADD CONSTRAINT `fk_taikhoan_giangvien` FOREIGN KEY (`MaGiangVien`) REFERENCES `giangvien` (`MaGiangVien`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_taikhoan_sinhvien` FOREIGN KEY (`MaSinhVien`) REFERENCES `sinhvien` (`MaSinhVien`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

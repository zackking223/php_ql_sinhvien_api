<?php
header('Content-Type: application/json');
function authenticateRequest(mysqli $con)
{
    $data = apache_request_headers();

    // Kiểm tra xem email và apiKey có tồn tại không
    if (!isset($data['email']) || !isset($data['apiKey'])) {
        // email and apiKey are required
        echo json_encode(['status' => 'failed', 'message' => 'unauthorized access']);
        exit;
    }

    $email = $data['email'];
    $apiKey = $data['apiKey'];

    // Truy vấn CSDL để kiểm tra email, apiKey và LoaiTaiKhoan
    $query = "SELECT LoaiTaiKhoan FROM taikhoan WHERE email = ? AND apiKey = ?";
    $stmt = mysqli_prepare($con, $query);
    mysqli_stmt_bind_param($stmt, "ss", $email, $apiKey);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if ($row = mysqli_fetch_assoc($result)) {
        // Kiểm tra LoaiTaiKhoan
        $loaiTaiKhoan = $row['LoaiTaiKhoan'];
        if ($loaiTaiKhoan == 'QuanTriVien' || $loaiTaiKhoan == 'GiangVien') {
            // Nếu LoaiTaiKhoan là QuanTriVien hoặc GiaoVien, tiếp tục
            return true;
        } else {
            echo json_encode(['status' => 'failed', 'message' => 'unauthorized access']);
            exit;
        }
    } else {
        // Nếu email hoặc apiKey không hợp lệ
        echo json_encode(['status' => 'failed', 'message' => 'unauthorized access']);
        exit;
    }
}
$con = mysqli_connect("localhost", "root", "", "ql_sinhvien");

if (!$con) {
    echo json_encode(['status' => 'failed', 'message' => 'Connection failed']);
    exit();
}

// Hàm để tạo mã khóa chính mới
function generateId(mysqli $con)
{
    $query = "SELECT MAX(MaLopHoc) AS max_id FROM LopHoc";
    $result = mysqli_query($con, $query);
    $row = mysqli_fetch_assoc($result);
    $max_id = $row['max_id'];

    // Tạo ID mới
    if ($max_id) {
        $num = intval(substr($max_id, strlen("LH"))) + 1;
    } else {
        $num = 1;
    }
    return "LH" . str_pad($num, 3, '0', STR_PAD_LEFT);
}

if (!$con) {
    echo json_encode(['status' => 'failed', 'message' => 'Connection failed']);
    exit();
}

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        $maLopHoc = isset($_GET['MaLopHoc']) ? $_GET['MaLopHoc'] : '';
        if ($maLopHoc) {
            $query = "SELECT DISTINCT lophoc.MaLopHoc, lophoc.TenLopHoc, lophoc.MaMonHoc, lophoc.MaGiangVien, lophoc.ThoiKhoaBieu, lophoc.MaGiangVien, lophoc.SinhVienDangKy, lophoc.SinhVienToiDa, lophoc.NgayBatDau, lophoc.NgayKetThuc, giangvien.HoTen as `TenGiangVien`, monhoc.TenMonHoc FROM lophoc JOIN giangvien ON giangvien.MaGiangVien = lophoc.MaGiangVien JOIN monhoc ON lophoc.MaMonHoc = monhoc.MaMonHoc WHERE lophoc.MaLopHoc = '$maLopHoc'";
            $result = mysqli_query($con, $query);
            if (mysqli_num_rows($result) > 0) {
                $data = mysqli_fetch_assoc($result);
                $listSinhVienQuery = "SELECT DISTINCT sinhvien.MaSinhVien, sinhvien.HoTen, sinhvien.NgaySinh, sinhvien.GioiTinh, sinhvien.DiaChi, sinhvien.SoDienThoai, sinhvien.Email, sinhvien.ImageUrl FROM sinhvien JOIN phanconglophoc ON sinhvien.MaSinhVien = phanconglophoc.MaSinhVien JOIN lophoc ON phanconglophoc.MaLopHoc = lophoc.MaLopHoc WHERE lophoc.MaLopHoc = '$maLopHoc'";
                $listSinhVienQueryResult = mysqli_query($con, $listSinhVienQuery);
                $listSinhVien = [];
                $listSinhVienCount = mysqli_num_rows($listSinhVienQueryResult);
                if ($listSinhVienCount == 1) {
                    $listSinhVien = [mysqli_fetch_assoc($listSinhVienQueryResult)];
                } else if ($listSinhVienCount > 1) {
                    $listSinhVien = mysqli_fetch_all($listSinhVienQueryResult, MYSQLI_ASSOC);
                }

                $monHocQuery = "SELECT monhoc.MaMonHoc, monhoc.TenMonHoc, monhoc.SoTinChi FROM monhoc JOIN lophoc ON lophoc.MaMonHoc = monhoc.MaMonHoc WHERE lophoc.MaLopHoc = '$maLopHoc'";
                $monHocQueryResult = mysqli_query($con, $monHocQuery);
                $monHocData = mysqli_fetch_assoc($monHocQueryResult);

                $giangVienQuery = "SELECT giangvien.MaGiangVien, HoTen, Email, SoDienThoai, DiaChi FROM giangvien JOIN lophoc ON lophoc.MaGiangVien = giangvien.MaGiangVien WHERE lophoc.MaLopHoc = '$maLopHoc'";
                $giangVienQueryResult = mysqli_query($con, $giangVienQuery);
                $giangVienData = mysqli_fetch_assoc($giangVienQueryResult);

                echo json_encode([
                    'status' => 'success',
                    'message' => 'Tìm thấy lớp học',
                    'data' => $data,
                    'sinhVienList' => $listSinhVien,
                    'monHoc' => $monHocData,
                    'giangVien' => $giangVienData
                ]);
            } else {
                echo json_encode(['status' => 'failed', 'message' => 'Không tìm thấy lớp']);
            }
        } else {
            // Lấy tham số từ $_GET
            $searchStr = isset($_GET['search']) ? $_GET['search'] : '';
            $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
            $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 10; // Số bản ghi mỗi trang
            $offset = ($page - 1) * $limit;

            $query = "SELECT DISTINCT lophoc.MaLopHoc, lophoc.TenLopHoc, lopHoc.MaMonHoc, lophoc.MaGiangVien, lophoc.SinhVienDangKy, lophoc.SinhVienToiDa, lophoc.ThoiKhoaBieu, lophoc.MaGiangVien, lophoc.NgayBatDau, lophoc.NgayKetThuc, giangvien.HoTen as `TenGiangVien`, monhoc.TenMonHoc FROM lophoc JOIN giangvien ON giangvien.MaGiangVien = lophoc.MaGiangVien JOIN monhoc ON lophoc.MaMonHoc = monhoc.MaMonHoc WHERE lophoc.MaLopHoc LIKE '%$searchStr%' OR lophoc.TenLopHoc LIKE '%$searchStr%' OR monhoc.TenMonHoc LIKE '%$searchStr%' OR giangvien.HoTen LIKE '%$searchStr%' LIMIT $limit OFFSET $offset";
            $result = mysqli_query($con, $query);

            if ($result) {
                $data = mysqli_fetch_all($result, MYSQLI_ASSOC);

                // Truy vấn tổng số bản ghi để tính tổng số trang
                $totalQuery = "SELECT COUNT(DISTINCT lophoc.MaLopHoc) as total FROM lophoc JOIN giangvien ON giangvien.MaGiangVien = lophoc.MaGiangVien JOIN monhoc ON lophoc.MaMonHoc = monhoc.MaMonHoc WHERE lophoc.MaLopHoc LIKE '%$searchStr%' OR lophoc.TenLopHoc LIKE '%$searchStr%' OR monhoc.TenMonHoc LIKE '%$searchStr%' OR giangvien.HoTen LIKE '%$searchStr%'";
                $totalResult = mysqli_query($con, $totalQuery);
                $totalRow = mysqli_fetch_assoc($totalResult);
                $totalRecords = intval($totalRow['total']);
                $totalPages = ceil($totalRecords / $limit);

                echo json_encode([
                    'status' => 'success',
                    'message' => 'Tìm thấy tất cả lớp học',
                    'data' => $data,
                    'pagination' => [
                        'current_page' => $page,
                        'total_pages' => $totalPages,
                        'total_records' => $totalRecords,
                        'limit' => $limit
                    ]
                ]);
            } else {
                echo json_encode(['status' => 'failed', 'message' => 'Query failed: ' . mysqli_error($con)]);
            }
        }
        break;

    case 'POST':
        authenticateRequest($con);
        $maLopHoc = generateId($con);
        $tenLopHoc = $_POST['TenLopHoc'];
        $maGiangVien = $_POST['MaGiangVien'];
        $maMonHoc = $_POST['MaMonHoc'];
        $thoiKhoaBieu = $_POST['ThoiKhoaBieu'];
        $ngayBatDau = $_POST['NgayBatDau'];
        $ngayKetThuc = $_POST['NgayKetThuc'];
        $sinhVienToiDa = $_POST['SinhVienToiDa'];

        $query = "SELECT * FROM lophoc WHERE TenLopHoc = '$tenLopHoc'";
        $result = mysqli_query($con, $query);

        if (mysqli_num_rows($result) > 0) {
            echo json_encode(['status' => 'failed', 'message' => 'Tên lớp học đã tồn tại']);
            exit();
        }

        $query = "SELECT * FROM monhoc WHERE MaMonHoc = '$maMonHoc'";
        $result = mysqli_query($con, $query);

        if (mysqli_num_rows($result) == 0) {
            echo json_encode(['status' => 'failed', 'message' => 'Sai mã môn học']);
            exit();
        }

        $query = "SELECT * FROM giangvien WHERE MaGiangVien = '$maGiangVien'";
        $result = mysqli_query($con, $query);

        if (mysqli_num_rows($result) == 0) {
            echo json_encode(['status' => 'failed', 'message' => 'Sai mã giảng viên']);
            exit();
        }

        $query = "INSERT INTO LopHoc (MaLopHoc, TenLopHoc, MaGiangVien, MaMonHoc, ThoiKhoaBieu, SinhVienToiDa, NgayBatDau, NgayKetThuc) VALUES ('$maLopHoc','$tenLopHoc', '$maGiangVien', '$maMonHoc', '$thoiKhoaBieu', $sinhVienToiDa, '$ngayBatDau', '$ngayKetThuc')";

        if (mysqli_query($con, $query)) {
            echo json_encode(['status' => 'success', 'message' => 'Thêm lớp thành công']);
        } else {
            echo json_encode(['status' => 'failed', 'message' => 'Thêm lớp thất bại']);
        }
        break;

    case 'PUT':
        authenticateRequest($con);
        parse_str(file_get_contents("php://input"), $_PUT);
        $maLopHoc = $_PUT['MaLopHoc'];
        $tenLopHoc = $_PUT['TenLopHoc'];
        $maGiangVien = $_PUT['MaGiangVien'];
        $maMonHoc = $_PUT['MaMonHoc'];
        $thoiKhoaBieu = $_PUT['ThoiKhoaBieu'];
        $ngayBatDau = $_PUT['NgayBatDau'];
        $ngayKetThuc = $_PUT['NgayKetThuc'];
        $sinhVienToiDa = $_PUT['SinhVienToiDa'];

        $query = "SELECT * FROM monhoc WHERE MaMonHoc = '$maMonHoc'";
        $result = mysqli_query($con, $query);

        if (mysqli_num_rows($result) == 0) {
            echo json_encode(['status' => 'failed', 'message' => 'Sai mã môn học']);
            exit();
        }

        $query = "SELECT * FROM giangvien WHERE MaGiangVien = '$maGiangVien'";
        $result = mysqli_query($con, $query);

        if (mysqli_num_rows($result) == 0) {
            echo json_encode(['status' => 'failed', 'message' => 'Sai mã giảng viên']);
            exit();
        }

        // Kiểm tra trùng lặp TenLopHoc với các lớp khác
        $query = "SELECT * FROM LopHoc WHERE TenLopHoc = '$tenLopHoc' AND MaLopHoc != '$maLopHoc'";
        $result = mysqli_query($con, $query);

        if (mysqli_num_rows($result) > 0) {
            echo json_encode(['status' => 'failed', 'message' => 'Tên lớp học đã tồn tại']);
            exit();
        }

        $query = "UPDATE LopHoc SET TenLopHoc = '$tenLopHoc', MaGiangVien = '$maGiangVien', MaMonHoc = '$maMonHoc', ThoiKhoaBieu = '$thoiKhoaBieu', SinhVienToiDa = $sinhVienToiDa, ngayBatDau = '$ngayBatDau', ngayKetThuc = '$ngayKetThuc' WHERE MaLopHoc = '$maLopHoc'";

        if (mysqli_query($con, $query)) {
            echo json_encode(['status' => 'success', 'message' => 'Cập nhật lớp thành công']);
        } else {
            echo json_encode(['status' => 'failed', 'message' => 'Cập nhật lớp thất bại']);
        }
        break;

    case 'DELETE':
        authenticateRequest($con);
        $headers = apache_request_headers();
        $maLopHoc = isset($headers['MaLopHoc']) ? $headers['MaLopHoc'] : "";

        if ($maLopHoc != "") {
            $query = "DELETE FROM LopHoc WHERE MaLopHoc = '$maLopHoc'";

            if (mysqli_query($con, $query)) {
                echo json_encode(['status' => 'success', 'message' => 'Xóa lớp thành công']);
            } else {
                echo json_encode(['status' => 'failed', 'message' => 'Xóa lớp thất bại']);
            }
        } else {
            echo json_encode(['status' => 'failed', 'message' => 'Không thấy mã lớp']);
        }
        break;

    default:
        echo json_encode(['status' => 'failed', 'message' => 'Invalid request']);
        break;
}

mysqli_close($con);

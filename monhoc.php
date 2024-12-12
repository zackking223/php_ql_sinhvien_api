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

function generateId(mysqli $con)
{
    $query = "SELECT MAX(MaMonHoc) AS max_id FROM MonHoc";
    $result = mysqli_query($con, $query);
    $row = mysqli_fetch_assoc($result);
    $max_id = $row['max_id'];

    // Tạo ID mới
    if ($max_id) {
        $num = intval(substr($max_id, strlen("MH"))) + 1;
    } else {
        $num = 1;
    }
    return "MH" . str_pad($num, 8, '0', STR_PAD_LEFT);
}

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        $maMonHoc = isset($_GET['MaMonHoc']) ? $_GET['MaMonHoc'] : '';
        if ($maMonHoc) {
            $query = "SELECT * FROM MonHoc WHERE MaMonHoc = '$maMonHoc'";
            $result = mysqli_query($con, $query);
            if (mysqli_num_rows($result) > 0) {
                $data = mysqli_fetch_assoc($result);
                $lopHocQuery = "SELECT DISTINCT lophoc.MaLopHoc, lophoc.TenLopHoc, lophoc.MaGiangVien, lophoc.ThoiKhoaBieu, lophoc.NgayBatDau, lophoc.NgayKetThuc, giangvien.HoTen as `TenGiangVien`, monhoc.TenMonHoc FROM lophoc JOIN giangvien ON giangvien.MaGiangVien = lophoc.MaGiangVien JOIN monhoc ON monhoc.MaMonHoc = lophoc.MaMonHoc WHERE monhoc.MaMonHoc = '$maMonHoc'";
                $lopHocQueryResult = mysqli_query($con, $lopHocQuery);
                $lopHocList = array_keys([]);
                $lopHocListCount = mysqli_num_rows($lopHocQueryResult);
                if ($lopHocListCount == 1) {
                    $lopHocList = [mysqli_fetch_assoc($lopHocQueryResult)];
                } else if ($lopHocListCount > 1) {
                    $lopHocList = mysqli_fetch_all($lopHocQueryResult, MYSQLI_ASSOC);
                }
                echo json_encode([
                    'status' => 'success',
                    'message' => 'Tìm thấy môn học',
                    'data' => $data,
                    'lopHocList' => $lopHocList
                ]);
            } else {
                echo json_encode(['status' => 'failed', 'message' => 'Không tìm thấy môn học']);
            }
        } else {
            // Lấy tham số từ $_GET
            $searchStr = isset($_GET['search']) ? $_GET['search'] : '';
            $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
            $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 10; // Số bản ghi mỗi trang
            $offset = ($page - 1) * $limit;

            $query = "SELECT DISTINCT * FROM MonHoc WHERE TenMonHoc LIKE '%$searchStr%' OR DanhMuc LIKE '%$searchStr%' LIMIT $limit OFFSET $offset";
            $result = mysqli_query($con, $query);
            $data = mysqli_fetch_all($result, MYSQLI_ASSOC);

            // Truy vấn tổng số bản ghi để tính tổng số trang
            $totalQuery = "SELECT COUNT(DISTINCT MaMonHoc) as total FROM MonHoc WHERE TenMonHoc LIKE '%$searchStr%' OR DanhMuc LIKE '%$searchStr%'";
            $totalResult = mysqli_query($con, $totalQuery);
            $totalRow = mysqli_fetch_assoc($totalResult);
            $totalRecords = intval($totalRow['total']);
            $totalPages = ceil($totalRecords / $limit);

            echo json_encode([
                'status' => 'success',
                'message' => 'Tìm thấy các môn học',
                'data' => $data,
                'pagination' => [
                    'current_page' => $page,
                    'total_pages' => $totalPages,
                    'total_records' => $totalRecords,
                    'limit' => $limit
                ]
            ]);
        }
        break;

    case 'POST':
        authenticateRequest($con);
        $tenMonHoc = $_POST['TenMonHoc'];
        $soTinChi = $_POST['SoTinChi'];
        $giaTien = $_POST['GiaTien'];
        $danhMuc = $_POST['DanhMuc'];

        // Kiểm tra số điện thoại đã tồn tại hay chưa
        $checkTenMonHocQuery = "SELECT * FROM MonHoc WHERE TenMonHoc = '$tenMonHoc'";
        $tenMonHocResult = mysqli_query($con, $checkTenMonHocQuery);

        if (mysqli_num_rows($tenMonHocResult) > 0) {
            echo json_encode(['status' => 'failed', 'message' => 'Tên môn học đã tồn tại']);
            break;
        }

        $maMonHoc = generateId($con);
        $query = "INSERT INTO MonHoc (MaMonHoc, TenMonHoc, GiaTien, SoTinChi, DanhMuc) VALUES ('$maMonHoc', '$tenMonHoc', '$giaTien', $soTinChi, '$danhMuc')";

        if (mysqli_query($con, $query)) {
            echo json_encode(['status' => 'success', 'message' => 'Thêm môn học thành công']);
        } else {
            echo json_encode(['status' => 'failed', 'message' => 'Thêm môn học thất bại']);
        }
        break;

    case 'PUT':
        authenticateRequest($con);
        parse_str(file_get_contents("php://input"), $_PUT);
        $maMonHoc = $_PUT['MaMonHoc'];
        $tenMonHoc = $_PUT['TenMonHoc'];
        $soTinChi = $_PUT['SoTinChi'];
        $giaTien = $_PUT['GiaTien'];
        $danhMuc = $_PUT['DanhMuc'];

        // Bước 1: Truy vấn thông tin môn học hiện tại
        $query = "SELECT TenMonHoc FROM MonHoc WHERE MaMonHoc = '$maMonHoc'";
        $result = mysqli_query($con, $query);

        if ($result) {
            $currentData = mysqli_fetch_assoc($result);
            $currentTenMonHoc = $currentData['TenMonHoc'];

            // Bước 2: So sánh TenMonHoc
            if ($tenMonHoc !== $currentTenMonHoc) {
                // Bước 3: Kiểm tra xem TenMonHoc mới đã tồn tại chưa
                $query = "SELECT MaMonHoc FROM MonHoc WHERE TenMonHoc = '$tenMonHoc' AND MaMonHoc != '$maMonHoc'";
                $result = mysqli_query($con, $query);
                if (mysqli_num_rows($result) > 0) {
                    echo json_encode(['status' => 'failed', 'message' => 'Tên môn học đã tồn tại trong cơ sở dữ liệu']);
                    break;
                }
            }

            // Bước 4: Cập nhật thông tin môn học nếu không có trùng lặp
            $query = "UPDATE MonHoc SET TenMonHoc = '$tenMonHoc', GiaTien = '$giaTien', SoTinChi = $soTinChi, DanhMuc = '$danhMuc' WHERE MaMonHoc = '$maMonHoc'";

            if (mysqli_query($con, $query)) {
                echo json_encode(['status' => 'success', 'message' => 'Cập nhật môn học thành công']);
            } else {
                echo json_encode(['status' => 'failed', 'message' => 'Cập nhật môn học thất bại']);
            }
        } else {
            echo json_encode(['status' => 'failed', 'message' => 'Không tìm thấy môn học']);
        }
        break;

    case 'DELETE':
        authenticateRequest($con);
        $header = apache_request_headers();
        $maMonHoc = isset($header['MaMonHoc']) ? $header['MaMonHoc'] : "";

        if ($maMonHoc) {
            $query = "DELETE FROM MonHoc WHERE MaMonHoc = '$maMonHoc'";

            if (mysqli_query($con, $query)) {
                echo json_encode(['status' => 'success', 'message' => 'Xóa môn học thành công']);
            } else {
                echo json_encode(['status' => 'failed', 'message' => 'Xóa môn học thất bại']);
            }
        } else {
            echo json_encode(['status' => 'failed', 'message' => 'Không thấy mã môn học']);
        }
        break;

    default:
        echo json_encode(['status' => 'failed', 'message' => 'Yêu cầu không hơp lệ']);
        break;
}

mysqli_close($con);

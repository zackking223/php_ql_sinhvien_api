<?php
header('Content-Type: application/json');
function authenticateRequest($con)
{
    // Kiểm tra method
    $method = $_SERVER['REQUEST_METHOD'];

    // Xử lý dữ liệu từ method
    if ($method == 'POST' || $method == 'PUT') {
        $data = json_decode(file_get_contents('php://input'), true);
    } else {
        $data = $_GET;
    }

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
authenticateRequest($con);

// Hàm để tạo mã khóa chính mới
function generateId($prefix, $table, $con)
{
    $query = "SELECT MAX(Ma$table) AS max_id FROM $table";
    $result = mysqli_query($con, $query);
    $row = mysqli_fetch_assoc($result);
    $max_id = $row['max_id'];

    // Tạo ID mới
    if ($max_id) {
        $num = intval(substr($max_id, strlen($prefix))) + 1;
    } else {
        $num = 1;
    }
    return $prefix . str_pad($num, 8, '0', STR_PAD_LEFT);
}

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        $id = isset($_GET['id']) ? $_GET['id'] : '';
        if ($id) {
            $query = "SELECT * FROM TaiKhoan WHERE id = '$id'";
            $result = mysqli_query($con, $query);
            if (mysqli_num_rows($result) > 0) {
                $data = mysqli_fetch_assoc($result);
                echo json_encode(['status' => 'success', 'data' => $data]);
            } else {
                echo json_encode(['status' => 'failed', 'message' => 'No account found']);
            }
        } else {
            $loaiTaiKhoan = isset($_GET["LoaiTaiKhoan"]) ? $_GET["LoaiTaiKhoan"] : "";
            $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 10; // Number of records per page
            $page = isset($_GET['page']) ? (int)$_GET['page'] : 1; // Current page number
            $offset = ($page - 1) * $limit; // Offset calculation

            // Base query
            $query = "SELECT id, email, LoaiTaiKhoan, MaSinhVien, MaGiangVien, name FROM TaiKhoan";
            $countQuery = "SELECT COUNT(*) as total FROM TaiKhoan";

            if ($loaiTaiKhoan == "SinhVien") {
                $query = "SELECT id, email, LoaiTaiKhoan, MaSinhVien, name FROM TaiKhoan WHERE LoaiTaiKhoan = '$loaiTaiKhoan'";
                $countQuery = "SELECT COUNT(*) as total FROM TaiKhoan WHERE LoaiTaiKhoan = '$loaiTaiKhoan'";
            } else if ($loaiTaiKhoan == "GiangVien") {
                $query = "SELECT id, email, LoaiTaiKhoan, MaGiangVien, name FROM TaiKhoan WHERE LoaiTaiKhoan = '$loaiTaiKhoan'";
                $countQuery = "SELECT COUNT(*) as total FROM TaiKhoan WHERE LoaiTaiKhoan = '$loaiTaiKhoan'";
            } else if ($loaiTaiKhoan == "QuanTriVien") {
                $query = "SELECT id, email, LoaiTaiKhoan, name FROM TaiKhoan WHERE LoaiTaiKhoan = '$loaiTaiKhoan'";
                $countQuery = "SELECT COUNT(*) as total FROM TaiKhoan WHERE LoaiTaiKhoan = '$loaiTaiKhoan'";
            }

            // Query to get the total number of records
            $total_result = mysqli_query($con, $countQuery);
            $total_row = mysqli_fetch_assoc($total_result);
            $total_records = intval($total_row['total']);

            // Add pagination to the main query
            $query .= " LIMIT $limit OFFSET $offset";
            $result = mysqli_query($con, $query);
            $data = mysqli_fetch_all($result, MYSQLI_ASSOC);

            // Calculate total pages
            $total_pages = ceil($total_records / $limit);

            // Return JSON response
            echo json_encode([
                'status' => 'success',
                'message' => 'All accounts found',
                'data' => $data,
                'pagination' => [
                    'current_page' => $page,
                    'total_pages' => $total_pages,
                    'total_records' => $total_records,
                    'limit' => $limit
                ]
            ]);
        }
        break;

    case 'POST':
        $email = $_POST['email'];
        $password = $_POST['password'];
        $apiKey = $_POST['apiKey'];
        $loaiTaiKhoan = $_POST['LoaiTaiKhoan'];

        // Check if email already exists
        $query = "SELECT * FROM TaiKhoan WHERE email = '$email'";
        $result = mysqli_query($con, $query);

        if (mysqli_num_rows($result) > 0) {
            echo json_encode(['status' => 'failed', 'message' => 'Email đã tồn tại']);
            exit();
        }

        // Variables for FK
        $maSinhVien = null;
        $maGiangVien = null;

        // Additional checks for phone numbers
        $soDienThoai = isset($_POST['SoDienThoai']) ? $_POST['SoDienThoai'] : '';
        if ($loaiTaiKhoan == 'SinhVien') {
            $query = "SELECT * FROM SinhVien WHERE SoDienThoai = '$soDienThoai'";
        } elseif ($loaiTaiKhoan == 'GiangVien') {
            $query = "SELECT * FROM GiangVien WHERE SoDienThoai = '$soDienThoai'";
        }

        if (isset($query)) {
            $result = mysqli_query($con, $query);
            if (mysqli_num_rows($result) > 0) {
                echo json_encode(['status' => 'failed', 'message' => 'Số điện thoại đã tồn tại']);
                exit();
            }
        }
        $hoTen = "Admin";

        // Insert into SinhVien or GiangVien if needed
        if ($loaiTaiKhoan == 'SinhVien') {
            $hoTen = $_POST['HoTen'];
            $ngaySinh = $_POST['NgaySinh'];
            $gioiTinh = $_POST['GioiTinh'];
            $diaChi = $_POST['DiaChi'];

            $id = generateId('SV', 'SinhVien', $con);
            $query = "INSERT INTO SinhVien (MaSinhVien, HoTen, NgaySinh, GioiTinh, DiaChi, SoDienThoai, Email) VALUES ('$id','$hoTen', '$ngaySinh', '$gioiTinh', '$diaChi', '$soDienThoai', '$email')";
            if (mysqli_query($con, $query)) {
                $maSinhVien = mysqli_insert_id($con);
            } else {
                echo json_encode(['status' => 'failed', 'message' => 'Failed to add student']);
                exit();
            }
        } elseif ($loaiTaiKhoan == 'GiangVien') {
            $hoTen = $_POST['HoTen'];
            $soDienThoai = $_POST['SoDienThoai'];
            $diaChi = $_POST['DiaChi'];
            $id = generateId('GV', 'GiangVien', $con);
            $query = "INSERT INTO GiangVien (MaGiangVien, HoTen, Email, SoDienThoai, DiaChi) VALUES ('$id', '$hoTen', '$email', '$soDienThoai', '$diaChi')";
            if (mysqli_query($con, $query)) {
                $maGiangVien = mysqli_insert_id($con);
            } else {
                echo json_encode(['status' => 'failed', 'message' => 'Failed to add lecturer']);
                exit();
            }
        }

        // Insert into TaiKhoan
        $query = "INSERT INTO TaiKhoan (email, name, password, apiKey, LoaiTaiKhoan, MaSinhVien, MaGiangVien) VALUES ('$email', '$hoTen', '$password', '$apiKey', '$loaiTaiKhoan', '$maSinhVien', '$maGiangVien')";

        if (mysqli_query($con, $query)) {
            echo json_encode(['status' => 'success', 'message' => 'Account added successfully']);
        } else {
            echo json_encode(['status' => 'failed', 'message' => 'Failed to add account']);
        }
        break;

    case 'PUT':
        parse_str(file_get_contents("php://input"), $_PUT);
        $id = $_PUT['id'];
        $email = $_PUT['email'];
        $password = $_PUT['password'];
        $apiKey = $_PUT['apiKey'];
        $loaiTaiKhoan = $_PUT['LoaiTaiKhoan'];
        $maSinhVien = $_PUT['MaSinhVien'];
        $maGiangVien = $_PUT['MaGiangVien'];
        $soDienThoai = isset($_PUT['SoDienThoai']) ? $_PUT['SoDienThoai'] : '';

        // Check if email already exists (excluding the current ID)
        $query = "SELECT * FROM TaiKhoan WHERE email = '$email' AND id != '$id'";
        $result = mysqli_query($con, $query);
        if (mysqli_num_rows($result) > 0) {
            echo json_encode(['status' => 'failed', 'message' => 'Email already exists']);
            exit();
        }

        // Additional checks for phone numbers
        if ($loaiTaiKhoan == 'SinhVien') {
            $query = "SELECT * FROM SinhVien WHERE SoDienThoai = '$soDienThoai' AND MaSinhVien != '$maSinhVien'";
        } elseif ($loaiTaiKhoan == 'GiangVien') {
            $query = "SELECT * FROM GiangVien WHERE SoDienThoai = '$soDienThoai' AND MaGiangVien != '$maGiangVien'";
        }

        if (isset($query)) {
            $result = mysqli_query($con, $query);
            if (mysqli_num_rows($result) > 0) {
                echo json_encode(['status' => 'failed', 'message' => 'Phone number already exists']);
                exit();
            }
        }

        // Update TaiKhoan
        $query = "UPDATE TaiKhoan SET email = '$email', password = '$password', apiKey = '$apiKey', LoaiTaiKhoan = '$loaiTaiKhoan', MaSinhVien = '$maSinhVien', MaGiangVien = '$maGiangVien' WHERE id = '$id'";

        if (mysqli_query($con, $query)) {
            echo json_encode(['status' => 'success', 'message' => 'Account updated successfully']);
        } else {
            echo json_encode(['status' => 'failed', 'message' => 'Failed to update account']);
        }
        break;

    case 'DELETE':
        parse_str(file_get_contents("php://input"), $_DELETE);
        $id = $_DELETE['id'];

        $query = "DELETE FROM TaiKhoan WHERE id = '$id'";

        if (mysqli_query($con, $query)) {
            echo json_encode(['status' => 'success', 'message' => 'Account deleted successfully']);
        } else {
            echo json_encode(['status' => 'failed', 'message' => 'Failed to delete account']);
        }
        break;

    default:
        echo json_encode(['status' => 'failed', 'message' => 'Invalid request']);
        break;
}

mysqli_close($con);

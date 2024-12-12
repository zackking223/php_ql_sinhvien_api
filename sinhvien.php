<?php
$defaultPassword = "\$2y\$10\$mlHaq1/3RBGGzTkRWGsF3.GrHdjIjHCRwZGs.2bAwo7b9FFNt03yO";
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
    $query = "SELECT MAX(MaSinhVien) AS max_id FROM SinhVien";
    $result = mysqli_query($con, $query);
    $row = mysqli_fetch_assoc($result);
    $max_id = $row['max_id'];

    // Tạo ID mới
    if ($max_id) {
        $num = intval(substr($max_id, strlen("SV"))) + 1;
    } else {
        $num = 1;
    }
    return "SV" . str_pad($num, 3, '0', STR_PAD_LEFT);
}

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        $maSinhVien = isset($_GET['MaSinhVien']) ? $_GET['MaSinhVien'] : '';
        $notMaLopHoc = isset($_GET['notin']) ? $_GET['notin'] : '';

        if ($maSinhVien) {
            $query = "SELECT * FROM SinhVien WHERE MaSinhVien = '$maSinhVien'";
            $result = mysqli_query($con, $query);
            if (mysqli_num_rows($result) > 0) {
                $data = mysqli_fetch_assoc($result);
                $lopHocQuery = "SELECT DISTINCT lophoc.MaLopHoc, lophoc.TenLopHoc, lophoc.MaGiangVien, lophoc.ThoiKhoaBieu, lophoc.NgayBatDau, lophoc.NgayKetThuc, giangvien.HoTen as `TenGiangVien`, monhoc.TenMonHoc FROM lophoc JOIN phanconglophoc ON lophoc.malophoc = phanconglophoc.malophoc JOIN giangvien ON giangvien.MaGiangVien = lophoc.MaGiangVien JOIN monhoc ON monhoc.MaMonHoc = lophoc.MaMonHoc WHERE phanconglophoc.MaSinhVien = '$maSinhVien'";
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
                    'message' => 'Student found',
                    'data' => $data,
                    'lopHocList' => $lopHocList
                ]);
            } else {
                echo json_encode(['status' => 'failed', 'message' => 'No student found']);
            }
        } else {
            // Lấy tham số từ $_GET
            $searchStr = isset($_GET['search']) ? $_GET['search'] : '';
            $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
            $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 10; // Số bản ghi mỗi trang
            $offset = ($page - 1) * $limit;

            // Câu truy vấn với LIMIT và OFFSET để phân trang
            $query = "SELECT DISTINCT * FROM SinhVien WHERE Email LIKE '%$searchStr%' OR HoTen LIKE '%$searchStr%' OR MaSinhVien LIKE '%$searchStr%' LIMIT $limit OFFSET $offset";
            if ($notMaLopHoc) {
                $query = "SELECT DISTINCT * FROM SinhVien WHERE (Email LIKE '%$searchStr%' OR HoTen LIKE '%$searchStr%' OR MaSinhVien LIKE '%$searchStr%') AND MaSinhVien NOT IN ( SELECT MaSinhVien FROM PhanCongLopHoc WHERE MaLopHoc = '$notMaLopHoc' ) LIMIT $limit OFFSET $offset";
            }
            $result = mysqli_query($con, $query);

            // Kiểm tra kết quả
            if ($result) {
                $data = mysqli_fetch_all($result, MYSQLI_ASSOC);

                // Truy vấn tổng số bản ghi để tính tổng số trang
                $totalQuery = "SELECT COUNT(DISTINCT MaSinhVien) as total FROM SinhVien WHERE Email LIKE '%$searchStr%' OR HoTen LIKE '%$searchStr%' OR MaSinhVien LIKE '%$searchStr%'";
                if ($notMaLopHoc) {
                    $totalQuery = "SELECT COUNT(DISTINCT MaSinhVien) as total FROM SinhVien WHERE (Email LIKE '%$searchStr%' OR HoTen LIKE '%$searchStr%' OR MaSinhVien LIKE '%$searchStr%') AND MaSinhVien NOT IN ( SELECT MaSinhVien FROM PhanCongLopHoc WHERE MaLopHoc = '$notMaLopHoc' )";
                }
                $totalResult = mysqli_query($con, $totalQuery);
                $totalRow = mysqli_fetch_assoc($totalResult);
                $totalRecords = intval($totalRow['total']);
                $totalPages = ceil($totalRecords / $limit);

                // Trả về dữ liệu dạng JSON
                echo json_encode([
                    'status' => 'success',
                    'message' => 'Tìm thấy tất cả sinh viên',
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
        $maSinhVien = generateId($con);
        $uploadPath = "";
        $imageUrl = "";
        if (isset($_POST['image']) && isset($_POST['imageName'])) {
            $image = explode(',', $_POST['image']);
            $base64String = $image[count($image) - 1];
            $imageName = $_POST['imageName'];

            // Decode the image
            $decodedImage = base64_decode($base64String);

            // Specify the directory to save the image
            $uploadPath = "sinhvienavatars/" . $maSinhVien . "_" . $imageName;

            // Save the image
            if (!file_put_contents($uploadPath, $decodedImage)) {
                unlink($uploadPath);
                echo json_encode(["status" => "failed", "message" => "Lỗi khi tải ảnh lên"]);
                exit();
            } else {
                $imageUrl = "/ql_sinhvien/sinhvienavatars/" . $maSinhVien . "_" . $imageName;
            }
        } else {
            echo json_encode(["status" => "failed", "message" => "Thiếu ảnh đại diện"]);
            exit();
        }

        $hoTen = $_POST['HoTen'];
        $ngaySinh = $_POST['NgaySinh'];
        $gioiTinh = $_POST['GioiTinh'];
        $diaChi = $_POST['DiaChi'];
        $soDienThoai = $_POST['SoDienThoai'];
        $email = $_POST['Email'];

        // Kiểm tra email đã tồn tại hay chưa
        $checkEmailQuery = "SELECT * FROM TaiKhoan WHERE email = '$email'";
        $emailResult = mysqli_query($con, $checkEmailQuery);

        if (mysqli_num_rows($emailResult) > 0) {
            echo json_encode(['status' => 'failed', 'message' => 'Email đã tồn tại']);
            break;
        }

        // Kiểm tra số điện thoại đã tồn tại hay chưa
        $checkPhoneQuery = "SELECT * FROM SinhVien WHERE SoDienThoai = '$soDienThoai'";
        $phoneResult = mysqli_query($con, $checkPhoneQuery);

        if (mysqli_num_rows($phoneResult) > 0) {
            echo json_encode(['status' => 'failed', 'message' => 'Số điện thoại đã tồn tại']);
            break;
        }

        if (strlen($soDienThoai) > 10) {
            echo json_encode(['status' => 'failed', 'message' => 'Số điện thoại quá dài']);
            break;
        }

        $query = "INSERT INTO SinhVien (MaSinhVien, HoTen, NgaySinh, GioiTinh, DiaChi, SoDienThoai, Email, ImageUrl) VALUES ('$maSinhVien', '$hoTen', '$ngaySinh', '$gioiTinh', '$diaChi', '$soDienThoai', '$email', '$imageUrl')";

        if (mysqli_query($con, $query)) {
            $query = "INSERT INTO taikhoan (email, password, apiKey, LoaiTaiKhoan, MaSinhVien, name) VALUES ('$email', '$defaultPassword', '', 'SinhVien', '$maSinhVien', '$hoTen')";
            if (mysqli_query($con, $query)) {
                echo json_encode(['status' => 'success', 'message' => 'Thêm sinh viên thành công']);
            } else {
                unlink($uploadPath);
                echo json_encode(['status' => 'failed', 'message' => 'Thêm tài khoản cho sinh viên thất bại']);
            }
        } else {
            unlink($uploadPath);
            echo json_encode(['status' => 'failed', 'message' => 'Thêm sinh viên thất bại']);
        }
        break;

    case 'PUT':
        authenticateRequest($con);
        parse_str(file_get_contents("php://input"), $_PUT);
        $maSinhVien = $_PUT['MaSinhVien'];
        $uploadPath = "";
        if (isset($_PUT['image']) && isset($_PUT['imageName']) && isset($_PUT['oldImageUrl'])) {
            $image = explode(',', $_PUT['image']);
            $base64String = $image[count($image) - 1];
            $imageName = $_PUT['imageName'];

            // Decode the image
            $decodedImage = base64_decode($base64String);

            // Specify the directory to save the image
            $uploadPath = "sinhvienavatars/" . $maSinhVien . "_" . $imageName;

            // Save the image
            if (!file_put_contents($uploadPath, $decodedImage)) {
                unlink($uploadPath);
                echo json_encode(["status" => "failed", "message" => "Lỗi khi tải ảnh lên"]);
                exit();
            } else {
                $oldImagePath = explode('ql_sinhvien/', $_PUT["oldImageUrl"])[1];
                if (!unlink($oldImagePath)) {
                    unlink($uploadPath);
                    echo json_encode(["status" => "failed", "message" => "Lỗi khi xóa ảnh cũ"]);
                    exit();
                } else {
                    $uploadPath = "/ql_sinhvien/" . $uploadPath;
                };
            }
        }
        $hoTen = $_PUT['HoTen'];
        $ngaySinh = $_PUT['NgaySinh'];
        $gioiTinh = $_PUT['GioiTinh'];
        $diaChi = $_PUT['DiaChi'];
        $soDienThoai = $_PUT['SoDienThoai'];
        $email = $_PUT['Email'];

        // Bước 1: Truy vấn thông tin sinh viên hiện tại
        $query = "SELECT Email, SoDienThoai FROM SinhVien WHERE MaSinhVien = '$maSinhVien'";
        $result = mysqli_query($con, $query);

        if ($result) {
            $currentData = mysqli_fetch_assoc($result);

            // Bước 2: So sánh Email và SoDienThoai
            $currentEmail = $currentData['Email'];
            $currentSoDienThoai = $currentData['SoDienThoai'];

            // Kiểm tra xem email mới khác với email hiện tại
            if ($email !== $currentEmail) {
                // Bước 3: Kiểm tra xem email mới đã tồn tại chưa
                $query = "SELECT MaSinhVien FROM SinhVien WHERE Email = '$email' AND MaSinhVien != '$maSinhVien'";
                $result = mysqli_query($con, $query);
                if (mysqli_num_rows($result) > 0) {
                    echo json_encode(['status' => 'failed', 'message' => 'Email đã tồn tại trong cơ sở dữ liệu']);
                    break;
                } else {
                    $query = "UPDATE TaiKhoan SET Email = '$email' WHERE MaSinhVien = '$maSinhVien'";
                    if (!mysqli_query($con, $query)) {
                        echo json_encode(['status' => 'failed', 'message' => 'Không thể cập nhật thông tin tài khoản']);
                        break;
                    }
                }
            }

            // Kiểm tra xem số điện thoại mới khác với số điện thoại hiện tại
            if ($soDienThoai !== $currentSoDienThoai) {
                // Bước 3: Kiểm tra xem số điện thoại mới đã tồn tại chưa
                $query = "SELECT MaSinhVien FROM SinhVien WHERE SoDienThoai = '$soDienThoai' AND MaSinhVien != '$maSinhVien'";
                $result = mysqli_query($con, $query);
                if (mysqli_num_rows($result) > 0) {
                    echo json_encode(['status' => 'failed', 'message' => 'Số điện thoại đã tồn tại trong cơ sở dữ liệu']);
                    break;
                }
            }

            // Bước 4: Cập nhật thông tin sinh viên nếu không có trùng lặp
            if ($uploadPath) {
                $query = "UPDATE SinhVien SET HoTen = '$hoTen', NgaySinh = '$ngaySinh', GioiTinh = '$gioiTinh', DiaChi = '$diaChi', SoDienThoai = '$soDienThoai', Email = '$email', ImageUrl = '$uploadPath' WHERE MaSinhVien = '$maSinhVien'";
            } else {
                $query = "UPDATE SinhVien SET HoTen = '$hoTen', NgaySinh = '$ngaySinh', GioiTinh = '$gioiTinh', DiaChi = '$diaChi', SoDienThoai = '$soDienThoai', Email = '$email' WHERE MaSinhVien = '$maSinhVien'";
            }

            if (mysqli_query($con, $query)) {
                echo json_encode(['status' => 'success', 'message' => 'Student updated successfully']);
            } else {
                echo json_encode(['status' => 'failed', 'message' => 'Failed to update student']);
            }
        } else {
            echo json_encode(['status' => 'failed', 'message' => 'Không tìm thấy sinh viên']);
        }
        break;

    case 'DELETE':
        authenticateRequest($con);
        $headers = apache_request_headers();
        $maSinhVien = isset($headers['MaSinhVien']) ? $headers['MaSinhVien'] : "";

        if ($maSinhVien != "") {
            $query = "DELETE FROM SinhVien WHERE MaSinhVien = '$maSinhVien'";
            if (mysqli_query($con, $query)) {
                echo json_encode(['status' => 'success', 'message' => 'Xóa sinh viên thành công']);
            } else {
                echo json_encode(['status' => 'failed', 'message' => 'Xóa sinh viên thất bại']);
            }
        } else {
            echo json_encode(['status' => 'failed', 'message' => 'Không thấy mã của sinh viên']);
        }
        break;

    default:
        echo json_encode(['status' => 'failed', 'message' => 'Invalid request']);
        break;
}

mysqli_close($con);

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
        if ($loaiTaiKhoan == 'QuanTriVien') {
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
    $query = "SELECT MAX(MaGiangVien) AS max_id FROM GiangVien";
    $result = mysqli_query($con, $query);
    $row = mysqli_fetch_assoc($result);
    $max_id = $row['max_id'];

    // Tạo ID mới
    if ($max_id) {
        $num = intval(substr($max_id, strlen("GV"))) + 1;
    } else {
        $num = 1;
    }
    return "GV" . str_pad($num, 3, '0', STR_PAD_LEFT);
}

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        $maGiangVien = isset($_GET['MaGiangVien']) ? $_GET['MaGiangVien'] : '';
        if ($maGiangVien) {
            $query = "SELECT * FROM GiangVien WHERE MaGiangVien = '$maGiangVien'";
            $result = mysqli_query($con, $query);
            if (mysqli_num_rows($result) > 0) {
                $data = mysqli_fetch_assoc($result);
                $lopHocQuery = "SELECT DISTINCT lophoc.MaLopHoc, lophoc.TenLopHoc, lophoc.MaGiangVien, lophoc.ThoiKhoaBieu, lophoc.NgayBatDau, lophoc.NgayKetThuc, giangvien.HoTen as `TenGiangVien`, monhoc.TenMonHoc FROM lophoc JOIN giangvien ON giangvien.MaGiangVien = lophoc.MaGiangVien JOIN monhoc ON monhoc.MaMonHoc = lophoc.MaMonHoc WHERE giangvien.MaGiangVien = '$maGiangVien'";
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
                    'message' => 'Tìm thấy giảng viên',
                    'data' => $data,
                    'lopHocList' => $lopHocList
                ]);
            } else {
                echo json_encode([
                    'status' => 'failed',
                    'message' => 'Không tìm thấy giảng viên'
                ]);
            }
        } else {
            // Lấy tham số từ $_GET
            $searchStr = isset($_GET['search']) ? $_GET['search'] : '';
            $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
            $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 10; // Số bản ghi mỗi trang
            $offset = ($page - 1) * $limit;

            // Câu truy vấn với LIMIT và OFFSET để phân trang
            $query = "SELECT DISTINCT * FROM GiangVien WHERE HoTen LIKE '%$searchStr%' OR MaGiangVien LIKE '%$searchStr%' OR Email LIKE '%$searchStr%' LIMIT $limit OFFSET $offset";
            $result = mysqli_query($con, $query);

            // Kiểm tra kết quả
            if ($result) {
                $data = mysqli_fetch_all($result, MYSQLI_ASSOC);

                // Truy vấn tổng số bản ghi để tính tổng số trang
                $totalQuery = "SELECT COUNT(DISTINCT MaGiangVien) as total FROM GiangVien WHERE HoTen LIKE '%$searchStr%' OR MaGiangVien LIKE '%$searchStr%' OR Email LIKE '%$searchStr%'";
                $totalResult = mysqli_query($con, $totalQuery);
                $totalRow = mysqli_fetch_assoc($totalResult);
                $totalRecords = intval($totalRow['total']);
                $totalPages = ceil($totalRecords / $limit);

                // Trả về dữ liệu dạng JSON
                echo json_encode([
                    'status' => 'success',
                    'message' => 'Tìm thấy các giảng viên',
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
        $maGiangVien = generateId($con);
        $uploadPath = "";
        $imageUrl = "";
        if (isset($_POST['image']) && isset($_POST['imageName'])) {
            $image = explode(',', $_POST['image']);
            $base64String = $image[count($image) - 1];
            $imageName = $_POST['imageName'];

            // Decode the image
            $decodedImage = base64_decode($base64String);

            // Specify the directory to save the image
            $uploadPath = "giangvienavatars/" . $maGiangVien . "_" . $imageName;

            // Save the image
            if (!file_put_contents($uploadPath, $decodedImage)) {
                unlink($uploadPath);
                echo json_encode(["status" => "failed", "message" => "Lỗi khi tải ảnh lên"]);
                exit();
            } else {
                $imageUrl = "/ql_sinhvien/giangvienavatars/" . $maGiangVien . "_" . $imageName;
            }
        } else {
            echo json_encode(["status" => "failed", "message" => "Thiếu ảnh đại diện"]);
            exit();
        }

        $hoTen = $_POST['HoTen'];
        $email = $_POST['Email'];
        $soDienThoai = $_POST['SoDienThoai'];
        $diaChi = $_POST['DiaChi'];

        // Kiểm tra email đã tồn tại hay chưa
        $checkEmailQuery = "SELECT * FROM TaiKhoan WHERE email = '$email'";
        $emailResult = mysqli_query($con, $checkEmailQuery);

        if (mysqli_num_rows($emailResult) > 0) {
            echo json_encode(['status' => 'failed', 'message' => 'Email đã tồn tại']);
            break;
        }

        // Kiểm tra số điện thoại đã tồn tại hay chưa
        $checkPhoneQuery = "SELECT * FROM GiangVien WHERE SoDienThoai = '$soDienThoai'";
        $phoneResult = mysqli_query($con, $checkPhoneQuery);

        if (mysqli_num_rows($phoneResult) > 0) {
            echo json_encode(['status' => 'failed', 'message' => 'Số điện thoại đã tồn tại']);
            break;
        }


        $query = "INSERT INTO GiangVien (MaGiangVien, HoTen, Email, SoDienThoai, DiaChi) VALUES ('$maGiangVien', '$hoTen', '$email', '$soDienThoai', '$diaChi')";

        if (mysqli_query($con, $query)) {
            echo json_encode(['status' => 'success', 'message' => 'Thêm giảng viên thành công']);
        } else {
            echo json_encode(['status' => 'failed', 'message' => 'Thêm giảng viên thất bại']);
        }
        break;

    case 'PUT':
        authenticateRequest($con);
        parse_str(file_get_contents("php://input"), $_PUT);
        $maGiangVien = $_PUT['MaGiangVien'];
        $uploadPath = "";
        if (isset($_PUT['image']) && isset($_PUT['imageName']) && isset($_PUT['oldImageUrl'])) {
            $image = explode(',', $_PUT['image']);
            $base64String = $image[count($image) - 1];
            $imageName = $_PUT['imageName'];

            // Decode the image
            $decodedImage = base64_decode($base64String);

            // Specify the directory to save the image
            $uploadPath = "giangvienavatars/" . $maGiangVien . "_" . $imageName;

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
        $email = $_PUT['Email'];
        $soDienThoai = $_PUT['SoDienThoai'];
        $diaChi = $_PUT['DiaChi'];

        // Bước 1: Truy vấn thông tin giảng viên hiện tại
        $query = "SELECT Email, SoDienThoai FROM GiangVien WHERE MaGiangVien = '$maGiangVien'";
        $result = mysqli_query($con, $query);

        if ($result) {
            $currentData = mysqli_fetch_assoc($result);

            // Bước 2: So sánh Email và SoDienThoai
            $currentEmail = $currentData['Email'];
            $currentSoDienThoai = $currentData['SoDienThoai'];

            // Kiểm tra xem email mới khác với email hiện tại
            if ($email !== $currentEmail) {
                // Bước 3: Kiểm tra xem email mới đã tồn tại chưa
                $query = "SELECT MaGiangVien FROM GiangVien WHERE Email = '$email' AND MaGiangVien != '$maGiangVien'";
                $result = mysqli_query($con, $query);
                if (mysqli_num_rows($result) > 0) {
                    echo json_encode(['status' => 'failed', 'message' => 'Email đã tồn tại trong cơ sở dữ liệu']);
                    break;
                } else {
                    $query = "UPDATE TaiKhoan SET Email = '$email' WHERE MaGiangVien = '$maGiangVien'";
                    if (!mysqli_query($con, $query)) {
                        echo json_encode(['status' => 'failed', 'message' => 'Không thể cập nhật thông tin tài khoản']);
                        break;
                    }
                }
            }

            // Kiểm tra xem số điện thoại mới khác với số điện thoại hiện tại
            if ($soDienThoai !== $currentSoDienThoai) {
                // Bước 3: Kiểm tra xem số điện thoại mới đã tồn tại chưa
                $query = "SELECT MaGiangVien FROM GiangVien WHERE SoDienThoai = '$soDienThoai' AND MaGiangVien != '$maGiangVien'";
                $result = mysqli_query($con, $query);
                if (mysqli_num_rows($result) > 0) {
                    echo json_encode(['status' => 'failed', 'message' => 'Số điện thoại đã tồn tại trong cơ sở dữ liệu']);
                    break;
                }
            }

            // Bước 4: Cập nhật thông tin giảng viên nếu không có trùng lặp
            if ($uploadPath) {
                $query = "UPDATE GiangVien SET HoTen = '$hoTen', Email = '$email', SoDienThoai = '$soDienThoai', DiaChi = '$diaChi', ImageUrl = '$uploadPath' WHERE MaGiangVien = '$maGiangVien'";
            } else {
                $query = "UPDATE GiangVien SET HoTen = '$hoTen', Email = '$email', SoDienThoai = '$soDienThoai', DiaChi = '$diaChi' WHERE MaGiangVien = '$maGiangVien'";
            }

            if (mysqli_query($con, $query)) {
                echo json_encode(['status' => 'success', 'message' => 'Cập nhật giảng viên thành công']);
            } else {
                echo json_encode(['status' => 'failed', 'message' => 'Cập nhật giảng viên thất bại']);
            }
        } else {
            echo json_encode(['status' => 'failed', 'message' => 'Không tìm thấy giảng viên']);
        }
        break;

    case 'DELETE':
        authenticateRequest($con);
        $header = apache_request_headers();
        $maGiangVien = isset($_header['MaGiangVien']) ? $_header['MaGiangVien'] : '';

        if ($maGiangVien) {
            $query = "DELETE FROM GiangVien WHERE MaGiangVien = '$maGiangVien'";

            if (mysqli_query($con, $query)) {
                echo json_encode(['status' => 'success', 'message' => 'Xóa giảng viên thành công']);
            } else {
                echo json_encode(['status' => 'failed', 'message' => 'Xóa giảng viên thất bại']);
            }
        } else {
            echo json_encode(['status' => 'failed', 'message' => 'Không thấy mã giảng viên']);
        }

        break;

    default:
        echo json_encode(['status' => 'failed', 'message' => 'Yêu cầu không hợp lệ']);
        break;
}

mysqli_close($con);

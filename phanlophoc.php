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
            // Nếu LoaiTaiKhoan là QuanTriVien hoặc GiangVien, tiếp tục
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
authenticateRequest($con);

if (!$con) {
    echo json_encode(['status' => 'failed', 'message' => 'Connection failed']);
    exit();
}

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        $maLopHoc = isset($_GET['MaLopHoc']) ? $_GET['MaLopHoc'] : '';
        if ($maLopHoc) {
            $query = "SELECT * FROM PhanCongLopHoc WHERE MaLopHoc = '$maLopHoc'";
            $result = mysqli_query($con, $query);
            if (mysqli_num_rows($result) > 0) {
                $data = mysqli_fetch_assoc($result);
                echo json_encode(['status' => 'success', 'data' => $data]);
            } else {
                echo json_encode(['status' => 'failed', 'message' => 'No assignment found']);
            }
        } else {
            $query = "SELECT * FROM PhanCongLopHoc";
            $result = mysqli_query($con, $query);
            $data = mysqli_fetch_all($result, MYSQLI_ASSOC);
            echo json_encode(['status' => 'success', 'data' => $data]);
        }
        break;

    case 'POST':
        $maLopHoc = $_POST['MaLopHoc'];
        $maSinhVien = $_POST['MaSinhVien'];
        $MaMonHoc = $_POST['MaMonHoc'];

        // Kiểm tra xem lớp học đã đạt số lượng sinh viên tối đa chưa
        $checkLopHocQuery = "SELECT SinhVienToiDa, SinhVienDangKy FROM lophoc WHERE MaLopHoc = '$maLopHoc'";
        $checkLopHocResult = mysqli_query($con, $checkLopHocQuery);

        if ($checkLopHocResult && mysqli_num_rows($checkLopHocResult) > 0) {
            $lopHoc = mysqli_fetch_assoc($checkLopHocResult);
            $sinhVienToiDa = $lopHoc['SinhVienToiDa'];
            $sinhVienDangKy = $lopHoc['SinhVienDangKy'];

            // Kiểm tra nếu lớp học đã đủ số lượng sinh viên
            if ($sinhVienDangKy >= $sinhVienToiDa) {
                echo json_encode(['status' => 'failed', 'message' => 'Lớp học đã đầy']);
                exit();
            }
        } else {
            echo json_encode(['status' => 'failed', 'message' => 'Không tìm thấy lớp học']);
            exit();
        }

        // Kiểm tra xem sinh viên đã thuộc lớp học chưa
        $query = "SELECT * FROM phanconglophoc WHERE MaLopHoc = '$maLopHoc' AND MaSinhVien = '$maSinhVien'";
        $result = mysqli_query($con, $query);

        if (mysqli_num_rows($result) > 0) {
            echo json_encode(['status' => 'failed', 'message' => 'Sinh viên đã thuộc lớp học']);
            exit();
        }

        // Thêm sinh viên vào lớp học
        $query = "INSERT INTO PhanCongLopHoc (MaLopHoc, MaSinhVien) VALUES ('$maLopHoc', '$maSinhVien')";

        if (mysqli_query($con, $query)) {
            // Kiểm tra xem bản ghi điểm đã tồn tại chưa
            $checkSql = "SELECT * FROM chamdiem WHERE MaSinhVien='$maSinhVien' AND MaMonHoc='$MaMonHoc'";
            $checkResult = mysqli_query($con, $checkSql);
            $daCoDiem = mysqli_num_rows($checkResult) > 0;

            if ($daCoDiem) {
                // Cập nhật điểm cho sinh viên
                $sql = "UPDATE chamdiem SET DiemChuyenCan='0', DiemGiuaKy='0', DiemThi='0', TongKet='0' WHERE MaSinhVien='$maSinhVien' AND MaMonHoc='$MaMonHoc'";
            } else {
                // Thêm bản ghi điểm mới nếu chưa tồn tại
                $sql = "INSERT INTO chamdiem (MaSinhVien, MaMonHoc, DiemChuyenCan, DiemGiuaKy, DiemThi, TongKet) VALUES ('$maSinhVien', '$MaMonHoc', '0', '0', '0', '0')";
            }

            if (mysqli_query($con, $sql)) {
                // Sau khi thêm sinh viên vào lớp, đếm số lượng sinh viên thực tế trong lớp và cập nhật vào bảng lophoc
                $countQuery = "SELECT COUNT(*) AS totalSinhVien FROM phanconglophoc WHERE MaLopHoc = '$maLopHoc'";
                $countResult = mysqli_query($con, $countQuery);
                $row = mysqli_fetch_assoc($countResult);
                $totalSinhVien = $row['totalSinhVien'];

                // Cập nhật số lượng sinh viên trong lớp học
                $updateLopHocQuery = "UPDATE lophoc SET SinhVienDangKy = '$totalSinhVien' WHERE MaLopHoc = '$maLopHoc'";
                if (mysqli_query($con, $updateLopHocQuery)) {
                    if (updateStudentRecords($maSinhVien, $con)) {
                        echo json_encode(['status' => 'success', 'message' => 'Thêm vào lớp thành công']);
                    } else {
                        echo json_encode(['status' => 'failed', 'message' => 'Sửa điểm TBC thất bại']);
                    }
                } else {
                    echo json_encode(['status' => 'failed', 'message' => 'Không thể cập nhật số lượng sinh viên']);
                }
            } else {
                echo json_encode(['status' => 'failed', 'message' => 'Thêm vào lớp thất bại']);
            }
        } else {
            echo json_encode(['status' => 'failed', 'message' => 'Thêm vào lớp thất bại']);
        }
        break;

    case 'DELETE':
        $data = apache_request_headers();
        $maLopHoc = isset($data['MaLopHoc']) ? $data['MaLopHoc'] : '';
        $maSinhVien = isset($data['MaSinhVien']) ? $data['MaSinhVien'] : '';

        $query = "DELETE FROM PhanCongLopHoc WHERE MaLopHoc = '$maLopHoc' AND MaSinhVien = '$maSinhVien'";

        if (mysqli_query($con, $query)) {
            // Cập nhật số lượng sinh viên trong lớp học
            $updateLopHocQuery = "UPDATE lophoc SET SinhVienDangKy = SinhVienDangKy - 1 WHERE MaLopHoc = '$maLopHoc'";
            if (mysqli_query($con, $updateLopHocQuery)) {
                echo json_encode(['status' => 'success', 'message' => 'Xóa sinh viên khỏi lớp thành công']);
            } else {
                echo json_encode(['status' => 'failed', 'message' => 'Không thể cập nhật số lượng sinh viên']);
            }
        } else {
            echo json_encode(['status' => 'failed', 'message' => 'Xóa sinh viên khỏi lớp thất bại']);
        }
        break;

    default:
        echo json_encode(['status' => 'failed', 'message' => 'Phương thức không hợp lệ']);
        break;
}

function updateStudentRecords(string $maSinhVien, mysqli $con) : bool
{
    // Tính tổng số tín chỉ mà sinh viên đã qua (điểm >= 5)
    $query1 = "
      SELECT SUM(m.SoTinChi) AS tongTinChi
      FROM ChamDiem c
      JOIN MonHoc m ON c.MaMonHoc = m.MaMonHoc
      WHERE c.MaSinhVien = '$maSinhVien' AND c.TongKet >= 5
  ";
    $result1 = mysqli_query($con, $query1);
    if (!$result1) {
        return false; // Trả về false nếu truy vấn thất bại
    }
    $row1 = mysqli_fetch_assoc($result1);
    $tongTinChi = $row1['tongTinChi'] ?? 0;

    // Cập nhật TinChiTichLuy trong bảng SinhVien
    $updateTinChiQuery = "
      UPDATE SinhVien
      SET TinChiTichLuy = $tongTinChi
      WHERE MaSinhVien = '$maSinhVien'
  ";
    if (!mysqli_query($con, $updateTinChiQuery)) {
        return false; // Trả về false nếu truy vấn thất bại
    }

    // Tính tổng điểm có trọng số
    $query2 = "
      SELECT SUM(c.TongKet * m.SoTinChi) AS tongDiem, SUM(m.SoTinChi) AS tongTinChi
      FROM ChamDiem c
      JOIN MonHoc m ON c.MaMonHoc = m.MaMonHoc
      WHERE c.MaSinhVien = '$maSinhVien'
  ";
    $result2 = mysqli_query($con, $query2);
    if (!$result2) {
        return false; // Trả về false nếu truy vấn thất bại
    }
    $row2 = mysqli_fetch_assoc($result2);
    $tongDiem = $row2['tongDiem'] ?? 0;
    $tongTinChi = $row2['tongTinChi'] ?? 1; // Tránh chia cho 0

    // Tính DiemTBC mới
    $diemTBCMoi = $tongTinChi > 0 ? $tongDiem / $tongTinChi : 0;

    // Cập nhật DiemTBC và HocLuc trong bảng SinhVien
    $hocLuc = '';
    if ($diemTBCMoi >= 9.5) {
        $hocLuc = 'Xuất sắc';
    } elseif ($diemTBCMoi >= 8.0) {
        $hocLuc = 'Giỏi';
    } elseif ($diemTBCMoi >= 6.5) {
        $hocLuc = 'Khá';
    } elseif ($diemTBCMoi >= 5.0) {
        $hocLuc = 'Trung bình';
    } else {
        $hocLuc = 'Yếu';
    }

    $updateDiemTBCQuery = "
      UPDATE SinhVien
      SET DiemTBC = $diemTBCMoi,
          HocLuc = '$hocLuc'
      WHERE MaSinhVien = '$maSinhVien'
  ";
    if (!mysqli_query($con, $updateDiemTBCQuery)) {
        return false; // Trả về false nếu truy vấn thất bại
    }

    return true; // Trả về true nếu tất cả các truy vấn thành công
}

mysqli_close($con);

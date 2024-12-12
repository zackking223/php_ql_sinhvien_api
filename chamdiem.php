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
// Kết nối cơ sở dữ liệu
$con = mysqli_connect("localhost", "root", "", "ql_sinhvien");

if (!$con) {
  echo json_encode(['status' => 'failed', 'message' => 'Connection failed']);
  exit();
}

// Xác định phương thức HTTP
$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
  case 'GET':
    // Xử lý GET (Read)
    $MaSinhVien = isset($_GET['MaSinhVien']) ? mysqli_real_escape_string($con, $_GET['MaSinhVien']) : null;
    $MaMonHoc = isset($_GET['MaMonHoc']) ? mysqli_real_escape_string($con, $_GET['MaMonHoc']) : null;

    if ($MaSinhVien && $MaMonHoc) {
      // Nếu có cả MaSinhVien và MaMonHoc, trả về một bản ghi
      $sql = "SELECT chamdiem.MaSinhVien, chamdiem.MaMonHoc, chamdiem.DiemChuyenCan, chamdiem.DiemGiuaKy, chamdiem.DiemThi, chamdiem.TongKet, monhoc.TenMonHoc, monhoc.SoTinChi, sinhvien.HoTen FROM chamdiem JOIN monhoc ON chamdiem.MaMonHoc = monhoc.MaMonHoc JOIN sinhvien ON sinhvien.MaSinhVien = chamdiem.MaSinhVien WHERE chamdiem.MaSinhVien='$MaSinhVien' AND chamdiem.MaMonHoc='$MaMonHoc'";
      $result = mysqli_query($con, $sql);

      if ($result && mysqli_num_rows($result) > 0) {
        $data = mysqli_fetch_assoc($result); // Lấy một bản ghi
        echo json_encode([
          'status' => 'success',
          'message' => 'Tìm thấy điểm',
          'data' => $data
        ]);
      } else {
        echo json_encode(['status' => 'failed', 'message' => 'Không tìm thấy điểm']);
      }
    } else if ($MaSinhVien) {
      // Nếu không có MaMonHoc, phân trang và trả về tất cả bản ghi
      $searchStr = isset($_GET['search']) ? $_GET['search'] : '';
      $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 10; // Số bản ghi trên mỗi trang
      $page = isset($_GET['page']) ? (int)$_GET['page'] : 1; // Trang hiện tại

      $offset = ($page - 1) * $limit;

      // Tính tổng số bản ghi
      $countSql = "SELECT COUNT(*) AS total FROM chamdiem WHERE MaSinhVien = '$MaSinhVien'";
      $countResult = mysqli_query($con, $countSql);
      $totalRecords = intval(mysqli_fetch_assoc($countResult)['total']);
      $totalPages = ceil($totalRecords / $limit);

      // Truy vấn dữ liệu với phân trang
      $sql = "SELECT DISTINCT chamdiem.MaSinhVien, chamdiem.MaMonHoc, chamdiem.DiemChuyenCan, chamdiem.DiemGiuaKy, chamdiem.DiemThi, chamdiem.TongKet, monhoc.TenMonHoc, monhoc.SoTinChi, sinhvien.HoTen FROM chamdiem JOIN monhoc ON chamdiem.MaMonHoc = monhoc.MaMonHoc JOIN sinhvien ON sinhvien.MaSinhVien = chamdiem.MaSinhVien WHERE chamdiem.MaSinhVien = '$MaSinhVien' AND (monhoc.TenMonHoc LIKE '%$searchStr%' OR monhoc.MaMonHoc LIKE '%$searchStr%') LIMIT $limit OFFSET $offset";
      $result = mysqli_query($con, $sql);

      if ($result) {
        $data = mysqli_fetch_all($result, MYSQLI_ASSOC);
        echo json_encode([
          'status' => 'success',
          'message' => 'Tìm thấy tất cả điểm',
          'data' => $data,
          'pagination' => [
            'current_page' => $page,
            'total_pages' => $totalPages,
            'total_records' => $totalRecords,
            'limit' => $limit
          ]
        ]);
      } else {
        echo json_encode(['status' => 'failed', 'message' => 'Query failed']);
      }
    } else {
      echo json_encode(['status' => 'failed', 'message' => 'Thiếu mã cần thiết']);
    }
    break;

  case 'PUT':
    // Xử lý PUT (Update)
    parse_str(file_get_contents("php://input"), $_PUT);

    if (isset($_PUT['MaSinhVien'], $_PUT['MaMonHoc'], $_PUT['DiemChuyenCan'], $_PUT['DiemGiuaKy'], $_PUT['DiemThi'])) {
      $MaSinhVien = mysqli_real_escape_string($con, $_PUT['MaSinhVien']);
      $MaMonHoc = mysqli_real_escape_string($con, $_PUT['MaMonHoc']);
      $DiemChuyenCan = mysqli_real_escape_string($con, $_PUT['DiemChuyenCan']);
      $DiemGiuaKy = mysqli_real_escape_string($con, $_PUT['DiemGiuaKy']);
      $DiemThi = mysqli_real_escape_string($con, $_PUT['DiemThi']);

      // Kiểm tra giá trị của điểm
      if ($DiemChuyenCan < 0 || $DiemChuyenCan > 10 || $DiemGiuaKy < 0 || $DiemGiuaKy > 10 || $DiemThi < 0 || $DiemThi > 10) {
        echo json_encode(['status' => 'failed', 'message' => 'Điểm phải nằm trong khoảng từ 0 đến 10']);
        break;
      }

      // Tính điểm tổng kết
      $TongKet = ($DiemChuyenCan * 0.1) + ($DiemGiuaKy * 0.3) + ($DiemThi * 0.6);

      $sql = "UPDATE chamdiem SET DiemChuyenCan='$DiemChuyenCan', DiemGiuaKy='$DiemGiuaKy', DiemThi='$DiemThi', TongKet='$TongKet' 
                  WHERE MaSinhVien='$MaSinhVien' AND MaMonHoc='$MaMonHoc'";

      if (mysqli_query($con, $sql)) {
        if (updateStudentRecords($maSinhVien, $con)) {
          echo json_encode(['status' => 'success', 'message' => 'Sửa điểm thành công']);
        } else {
          echo json_encode(['status' => 'failed', 'message' => 'Sửa điểm TBC thất bại']);
        }
      } else {
        echo json_encode(['status' => 'failed', 'message' => 'Sửa điểm thất bại']);
      }
    } else {
      echo json_encode(['status' => 'failed', 'message' => $_PUT['MaSinhVien']]);
    }
    break;


  case 'DELETE':
    // Xử lý DELETE (Delete)
    $headers = apache_request_headers();

    if (isset($headers['MaSinhVien']) && isset($headers['MaMonHoc'])) {
      $MaSinhVien = mysqli_real_escape_string($con, $headers['MaSinhVien']);
      $MaMonHoc = mysqli_real_escape_string($con, $headers['MaMonHoc']);

      $sql = "DELETE FROM chamdiem WHERE chamdiem.MaSinhVien='$MaSinhVien' AND chamdiem.MaMonHoc='$MaMonHoc'";

      if (mysqli_query($con, $sql)) {
        echo json_encode(['status' => 'success', 'message' => 'Record deleted']);
      } else {
        echo json_encode(['status' => 'failed', 'message' => 'Query failed']);
      }
    } else {
      echo json_encode(['status' => 'failed', 'message' => 'Invalid headers']);
    }
    break;

  default:
    echo json_encode(['status' => 'failed', 'message' => 'Invalid request method']);
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

// Đóng kết nối cơ sở dữ liệu
mysqli_close($con);

<?php
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

  if (mysqli_fetch_assoc($result)) {
    return true;
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
$result = array();
$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
  case 'POST':
    if (isset($_POST["name"]) && authenticateRequest($con)) {
      $data = apache_request_headers();
      $email = $data['email'];
      $apiKey = $data['apiKey'];
      $name = $_POST["name"];

      if ($name == "") {
        echo json_encode(['status' => 'failed', 'message' => 'Tên mới không được bỏ trống!']);
        exit;
      }

      if (isset($_POST["password"])) {
        $password_hashed = password_hash($_POST["password"], PASSWORD_BCRYPT);
      }

      if ($con) {
        if (isset($_POST["password"])) {
          $query = "UPDATE taikhoan SET name = '$name', password = '$password_hashed' WHERE email = '$email' AND apiKey = '$apiKey'";
        } else {
          $query = "UPDATE taikhoan SET name = '$name' WHERE email = '$email' AND apiKey = '$apiKey'";
        }

        if (mysqli_query($con, $query)) {
          $result = array(
            "status" => "success",
            "message" => "Cập nhật thông tin thành công"
          );
        } else {
          $result = array(
            "status" => "failed",
            "message" => "Cập nhật thông tin thất bại"
          );
        }
      } else {
        $result = array(
          "status" => "failed",
          "message" => "Database connection failed"
        );
      }
    } else {
      $result = array(
        "status" => "failed",
        "message" => "Thông tin không được trống!"
      );
    }
    break;
  case 'DELETE':
    authenticateRequest($con);
    $data = apache_request_headers();
    $email = $data['email'];
    $apiKey = $data['apiKey'];

    // Kiểm tra tài khoản và xác định loại tài khoản
    $query = "SELECT LoaiTaiKhoan, MaGiangVien, MaSinhVien FROM TaiKhoan WHERE email = ? AND apiKey = ?";
    if ($stmt = mysqli_prepare($con, $query)) {
      mysqli_stmt_bind_param($stmt, 'ss', $email, $apiKey);
      mysqli_stmt_execute($stmt);
      mysqli_stmt_bind_result($stmt, $loaiTaiKhoan, $maGiangVien, $maSinhVien);
      mysqli_stmt_fetch($stmt);
      mysqli_stmt_close($stmt);

      if ($loaiTaiKhoan) {
        // Xóa tài khoản
        $query = "DELETE FROM TaiKhoan WHERE email = ? AND apiKey = ?";
        if ($stmt = mysqli_prepare($con, $query)) {
          mysqli_stmt_bind_param($stmt, 'ss', $email, $apiKey);
          mysqli_stmt_execute($stmt);
          mysqli_stmt_close($stmt);

          // Xóa GiangVien nếu tài khoản là GiangVien
          if ($loaiTaiKhoan === 'GiangVien' && $maGiangVien) {
            $query = "DELETE FROM GiangVien WHERE MaGiangVien = ?";
            if ($stmt = mysqli_prepare($con, $query)) {
              mysqli_stmt_bind_param($stmt, 's', $maGiangVien);
              mysqli_stmt_execute($stmt);
              mysqli_stmt_close($stmt);
            } else {
              $result = array(
                "status" => "failed",
                "message" => "Lỗi khi xóa GiangVien!"
              );
            }
          }

          // Xóa SinhVien nếu tài khoản là SinhVien
          if ($loaiTaiKhoan === 'SinhVien' && $maSinhVien) {
            $query = "DELETE FROM SinhVien WHERE MaSinhVien = ?";
            if ($stmt = mysqli_prepare($con, $query)) {
              mysqli_stmt_bind_param($stmt, 's', $maSinhVien);
              mysqli_stmt_execute($stmt);
              mysqli_stmt_close($stmt);
            } else {
              $result = array(
                "status" => "failed",
                "message" => "Lỗi khi xóa SinhVien!"
              );
            }
          }

          $result = array(
            "status" => "success",
            "message" => "Tài khoản đã được xóa thành công!" .
              ($loaiTaiKhoan === 'GiangVien' ? " GiangVien cũng đã được xóa!" : "") .
              ($loaiTaiKhoan === 'SinhVien' ? " SinhVien cũng đã được xóa!" : "")
          );
        } else {
          $result = array(
            "status" => "failed",
            "message" => "Lỗi khi xóa tài khoản!"
          );
        }
      } else {
        $result = array(
          "status" => "failed",
          "message" => "Tài khoản không hợp lệ hoặc không tồn tại!"
        );
      }
    } else {
      $result = array(
        "status" => "failed",
        "message" => "Lỗi khi kiểm tra tài khoản!"
      );
    }

    break;
}

echo json_encode($result, JSON_PRETTY_PRINT);
// Đóng kết nối
mysqli_close($con);

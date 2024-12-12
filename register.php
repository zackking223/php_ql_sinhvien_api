<?php
$result = array();
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

if (!empty($_POST["name"]) && !empty($_POST["email"]) && !empty($_POST["password"])) {
  $con = mysqli_connect(
    "localhost",
    "root",
    "",
    "ql_sinhvien"
  );

  $name = $_POST["name"];
  $email = $_POST["email"];
  $password_hashed = password_hash($_POST["password"], PASSWORD_BCRYPT);

  if ($con) {
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

    // Kiểm tra email đã tồn tại hay chưa
    $checkEmailQuery = "SELECT * FROM TaiKhoan WHERE email = '$email'";
    $emailResult = mysqli_query($con, $checkEmailQuery);

    if (mysqli_num_rows($emailResult) > 0) {
        echo json_encode(['status' => 'failed', 'message' => 'Email đã tồn tại']);
        exit;
    }

    $query = "INSERT INTO GiangVien (MaGiangVien, HoTen, Email, ImageUrl) VALUES ('$maGiangVien', '$name', '$email', '$imageUrl')";

    if (mysqli_query($con, $query)) {
      //Register logic
      $query = "INSERT INTO taikhoan (name, email, password, apiKey, loaitaikhoan) values ('" . $name . "','" . $email . "','" . $password_hashed . "','','GiangVien')";
      if (mysqli_query($con, $query)) {
        $result = array(
          "status" => "success",
          "message" => "Đăng ký thành công"
        );
      } else {
        $result = array(
          "status" => "failed",
          "message" => "Đăng ký thất bại"
        );
      }
    } else {
      echo json_encode(['status' => 'failed', 'message' => 'Thêm giảng viên thất bại']);
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
    "message" => "All fields are required"
  );
}

echo json_encode($result, JSON_PRETTY_PRINT);

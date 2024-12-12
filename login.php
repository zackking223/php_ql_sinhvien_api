<?php
$result = array();

if (!empty($_POST["email"]) && !empty($_POST["password"])) {
  $email = $_POST["email"];
  $password = $_POST["password"];

  $con = mysqli_connect(
    "localhost",
    "root",
    "",
    "ql_sinhvien"
  );

  if ($con) {
    $query = "select * from taikhoan where email = '" . $email . "'";

    $res = mysqli_query($con, $query);

    if (mysqli_num_rows($res) != 0) {
      $row = mysqli_fetch_assoc($res);
      $name = $row["name"];
      $loaiTaiKhoan = $row["LoaiTaiKhoan"];

      // Check if password is correct
      if (password_verify($password, $row["password"])) {
        $imageUrl = "/ql_sinhvien/quantrivienavatars/admin.jpg";
        if ($loaiTaiKhoan == "GiangVien") {
          $query = "select ImageUrl from giangvien where Email = '$email'";

          $res = mysqli_query($con, $query);

          if (mysqli_num_rows($res) != 0) {
            $row = mysqli_fetch_assoc($res);
            $imageUrl = $row["ImageUrl"];
          }
        } else if ($loaiTaiKhoan == "SinhVien") {
          $query = "select ImageUrl from sinhvien where Email = '$email'";

          $res = mysqli_query($con, $query);

          if (mysqli_num_rows($res) != 0) {
            $row = mysqli_fetch_assoc($res);
            $imageUrl = $row["ImageUrl"];
          }
        }

        $apiKey = "apikey";
        try {
          $apiKey = bin2hex(random_bytes(23));
        } catch (Exception $e) {
          $apiKey = bin2hex(uniqid($email, true));
        }
        $updateQuery = "update taikhoan set apiKey = '" . $apiKey . "' where email = '" . $email . "'";

        if (mysqli_query($con, $updateQuery)) {
          $result = array(
            "status" => "success",
            "message" => "Login success",
            "name" => $name,
            "email" => $email,
            "apiKey" => $apiKey,
            "imageUrl" => $imageUrl,
            "LoaiTaiKhoan" => $loaiTaiKhoan
          );
        } else {
          $result = array(
            "status" => "failed",
            "message" => "Can't update ApiKey"
          );
        }
      } else {
        $result = array(
          "status" => "failed",
          "message" => "Password is incorrect"
        );
      }
    } else {
      $result = array(
        "status" => "failed",
        "message" => "User doesn't exist"
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
    "message" => "All fields are required"
  );
}

echo json_encode($result, JSON_PRETTY_PRINT);

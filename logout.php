<?php
$result = array();

if (!empty($_POST["email"]) && !empty($_POST["apiKey"])) {
  $email = $_POST["email"];
  $apiKey = $_POST["apiKey"];

  $con = mysqli_connect(
    "localhost",
    "root",
    "",
    "ql_sinhvien"
  );

  if ($con) {
    $query = "select * from taikhoan where email = '" . $email . "' and apiKey = '" . $apiKey . "'";
    $res = mysqli_query($con, $query);
    if (mysqli_num_rows($res) != 0) {
      $updateQuery = "update taikhoan set apiKey = '' where email = '" . $email . "'";
      if (mysqli_query($con, $updateQuery)) {
        $result = array(
          "status" => "success",
          "message" => "Logout successfully"
        );
      }
    } else {
      $result = array(
        "status" => "failed",
        "message" => "Unauthorized ApiKey"
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
    "message" => "All fields are required!"
  );
}

echo json_encode($result, JSON_PRETTY_PRINT);

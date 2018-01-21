<?php
if (isset($_POST['action']) != null and isset($_POST['action']) != "") {
	$action = $_POST['action'];
	$response = array();
	if ($action == 'login') {
		if ($_POST['email'] != "" and $_POST['password'] != "") {
			$userName = getUserName($_POST['email'], $_POST['password']);
			if ($userName != "") {
				$userCode = createUniqueCode($_POST['email']);
				if ($userCode != "") {
					$response = array("logged"=>true, "userCode"=> $userCode, "userName" => $userName);
				} else {
					$response = array("logged"=>false, "errorCode"=> 500, "errorMessage" => "unable to generate the code");
				}
			} else {
				$response = array("logged"=>false, "errorCode"=> 500, "errorMessage" => "invalid email or password");
			}
		} else {
			$response = array("logged"=>false, "errorCode"=> 500, "errorMessage" => "empty email or password");
		}
	} else if ($action == 'logout') {
		if ($_POST['userCode'] != "") {
			if (logout($_POST['userCode'])) {
				$response = array("logged"=>false);
			} else {
				$response = array("logged"=>true, "errorCode"=> 500, "errorMessage" => "unable to logout");
			}
		} else {
			$response = array("logged"=>true, "errorCode"=> 500, "errorMessage" => "unable to logout");
		}
	}
	echo json_encode($response);
}
function logout($userCode) {
	$updated = false;
	include "../db.php";
	$conn = new mysqli($host, $userName, $password, $dbName);
	$update ="UPDATE user SET code = '' WHERE code = '".$userCode."';";
	if($conn->query($update)) {
		$updated = true;
	}
	mysqli_close($conn);
	return $updated;
}
function getUserName($email, $userpassword) {
	include "../db.php";
	$conn = new mysqli($host, $userName, $password, $dbName);
	$name = "";
	$selectUsers = "SELECT name FROM user WHERE email = '".$email."' AND password = '".$userpassword."'";
	$result = $conn->query($selectUsers);
	if ($row = $result->fetch_assoc()) {
		$name = $row["name"];
	}
	mysqli_close($conn);
	return $name;
}
function createUniqueCode($email) {
	$codeSize = 20;
	$newCode = false;
	include "../db.php";
	$conn = new mysqli($host, $userName, $password, $dbName);
	while ($newCode == false) {
		$code = strtoupper(substr(md5(uniqid(mt_rand(), true)), 0, $codeSize));
		$selectUsers = "SELECT code FROM user WHERE code = '".$code."'";
		$result = $conn->query($selectUsers);
		if ($result->num_rows == 0) {
			$update ="UPDATE user SET code = '".$code."' WHERE email = '".$email."';";
			if($conn->query($update)) {
				$newCode = true;
			}
		}
	}
	mysqli_close($conn);
	return $code;
}
?>
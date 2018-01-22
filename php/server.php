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
					$response = array("error"=>false, "logged"=>true, "userCode"=> $userCode, "userName" => $userName);
				} else {
					$response = array("error"=>true, "logged"=>false, "errorCode"=> 500, "errorMessage" => "unable to generate the code");
				}
			} else {
				$response = array("error"=>true, "logged"=>false, "errorCode"=> 500, "errorMessage" => "invalid email or password");
			}
		} else {
			$response = array("error"=>true, "logged"=>false, "errorCode"=> 500, "errorMessage" => "empty email or password");
		}
	} else if ($action == 'logout') {
		if ($_POST['userCode'] != "") {
			if (logout($_POST['userCode'])) {
				$response = array("error"=>false, "logged"=>false);
			} else {
				$response = array("error"=>true, "logged"=>true, "errorCode"=> 500, "errorMessage" => "unable to logout");
			}
		} else {
			$response = array("error"=>true, "logged"=>true, "errorCode"=> 500, "errorMessage" => "unable to logout");
		}
	} else if ($action == 'submitPost') {
		if ($_POST['userCode'] != "" and $_POST['title'] != "" and $_POST['message'] != "" and $_POST['category'] != "") {
			if (createPost($_POST['userCode'], $_POST['title'], $_POST['message'], $_POST['category'])) {
				$response = array("error"=>false);
			} else {
				$response = array("error"=>true, "errorCode"=> 500, "errorMessage" => "unable to submit the post");
			}
		} else {
			$response = array("error"=>true, "errorCode"=> 500, "errorMessage" => "missing mandatory fields to submit the post");
		}
	} else if ($action == 'getPosts') {
		$response = getPosts($_POST['userCode'], $_POST['offset']);
	}
	echo json_encode($response);
}
function getPosts($userCode, $offset) {
	$response = array();
	include "../db.php";
	$conn = new mysqli($host, $userName, $password, $dbName);
	$select = "SELECT P.id, P.title, P.category, P.message, P.createdDate, U.code, U.name FROM post P INNER JOIN user U ON U.email = P.userId ORDER BY P.id ASC LIMIT 10 OFFSET $offset";
	$result = $conn->query($select);
	if ($result->num_rows > 0) {
		while($row = $result->fetch_assoc()) {
			$isEditable = $userCode == "" ? false : $row['code'] == $userCode;
			$post = array("id" => $row['id'], "title" => $row['title'], "category" => $row['category'], "message" => $row['message'], "createdDate" => $row['createdDate'], "createdBy" => $row['name'], "isEditable" => $isEditable);
			array_push($response, $post);
		}
	}
	mysqli_close($conn);
	return $response;
}
function createPost($userCode, $title, $message, $category) {
	date_default_timezone_set("Brazil/East");
	$datetime = date('Y-m-d H:i:s');
	$created = false;
	include "../db.php";
	$conn = new mysqli($host, $userName, $password, $dbName);
	$selectUsers = "SELECT email FROM user WHERE code = '".$userCode."'";
	$result = $conn->query($selectUsers);
	if ($row = $result->fetch_assoc()) {
		$email = $row["email"];
		$sql2 = "INSERT INTO post (title, category, message, createdDate, userId) VALUES ('".$title."', '".$category."', '".$message."', '".$datetime."', '".$email."')";
		if($conn->query($sql2)){
			$created = true;
		}
		
	}
	mysqli_close($conn);
	return $created;
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
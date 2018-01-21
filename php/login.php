<?php
if (isset($_POST['action']) != null and isset($_POST['action']) != "") {
	$action = $_POST['action'];
	$response = array();
	if ($action == 'login') {
		if ($_POST['email'] != "" and $_POST['password'] != "") {
			$userName = "lucas";
			$userCode = "code";
			$response = array("logged"=>true, "userCode"=> $userCode, "userName" => $userName);
		} else {
			$response = array("logged"=>false, "errorCode"=> 500, "errorMessage" => "invalid email or password");
		}
	} else if ($action == 'logout') {
		if ($_POST['userCode'] != "") {
			$response = array("logged"=>false);
		} else {
			$response = array("logged"=>true, "errorCode"=> 500, "errorMessage" => "unable to logout");
		}
	}
	echo json_encode($response);
}
?>
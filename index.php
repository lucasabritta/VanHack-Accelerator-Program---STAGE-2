<html>
	<head>
		<meta charset="utf-8">
		<script type="text/javascript" src="js/jquery-1.11.3.min.js"></script>
		<link rel="stylesheet" href="css/bootstrap.min.css">
		<script src="js/bootstrap.min.js"></script>
		<link rel="stylesheet" href="css/index-style.css">
		<script type="text/javascript" src="js/functions.js"></script>
	</head>
	<body>
	<div class = "content" id = "content">
		<div class = "header" id = "header">
			this is the header
			<div class = "login" id = "login">
				<form action="javascript:login()">
					<label class = "loginLabel">E-mail:</label> <input id = "loginEmail" class = "loginInput" type="text" name="loginEmail">
					<label class = "loginLabel">Password:</label> <input id = "loginPassword" class = "loginInput" type="text" name="loginPassword">
					<input class = "loginButton" type="submit" value="Log in">
				</form>
			</div>
			<div class = "logged" id = "logged">
				<label class = "logoutLabel" id = "logoutLabel"></label> <input onclick="logout()" class = "loginButton" type="submit" value="Log out">
			</div>
			<label class = "errorLabel" id = "errorLabel"></label>
		</div>
		<div class = "postSpace" id = "postSpace">
			sample post
		</div>
	</div>
	<script>
	var userCode = getCookie("userCode");
	if (userCode != "") {
		logged();
	} else {
		document.getElementById('errorLabel').style.display = 'none';
		document.getElementById('logged').style.display = 'none';
		document.getElementById('login').style.display = 'block';
	}
	function logged() {
		document.getElementById('logged').style.display = 'block';
		document.getElementById('login').style.display = 'none';
		document.getElementById('errorLabel').style.display = 'none';
		var userCode = getCookie("userCode");
		var userName = getCookie("userName");
		document.getElementById('logoutLabel').innerHTML  = 'Hello ' + userName + ",";
	}
	function logout() {
		$.ajax({
			type: 'post',
			dataType : "json",
			url: 'php/login.php',
			data: {action: "logout", userCode: getCookie("userCode")},
			success: function(output) {
				console.log(output);
				var response_logged = output.logged;
				if (!response_logged) {
					document.getElementById('errorLabel').style.display = 'none';
					document.getElementById('logged').style.display = 'none';
					document.getElementById('login').style.display = 'block';
					setCookie("userCode", "", 0);
					setCookie("userName", "", 0);
				} else {
					var errorLabel = document.getElementById('errorLabel');
					errorLabel.style.display = 'block';
					errorLabel.innerHTML  = output.errorMessage;
				}
			},
			error: function(output) {
				var errorLabel = document.getElementById('errorLabel');
				errorLabel.style.display = 'block';
				errorLabel.innerHTML  = "An error occurred while trying to logout, please try again later";
			}
		});
	}
	function login() {
		var email = document.getElementById('loginEmail').value;
		var password = document.getElementById('loginPassword').value;
		$.ajax({
			type: 'post',
			dataType : "json",
			url: 'php/login.php',
			data: {action: "login", email: email, password: password},
			success: function(output) {
				var response_logged = output.logged;
				if (response_logged) {
					var userCode = output.userCode;
					var userName = output.userName;
					setCookie("userCode", userCode, 2);
					setCookie("userName", userName, 2);
					logged();
				} else {
					var errorLabel = document.getElementById('errorLabel');
					errorLabel.style.display = 'block';
					errorLabel.innerHTML  = output.errorMessage;
				}
			},
			error: function(output) {
				var errorLabel = document.getElementById('errorLabel');
				errorLabel.style.display = 'block';
				errorLabel.innerHTML  = "An error occurred while trying to login, please try again later";
			}
		});
	}
	</script>
	</body>
</html>

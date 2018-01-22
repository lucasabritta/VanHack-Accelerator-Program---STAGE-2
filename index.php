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
	<div id="submitPostModal" class="modal">
		<div class="modal-content">
			<span class="close" onclick = "closeModal('submitPostModal')">&times;</span>
			<p>The post was successfully submitted!</p>
		</div>
	</div>
	<div class = "content" id = "content">
		<div class = "header" id = "header">
			this is the header
			<div class = "login" id = "login">
				<form action="javascript:login()">
					<label class = "loginLabel">E-mail:</label> <input id = "loginEmail" class = "loginInput" type="text" name="loginEmail">
					<label class = "loginLabel">Password:</label> <input id = "loginPassword" class = "loginInput" type="password" name="loginPassword">
					<input class = "loginButton" type="submit" value="Log in">
				</form>
			</div>
			<div class = "logged" id = "logged">
				<label class = "logoutLabel" id = "logoutLabel"></label> <input onclick="logout()" class = "loginButton" type="submit" value="Log out">
			</div>
			<label class = "errorLabel" id = "errorLabel"></label>
		</div>
		<div class = "postSpace" id = "postSpace">
			<div class = "createPost" id = "createPost">
				<input onclick="createPost()" class = "loginButton" type="submit" value="Create Post">
			</div>
			<div class = "postInfo" id = "postInfo">
				<label class = "postInfoLabel">Title</label><br>
				<input id = "postTitle" style = "width:500px;" type="text" name="postTitle"><br><br>
				<label class = "postInfoLabel">Message</label><br>
				<textarea id = "postMessage" rows="8" cols="68"> </textarea><br><br>
				<label class = "postInfoLabel">Category</label><br>
				<form action="">
					<input type="radio" name="category" value="question"> Question<br>
					<input type="radio" name="category" value="other" checked> Other
				</form>
				<input onclick="submitPost()" class = "submitPostButton" type="submit" value="Submit">
				<label class = "errorPostLabel" id = "errorPostLabel"></label>
			</div>
			<div class = "postList" id = "postList">
			</div>
		</div>
	</div>
	<script>
	function renderPost(title, message, createdDate, createdBy, postId, category, isEditable) {
		var table ='<table id = "'+postId+'" class = "postListTable">\
						<tr class = "postListTableTitle">\
							<td class = "postListTableTD" colspan="3">' + title + '</td>\
						</tr>\
						<tr class = "postListTableMessage">\
							<td class = "postListTableTD" colspan="3">' + message + '</td>\
						</tr>\
						<tr class = "postListTableFooter">\
							<td class = "postListTableTD">Created: ' + createdDate + '</td>\
							<td style = "text-align:center;">Created by: ' + createdBy + '</td>\
							<td style = "text-align:right;padding-right:5px;">Category: ' + category + '</td>\
						</tr>\
					</table>';
		document.getElementById('postList').innerHTML += table;
	}
	function getPosts(offset) {
		var userCode = getCookie("userCode");
		$.ajax({
			type: 'post',
			dataType : "json",
			url: 'php/server.php',
			data: {action: "getPosts", userCode: userCode, offset: offset},
			success: function(output) {
				var responseLen = output.length;
				for (var i = 0; i < responseLen; i++) {
					var title = output[i].title;
					var message = output[i].message;
					var createdDate = output[i].createdDate;
					var createdBy = output[i].createdBy;
					var postId = output[i].id;
					var category = output[i].category;
					var isEditable = output[i].isEditable;
					renderPost(title, message, createdDate, createdBy, postId, category, isEditable);
				}
			},
			error: function(output) {
			}
		});
	}
	getPosts(0); /* update this to get the real offset*/
	window.onclick = function(event) {
		var modal = document.getElementById('submitPostModal');
		if (event.target == modal) {
			modal.style.display = "none";
		}
	}
	function closeModal(id) {
		var modal = document.getElementById(id);
		modal.style.display = "none";
	}
	function openModal(id) {
		var modal = document.getElementById(id);
		modal.style.display = "block";
	}
	
	function createPost() {
		document.getElementById('postInfo').style.display = 'block';
		document.getElementById('errorPostLabel').style.display = 'none';
		document.getElementById('postTitle').value = "";
		document.getElementById('postMessage').value = "";
	}
	function submitPost() {
		document.getElementById('errorPostLabel').style.display = 'none';
		var title = document.getElementById('postTitle').value;
		var message = document.getElementById('postMessage').value;
		var radios = document.getElementsByName('category');
		var category = "";
		for (var i = 0, length = radios.length; i < length; i++) {
			if (radios[i].checked) {
				category = radios[i].value;
				break;
			}
		}
		if (title == "") {
			var errorLabel = document.getElementById('errorPostLabel');
			errorLabel.style.display = 'block';
			errorLabel.innerHTML  = "Title field is mandatory";
		} else if (message == "") {
			var errorLabel = document.getElementById('errorPostLabel');
			errorLabel.style.display = 'block';
			errorLabel.innerHTML  = "Message field is mandatory";
		}
		document.getElementById('postTitle').value = "";
		document.getElementById('postMessage').value = "";
		$.ajax({
			type: 'post',
			dataType : "json",
			url: 'php/server.php',
			data: {action: "submitPost", userCode: getCookie("userCode"), title: title, message: message, category: category},
			success: function(output) {
				var response = output.error;
				if (!response) {
					document.getElementById('postInfo').style.display = 'none';
					openModal('submitPostModal');
				} else {
					var errorLabel = document.getElementById('errorPostLabel');
					errorLabel.style.display = 'block';
					errorLabel.innerHTML  = output.errorMessage;
				}
			},
			error: function(output) {
				console.log(output);
				var errorLabel = document.getElementById('errorPostLabel');
				errorLabel.style.display = 'block';
				errorLabel.innerHTML  = "An error occurred while trying to submit a post, please try again later";
			}
		});
	}
	var userCode = getCookie("userCode");
	if (userCode != "") {
		logged();
	} else {
		logouActions();
	}
	function logged() {
		document.getElementById('logged').style.display = 'block';
		document.getElementById('createPost').style.display = 'block';
		document.getElementById('login').style.display = 'none';
		document.getElementById('errorLabel').style.display = 'none';
		var userCode = getCookie("userCode");
		var userName = getCookie("userName");
		document.getElementById('logoutLabel').innerHTML  = 'Hello ' + userName + ",";
	}
	function logouActions(){
		document.getElementById('errorLabel').style.display = 'none';
		document.getElementById('logged').style.display = 'none';
		document.getElementById('login').style.display = 'block';
		document.getElementById('createPost').style.display = 'none';
		setCookie("userCode", "", 0);
		setCookie("userName", "", 0);
	}
	function logout() {
		$.ajax({
			type: 'post',
			dataType : "json",
			url: 'php/server.php',
			data: {action: "logout", userCode: getCookie("userCode")},
			success: function(output) {
				var response_logged = output.logged;
				if (!response_logged) {
					logouActions();
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
			url: 'php/server.php',
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

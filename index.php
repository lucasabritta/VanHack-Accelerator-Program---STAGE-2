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
	<div id="editPostModalMessage" class="modal">
		<div class="modal-content">
			<span class="close" onclick = "closeModal('editPostModalMessage')">&times;</span>
			<p>The post was successfully edited!</p>
		</div>
	</div>
	<div id="submitCommentModal" class="modal">
		<div class="modal-content">
			<span class="close" onclick = "closeModal('submitCommentModal')">&times;</span>
			<p>The comment was successfully submitted!</p>
		</div>
	</div>
	<div id="editCommentModalMessage" class="modal">
		<div class="modal-content">
			<span class="close" onclick = "closeModal('editCommentModalMessage')">&times;</span>
			<p>The comment was successfully edited!</p>
		</div>
	</div>
	<div id="editPostModal" class="modal">
		<div class="modal-content" style = "padding: 25px 20%;">
			<span class="close" onclick = "closeModal('editPostModal')">&times;</span>
			<p>Edit Post</p> <br><br>
			<label class = "postInfoLabel">Title</label><br>
			<input id = "editPostTitle" style = "width:500px;" type="text" name="postTitle"><br><br>
			<label class = "postInfoLabel">Message</label><br>
			<textarea id = "editPostMessage" rows="8" cols="68"> </textarea><br><br>
			<label class = "postInfoLabel">Category</label><br>
			<form action="">
				<input type="radio" name="editCategory" value="question"> Question<br>
				<input type="radio" name="editCategory" value="other" checked> Other
			</form>
			<input onclick="submitEditedPost()" class = "submitPostButton" type="submit" value="Submit">
			<label class = "errorPostLabel" id = "editErrorPostLabel"></label>
		</div>
	</div>
	<div id="editCommentModal" class="modal">
		<div class="modal-content" style = "padding: 25px 20%;">
			<span class="close" onclick = "closeModal('editCommentModal')">&times;</span>
			<p>Edit Comment</p> <br><br>
			<label class = "postInfoLabel">Message</label><br>
			<textarea id = "editCommentMessage" rows="8" cols="68"> </textarea><br><br>
			<input onclick="submitEditedComment()" class = "submitPostButton" type="submit" value="Submit">
			<label class = "errorPostLabel" id = "editErrorCommentLabel"></label>
		</div>
	</div>
	<div class = "content" id = "content">
		<div class = "header" id = "header">
			<label style="text-align:center;width:100%;font-size:30px;">VanHack Accelerator Program STAGE-2</label>
			<form action="javascript:searchPost('0')" style="text-align:right;margin-right:10px;">
			  Search:
			  <input type="search" name="postSearch" id="postSearch">
			  <input type="image" src="img/MagnifyingGlass.png" width="20px" height="20px" style="padding-top:3px;" border="0" alt="search">
			</form>
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
			<div class = "createComment" id = "createComment">
				<label class = "postInfoLabel">Message</label><br>
				<textarea id = "commentMessage" rows="8" cols="68"> </textarea><br><br>
				<input onclick="submitComment()" class = "submitPostButton" type="submit" value="Submit">
				<label class = "errorPostLabel" id = "errorCommentLabel"></label>
			</div>
		</div>
	</div>
	<script>
	function submitEditedComment() {
		var commentId = getCookie("commentId");
		document.getElementById('editErrorCommentLabel').style.display = 'none';
		var message = document.getElementById('editCommentMessage').value;
		if (message == "") {
			var errorLabel = document.getElementById('editErrorCommentLabel');
			errorLabel.style.display = 'block';
			errorLabel.innerHTML  = "Message field is mandatory";
		}
		var postId = getCookie("postId");
		document.getElementById('editCommentMessage').value = "";
		$.ajax({
			type: 'post',
			dataType : "json",
			url: 'php/server.php',
			data: {action: "editComment", commentId: commentId, message: message},
			success: function(output) {
				var response = output.error;
				if (!response) {
					closeModal('editCommentModal');
					var postId = getCookie("postId");
					postClicked(postId);
					openModal('editCommentModalMessage');
				} else {
					var errorLabel = document.getElementById('editErrorCommentLabel');
					errorLabel.style.display = 'block';
					errorLabel.innerHTML  = output.errorMessage;
				}
			},
			error: function(output) {
				var errorLabel = document.getElementById('editErrorCommentLabel');
				errorLabel.style.display = 'block';
				errorLabel.innerHTML  = "An error occurred while trying to edit a comment, please try again later";
			}
		});
		
	}
	function submitComment() {
		document.getElementById('errorCommentLabel').style.display = 'none';
		var message = document.getElementById('commentMessage').value;
		if (message == "") {
			var errorLabel = document.getElementById('errorCommentLabel');
			errorLabel.style.display = 'block';
			errorLabel.innerHTML  = "Message field is mandatory";
		}
		var postId = getCookie("postId");
		document.getElementById('commentMessage').value = "";
		$.ajax({
			type: 'post',
			dataType : "json",
			url: 'php/server.php',
			data: {action: "submitComment", userCode: getCookie("userCode"), message: message, postId: postId},
			success: function(output) {
				var response = output.error;
				if (!response) {
					document.getElementById('createComment').style.display = 'none';
					openModal('submitCommentModal');
					var postId = getCookie("postId");
					postClicked(postId);
				} else {
					var errorLabel = document.getElementById('errorCommentLabel');
					errorLabel.style.display = 'block';
					errorLabel.innerHTML  = output.errorMessage;
				}
			},
			error: function(output) {
				console.log(output);
				var errorLabel = document.getElementById('errorCommentLabel');
				errorLabel.style.display = 'block';
				errorLabel.innerHTML  = "An error occurred while trying to submit a comment, please try again later";
			}
		});
	}
	function onInit() {
		var userCode = getCookie("userCode");
		if (userCode != "") {
			logged();
		} else {
			logouActions();
		}
	}
	onInit();
	function postClicked(postId) {
		setCookie("postId", postId, 2);
		document.getElementById('postList').innerHTML = "";
		var userCode = getCookie("userCode");
		if (userCode != "") {
			document.getElementById('createComment').style.display = 'block';
		}
		var offset = 0;
		$.ajax({
			type: 'post',
			dataType : "json",
			url: 'php/server.php',
			data: {action: "getPostAndComments", userCode: userCode, offset:offset, postId: postId},
			success: function(output) {
				var responseLen = output.length;
				if (responseLen > 0 ) {
					var title = output[0].title;
					var message = output[0].message;
					var createdDate = output[0].createdDate;
					var createdBy = output[0].createdBy;
					var id = output[0].id;
					var category = output[0].category;
					var isEditable = output[0].isEditable;
					renderPost(title, message, createdDate, createdBy, id, category, isEditable);
					for (var i = 1; i < responseLen; i++) {
						var commentary = output[i].commentary;
						var createdDate = output[i].createdDate;
						var id = output[i].id;
						var isEditable = output[i].isEditable;
						var userName = output[i].userName;
						renderComment(commentary, createdDate, id, isEditable, userName)
					}
				}
			},
			error: function(output) {
			}
		});
	}
	function editComment(commentId, message) {
		document.getElementById('editCommentMessage').value = message;
		document.getElementById('editErrorCommentLabel').style.display = 'none';
		openModal('editCommentModal');
		setCookie("commentId", commentId, 2);
	}
	function renderComment(commentary, createdDate, id, isEditable, userName) {
		var table = '';
		if (isEditable) {
			table +='<table id = "commentary_'+id+'" class = "postListTable" onclick= "editComment(\''+id+'\', \''+commentary+'\')">';
		} else {
			table +='<table id = "commentary_'+id+'" class = "postListTable" onclick= "">';
		}
		table +='\
						<tr class = "postListTableMessage">\
							<td valign="top" class = "postListTableTD" colspan="2">' + commentary + '</td>\
						</tr>\
						<tr class = "postListTableFooter">\
							<td class = "postListTableTD">Created: ' + createdDate + '</td>\
							<td style = "text-align:right;padding-right:5px;">Created by: ' + userName + '</td>\
						</tr>\
					</table>';
		document.getElementById('postList').innerHTML += table;
	}
	function searchPost(offset) {
		document.getElementById('postList').innerHTML = "";
		document.getElementById('createComment').style.display = 'none';
		var postSearch = document.getElementById('postSearch').value;
		document.getElementById('postSearch').value = "";
		var userCode = getCookie("userCode");
		$.ajax({
			type: 'post',
			dataType : "json",
			url: 'php/server.php',
			data: {action: "getPosts", userCode: userCode, offset:offset, title:postSearch},
			success: function(output) {
				var responseLen = output.length;
				for (var i = 0; i < responseLen; i++) {
					var title = output[i].title;
					var message = output[i].message;
					var createdDate = output[i].createdDate;
					var createdBy = output[i].createdBy;
					var postId = output[i].id;
					var category = output[i].category;
					renderPost(title, message, createdDate, createdBy, postId, category, false);
				}
			},
			error: function(output) {
			}
		});
	}
	function editPost(postId, title, category, message) {
		document.getElementById('editPostTitle').value = title;
		document.getElementById('editPostMessage').value = message;
		var radios = document.getElementsByName('editCategory');
		for (var i = 0, length = radios.length; i < length; i++) {
			if (radios[i].value == category) {
				radios[i].checked = true;
				break;
			}
		}
		document.getElementById('editErrorPostLabel').style.display = 'none';
		openModal('editPostModal');
	}
	function submitEditedPost() {
		var postId = getCookie("postId");
		document.getElementById('editErrorPostLabel').style.display = 'none';
		var title = document.getElementById('editPostTitle').value;
		var message = document.getElementById('editPostMessage').value;
		var radios = document.getElementsByName('editCategory');
		var category = "";
		for (var i = 0, length = radios.length; i < length; i++) {
			if (radios[i].checked) {
				category = radios[i].value;
				break;
			}
		}
		if (title == "") {
			var errorLabel = document.getElementById('editErrorPostLabel');
			errorLabel.style.display = 'block';
			errorLabel.innerHTML  = "Title field is mandatory";
		} else if (message == "") {
			var errorLabel = document.getElementById('editErrorPostLabel');
			errorLabel.style.display = 'block';
			errorLabel.innerHTML  = "Message field is mandatory";
		}
		document.getElementById('editPostTitle').value = "";
		document.getElementById('editPostMessage').value = "";
		$.ajax({
			type: 'post',
			dataType : "json",
			url: 'php/server.php',
			data: {action: "editPost", postId: postId, title: title, message: message, category: category},
			success: function(output) {
				var response = output.error;
				if (!response) {
					document.getElementById('postInfo').style.display = 'none';
					closeModal('editPostModal');
					var postId = getCookie("postId");
					postClicked(postId);
					openModal('editPostModalMessage');
				} else {
					var errorLabel = document.getElementById('editErrorPostLabel');
					errorLabel.style.display = 'block';
					errorLabel.innerHTML  = output.errorMessage;
				}
			},
			error: function(output) {
				var errorLabel = document.getElementById('editErrorPostLabel');
				errorLabel.style.display = 'block';
				errorLabel.innerHTML  = "An error occurred while trying to submit a post, please try again later";
			}
		});
	}
	function renderPost(title, message, createdDate, createdBy, postId, category, isEditable) {
		var table = '';
		if (isEditable) {
			table += '<table id = "post_'+postId+'" class = "postListTable" onclick= "editPost(\''+postId+'\',\''+title+'\',\''+category+'\',\''+message+'\')">';
		} else {
			table += '<table id = "post_'+postId+'" class = "postListTable" onclick= "postClicked(\''+postId+'\')">';
		}
		table += '\
						<tr class = "postListTableTitle">\
							<td class = "postListTableTD" colspan="3">' + title + '</td>\
						</tr>\
						<tr class = "postListTableMessage">\
							<td valign="top" class = "postListTableTD" colspan="3">' + message + '</td>\
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
		document.getElementById('postList').innerHTML = "";
		document.getElementById('createComment').style.display = 'none';
		var userCode = getCookie("userCode");
		$.ajax({
			type: 'post',
			dataType : "json",
			url: 'php/server.php',
			data: {action: "getPosts", userCode: userCode, offset: offset, title:""},
			success: function(output) {
				var responseLen = output.length;
				for (var i = 0; i < responseLen; i++) {
					var title = output[i].title;
					var message = output[i].message;
					var createdDate = output[i].createdDate;
					var createdBy = output[i].createdBy;
					var postId = output[i].id;
					var category = output[i].category;
					renderPost(title, message, createdDate, createdBy, postId, category, false);
				}
			},
			error: function(output) {
			}
		});
	}
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
					getPosts(0);
					openModal('submitPostModal');
				} else {
					var errorLabel = document.getElementById('errorPostLabel');
					errorLabel.style.display = 'block';
					errorLabel.innerHTML  = output.errorMessage;
				}
			},
			error: function(output) {
				var errorLabel = document.getElementById('errorPostLabel');
				errorLabel.style.display = 'block';
				errorLabel.innerHTML  = "An error occurred while trying to submit a post, please try again later";
			}
		});
	}
	function logged() {
		document.getElementById('logged').style.display = 'block';
		document.getElementById('createPost').style.display = 'block';
		document.getElementById('login').style.display = 'none';
		document.getElementById('errorLabel').style.display = 'none';
		document.getElementById('postInfo').style.display = 'none';
		var userCode = getCookie("userCode");
		var userName = getCookie("userName");
		document.getElementById('logoutLabel').innerHTML  = 'Hello ' + userName + ",";
		getPosts(0);
	}
	function logouActions(){
		document.getElementById('errorLabel').style.display = 'none';
		document.getElementById('logged').style.display = 'none';
		document.getElementById('login').style.display = 'block';
		document.getElementById('createPost').style.display = 'none';
		document.getElementById('postInfo').style.display = 'none';
		setCookie("userCode", "", 0);
		setCookie("userName", "", 0);
		getPosts(0);
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

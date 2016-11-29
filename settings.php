<?php
	require_once("resources/functions/data.php");
	$session = checkSession();
	if ($session === false) {
		header("Location: /login");
		die();
	}

	if (isset($_GET["setting"])) {
		$url = getPageName();
		if (preg_match("/^settings\.php\?setting\=.*/", $url)) {
			$setting = explode("=", $url)[1];
			header("Location: /settings/" . $setting);
			die();
		}
	} else {
		header("Location: /settings/account");
		die();
	}

	$user = getUserInfo($_SESSION["userID"]);

	if ($_SERVER["REQUEST_METHOD"] == "POST") {
		$settingType = $_POST["settingType"];
		if ($settingType == "account") {
			if (validateUserWithID($_SESSION["userID"], $_POST["password"])) {
				$validUpdate = true;
				$validUsername = true;
				$validEmail = true;
				$name = $_POST["name"];
				$originalUsername = $_POST["origUsername"];
				$username = $_POST["username"];
				$originalEmail = $_POST["origEmail"];
				$email = $_POST["email"];
				$bio = $_POST["bio"];
				$url = $_POST["url"];
				if ($username !== $originalUsername) {
					$validUsername = validateUsername($username);
				}
				if ($email !== $originalEmail) {
					$validEmail = validateEmail($email);
				}
				if ($validUsername === "empty") {
					$error = "You forgot to enter a username, silly.";
					$validUpdate = false;
				} else if ($validUsername === "exists") {
					$error = "The username already exists, choose a different one.";
					$validUpdate = false;
				} else if ($validEmail === "empty") {
					$error = "You forgot to enter an email address, silly.";
					$validUpdate = false;
				} else if ($validEmail === "exists") {
					$error = "Looks like the email is already registered to an account.";
					$validUpdate = false;
				}
				if ($validUpdate) {
					saveAccountSettings($_SESSION["userID"], $name, $username, $email, $bio, $url);
					$message = "Changes saved.";
				}
			} else {
				$error = "Invalid password.";
			}
			$user = getUserInfo($_SESSION["userID"]);
		} else if ($settingType == "images") {
			if (validateUserWithID($_SESSION["userID"], $_POST["password"])) {
				$uploads_dir = "resources/img/users/".$user["id"]."/";
				$imageNumber = 1;
				$profileImageChange = false;
				$coverImageChange = false;
				if ($_FILES["profileImage"]["error"][0] == UPLOAD_ERR_OK) {
					removeProfileImage($user["id"]);
					foreach ($_FILES["profileImage"]["error"] as $key => $image_error) {
						if ($image_error == UPLOAD_ERR_OK) {
							$tmp_name = $_FILES["profileImage"]["tmp_name"][$key];
							$end = explode(".", $_FILES["profileImage"]["name"][$key])[1];
							$name = "p";
							$fullName = $name.".".$end;
							if (move_uploaded_file($tmp_name, $uploads_dir.$fullName)) {
								$imageNumber++;
								$profileImageChange = true;
							}
						}
					}
				}
				if ($_FILES["coverImage"]["error"][0] == UPLOAD_ERR_OK) {
					$input = $_POST["password"];
					removeCoverImage($user["id"]);
					foreach ($_FILES["coverImage"]["error"] as $key => $image_error) {
						if ($image_error == UPLOAD_ERR_OK) {
							$tmp_name = $_FILES["coverImage"]["tmp_name"][$key];
							$end = explode(".", $_FILES["coverImage"]["name"][$key])[1];
							$name = "c";
							$fullName = $name.".".$end;
							if (move_uploaded_file($tmp_name, $uploads_dir.$fullName)) {
								$imageNumber++;
								$coverImageChange = true;
							}
						}
					}
				}
				if ($profileImageChange && $coverImageChange) {
					$message = "Profile image and cover image changed.";
				} else if ($profileImageChange && $coverImageChange === false) {
					$message = "Profile image changed.";
				} else if ($profileImageChange === false && $coverImageChange) {
					$message = "Cover image changed.";
				} else if ($profileImageChange === false && $coverImageChange === false) {
					$message = "No changes were made.";
				}
			} else {
				$error = "Invalid password.";
			}
		} else if ($settingType == "password") {
			$newPass = $_POST["newPass"];
			$newPassVal = $_POST["newPassVal"];
			$password = $_POST["password"];
			if ($newPass === "") {
				$error = "You didn't enter a new password, naughty.";
			} else if ($newPassVal === "") {
				$error = "You didn't re-enter a new password, naughty.";
			} else if ($newPass !== $newPassVal) {
				$error = "The passwords doesn't match.";
			} else if ($newPass === $newPassVal) {
				if (validateUserWithID($_SESSION["userID"], $password)) {
					saveNewPassword($_SESSION["userID"], $newPass);
					$message = "Password changed.";
				} else {
					$error = "Invalid password.";
				}
			}
		}
	}
?>
<html>
	<head>
		<title>Settings | Twitter</title>
		<?php require("resources/sections/head.php");?>
		<script src="/resources/js/settings.js" type="text/javascript"></script>
	</head>
	<body>
		<?php require("resources/sections/header.php");?>

		<section id="homePage">
			<div class="wrapper">
				<article id="sideBar" class="sideBar">
					<div class="profileSideBarInner">
						<div class="userCoverImageSideWrapper">
							<div class="userCoverImage" style="background:url(/resources/img/<?php print getUserCover($user["id"]);?>) no-repeat center center; background-size:cover;"></div>
						</div>
						<div class="userSideBarInfoWrapper">
							<a href="/<?php print $user["username"];?>">
								<div class="siderBarProfileImageWrapper">
									<div class="siderBarProfileImage" style="background:url(/resources/img/<?php print getUserImage($user["id"]); ?>) no-repeat center center; background-size:cover;"></div>
								</div>
							</a>
							<div class="userSideBarInfo">
								<a href="/<?php print $user["username"];?>">
									<h3 class="userSideBarName">
										<?php
											if (strlen($user["name"]) >= 17) {
												print substr($user["name"],0,17) . "..";
											} else {
												print $user["name"];
											}
										?>
									</h3>
								</a>
								<a href="/<?php print $user["username"];?>">
									<h6 class="userSibeBarUsername"><?php print "@" . $user["username"];?></h6>
								</a>
							</div>
						</div>
					</div>
					<div id="settingsCategoryWrapper">
						<ul id="settingsCategories">
							<?php if ($_GET["setting"] !== "account") { print '<a href="account">'; }?>
								<li <?php if ($_GET["setting"] == "account") { print 'class="selected"'; } ?>>
									Account
									<i class="fa fa-arrow-right"></i>
								</li>
							<?php if ($_GET["setting"] !== "account") { print '</a>'; }?>

							<?php if ($_GET["setting"] !== "images") { print '<a href="images">'; }?>
								<li <?php if ($_GET["setting"] == "images") { print 'class="selected"'; } ?>>
									Images
									<i class="fa fa-arrow-right"></i>
								</li>
							<?php if ($_GET["setting"] !== "images") { print '</a>'; }?>

							<?php if ($_GET["setting"] !== "password") { print '<a href="password">'; }?>
								<li class="last <?php if ($_GET["setting"] == "password") { print "selected"; } ?>">
									Password
									<i class="fa fa-arrow-right"></i>
								</li>
							<?php if ($_GET["setting"] !== "password") { print '</a>'; }?>
						</ul>
					</div>
				</article>
				<article id="settingsBar" class="wide">
					<div id="settingsWrapper">
						<div class="settingsTitleWrapper">
							<div class="settingsTitle">
								<?php print $_GET["setting"];?>
							</div>
							<div class="settingsDescription">
								<?php
									if ($_GET["setting"] == "account") {
										print "Change your basic account information.";
									} else if ($_GET["setting"] == "images") {
										print "Manage the images linked to your account.";
									} else if ($_GET["setting"] == "password") {
										print "Change your account password.";
									} else if ($_GET["setting"] == "remove") {
										print "Remove your Twitter account permanently.";
									}
								?>
							</div>
						</div>
						<div id="messageWrapper">

							<?php if (isset($error)) {?>

								<div class="errorBox">
									<?php print $error;?>
									<div id="closeError">
										<i class="fa fa-times" id="closeErrorIcon"></i>
									</div>
								</div>

							<?php }?>

							<?php if (isset($message)) {?>

								<div class="messageBox">
									<?php print $message;?>
									<div id="closeMessage">
										<i class="fa fa-times" id="closeMessageIcon"></i>
									</div>
								</div>

							<?php }?>

						</div>

						<?php if ($_GET["setting"] == "account") { ?>

							<div class="settingsContentWrapper">
								<form action="/settings/<?php print $_GET["setting"];?>" method="post" id="accountChanges">
									<input type="text" hidden>
									<input type="password" hidden>
									<input type="text" hidden name="settingType" value="account">
									<div class="settingsFieldSet">
										<label for="setName">Name</label>
										<input type="text" name="name" id="setName" value="<?php print $user["name"];?>">
									</div>
									<div class="settingsFieldSet">
										<label for="setUsername">Username</label>
										<input type="hidden" name="origUsername" value="<?php print $user["username"]; ?>">
										<input type="text" name="username" id="setUsername" value="<?php print $user["username"];?>">
										<div class="settingSmall">This is your current username.</div>
									</div>
									<div class="settingsFieldSet">
										<label for="setEmail">Email</label>
										<input type="hidden" name="origEmail" value="<?php print $user["email"];?>">
										<input type="email" name="email" id="setEmail" value="<?php print $user["email"];?>">
										<div class="settingSmall">Email will not be publically displayed.</div>
									</div>
									<div class="settingsFieldSet">
										<label for="setBio">Bio</label>
										<textarea name="bio" id="setBio"><?php print $user["bio"];?></textarea>
									</div>
									<div class="settingsFieldSet">
										<label for="setURL">URL</label>
										<input type="text" name="url" id="setURL" value="<?php print $user["url"];?>">
									</div>
									<div class="settingsFieldSet bottom">
										<label for="currentPassword">Your password</label>
										<input <?php if (isset($error) && $error == "Invalid password.") { print 'class="error"'; } ?> type="password" name="password" id="currentAccountPassword">
									</div>
								</form>
							</div>
							<div class="saveSettingsWrapper">
								<div id="saveAccountSettings" class="blueButton">Save changes</div>
							</div>

						<?php } else if ($_GET["setting"] == "images") {?>

							<form action="/settings/images" method="post" id="imagesForm" enctype="multipart/form-data">
								<input type="text" hidden name="settingType" value="images">
								<div class="settingsContentWrapper">
									<div class="settingsSubTitle">Profile image</div>
									<div class="settingsImageWrapper" id="userImageWrapper">
										<img src="/resources/img/<?php print getUserImage($user["id"]);?>" alt="settings profile image" class="settingsImage" id="profileImagePreview">
									</div>
									<input type="file" accept="image/*" name="profileImage[]" id="profileImageFile" hidden>
									<div class="imageSettingsWrapper">
										<div class="blueButton" id="changeProfileImage">
											<i class="fa fa-camera"></i>
											Change image
										</div>
									</div>
								</div>

								<div class="settingsContentWrapper">
									<div class="settingsSubTitle">Cover image</div>
									<div class="settingsImageWrapper">
											<div class="shield"></div>
											<img src="/resources/img/<?php print getUserCover($user["id"]);?>" alt="settings cover image" class="settingsImage" id="coverImagePreview">
									</div>
									<div class="imageSettingsWrapper">
										<input type="file" accept="image/*" name="coverImage[]" hidden id="coverImageFile">
										<div class="blueButton" id="changeCover">
											<i class="fa fa-camera"></i>
											Change cover
										</div>
									</div>
								</div>
								<input type="submit" hidden id="saveImagesHidden" value="Save images">
								<div class="saveSettingsWrapper imagePassword">
									<div class="settingsFieldSet bottom">
										<label for="currentPassword">Your password</label>
										<input <?php if (isset($error) && $error == "Invalid password.") { print 'class="error"'; } ?> type="password" name="password" id="currentAccountPassword">
									</div>
								</div>
							</form>
							<div class="saveSettingsWrapper saveImagesWrapper">
								<div id="saveImagesSettings" class="blueButton">Save changes</div>
							</div>

						<?php } else if ($_GET["setting"] == "password") {?>

							<form action="/settings/password" method="post" id="passwordForm">
								<div class="settingsContentWrapper">
										<input type="text" hidden>
										<input type="password" hidden>
										<input type="text" hidden name="settingType" value="password">
										<div class="settingsFieldSet">
											<label for="newPassword">New password</label>
											<input type="password" name="newPass" id="newPassword">
										</div>
										<div class="settingsFieldSet lastFieldSet">
											<label for="newPasswordVal">Repeat password</label>
											<input type="password" name="newPassVal" id="newPasswordVal">
										</div>
										<div class="settingsFieldSet bottom">
											<label for="currentPassword">Current password</label>
											<input <?php if (isset($error) && $error == "Invalid password.") { print 'class="error"'; } ?> type="password" name="password" id="currentAccountPassword">
										</div>

								</div>
								<input type="submit" hidden id="hiddenSavePassword">
							</form>
							<div class="saveSettingsWrapper savePasswordWrapper">
								<div id="savePasswordSettings" class="blueButton">Save changes</div>
							</div>

						<?php }?>

					</div>
				</article>



			</div>
		</section>
	</body>
</html>

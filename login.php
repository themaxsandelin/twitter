<?php	
	require_once("resources/functions/data.php");
	$session = checkSession();
	if ($session) {
		header("Location: /");
		die();
	} else {
		$url = $_SERVER['REQUEST_URI'];
		$url = str_replace("/", "", $url);
		if ($url !== "login") {
			header("Location: /login");
			die();
		}
		$page = "login";
		if ($_SERVER["REQUEST_METHOD"] == "POST") {
			$postType = $_POST["postType"];
			if ($postType === "login") {
				$username = $_POST["username"];
				$password = $_POST["password"];
				if ($username !== "" || $password !== "") {
					$user = validateUser($username, $password);
					if ($user) {
						createSession($user);
						if (isset($_POST["origin"])) {
							header("Location: " . $_POST["origin"]);
							die();
						} else {
							redirectLogin();
						}
					} else {
						$error = $loginErrors[array_rand($loginErrors)];
					}
				} else {
					$error = "Leaving things empty won't make things better you know.";
				}
			} else if ($postType === "register") {
				$validRegister = true;
				$name = $_POST["regName"];
				$username = $_POST["regUsername"];
				$email = $_POST["regEmail"];
				$password = $_POST["regPassword"];
				$validName = validateName($name);
				$validUsername = validateUsername($username);
				$validEmail = validateEmail($email);
				$validPassword = validateRegPassword($password);
				if ($validName === "empty") {
					$error = "You forgot to enter a name, silly.";
					$validRegister = false;
				} else if ($validUsername === "empty") {
					$error = "You forgot to enter a username, silly.";
					$validRegister = false;
				} else if ($validUsername === "exists") {
					$error = "The username already exists, choose a different one.";
					$validRegister = false;
				} else if ($validEmail === "empty") {
					$error = "You forgot to enter an email address, silly.";
					$validRegister = false;
				} else if ($validEmail === "exists") {
					$error = "Looks like the email is already registered to an account.";
					$validRegister = false;
				} else if ($validPassword === "empty") {
					$error = "You forgot to enter a password, silly.";
					$validRegister = false;
				}
				if ($validRegister) {
					registerUser($name, $username, $email, $password);
					$message = "You are now registered! Please login to confirm the registration.";
				}
			}
		}
	}
?>
<html>
	<head>
		<?php require("resources/sections/head.php");?>
		<title>Twitter</title>
		<script src="/resources/js/login.js" type="text/javascript"></script>
	</head>
	<body>
		<?php require("resources/sections/header.php");?>
		<section id="loginPage">
			<div id="loginPageBackground"></div>
			<div class="loginOverlay"></div>
			<article id="loginPageContent">
				<?php if (isset($error)) {?>
					<div class="errorBox">
						<?php print $error;?>
						<div id="closeError" onclick="$(this).parent().remove();">
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
				<div class="loginGreetWrapper">
					<h1>Welcome to Twitter!</h1>
					<p>Connect with your friends — and other fascinating people. Get in-the-moment updates on the things that interest you. And watch events unfold, in real time, from every angle.</p>
				</div>
				<div class="loginFormCards">
					<div class="loginCard">
						<form action="/login" method="POST">
							<input type="hidden" name="postType" value="login">
							<label for="loginUsername">Email or username</label>
							<input id="loginUsername" class="userLoginField" type="text" name="username" value="<?php if (isset($_POST["username"])) { print $_POST["username"]; } ?>">
							<label for="loginPassword">Password</label>
							<input id="loginPassword" class="userLoginField" type="password" name="password" value="<?php if (isset($_POST["password"])) { print $_POST["password"]; } ?>">
							<?php if (isset($_POST["origin"])) {?>
								<input type="text" name="origin" hidden value="<?php print $_POST["origin"];?>">
							<?php }?>
							<button hidden id="hiddenLogin" type="submit">Log in</button>
							<div class="blueButton" id="loginButton">Log in</div>
						</form>
					</div>
					<div class="loginCard registerCard">
						<div class="registerMessage"><span>New to Twitter?</span> Sign up</div>
						<form action="/login" method="POST">
							<input type="text" hidden>
							<input type="password" hidden>
							<input type="hidden" name="postType" value="register">
							<label for="registerName">Enter your full name</label>
							<input id="registerName" class="userLoginField" type="text" name="regName" value="<?php if (isset($error) && isset($_POST["regName"])) { print $_POST["regName"]; } ?>">
							<label for="registerUsername">Choose a username</label>
							<input id="registerUsername" class="userLoginField" type="text" name="regUsername" value="<?php if (isset($error) && isset($_POST["regUsername"])) { print $_POST["regUsername"]; } ?>">
							<label for="registerEmail">Enter your email</label>
							<input id="registerEmail" class="userLoginField" type="email" name="regEmail" value="<?php if (isset($error) && isset($_POST["regEmail"])) { print $_POST["regEmail"]; } ?>">
							<label for="registerPassword">Choose a password</label>
							<input id="registerPassword" class="userLoginField" type="password" name="regPassword">
							<button hidden id="hiddenRegister" type="submit"></button>
							<div class="orangeButton" id="registerButton">Sign up for Twitter</div>
						</form>
					</div>
				</div>
			</article>
		</section>
		
	</body>
</html>
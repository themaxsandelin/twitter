<?php
	require_once("resources/functions/data.php");

	$url = getPageName();
	if (preg_match("/^profile\.php\?username\=.*/", $url)) {
		$username = explode("=", $url)[1];
		header("Location: " . $username);
		die();
	}

	$session = checkSession();
	if (isset($_GET["username"])) {
		$userExists = checkUsername($_GET["username"]);
		if ($_GET["username"] == "") {
			if ($session === false) {
				header("Location: login.php");
				die();
			}
			$user = getUserInfo($_SESSION["userID"]);
			$profile = getUserInfo(getUserID($_GET["username"])[0]["user_id"]);
			header("Location: " . $user["username"]);
		} else {
			if ($userExists) {
				if ($session) {
					$user = getUserInfo($_SESSION["userID"]);
					$profile = getUserInfo(getUserID($_GET["username"])[0]["user_id"]);
				} else {
					$profile = getUserInfo(getUserID($_GET["username"])[0]["user_id"]);
				}
			} else {
				print "This user doesn't exist.";
			}
		}
	} else {
		if ($session ===	false) {
			header("Location: login.php");
			die();
		}
		$user = getUserInfo($_SESSION["userID"]);
		$profile = getUserInfo(getUserID($_GET["username"])[0]["user_id"]);
		header("Location: " . $user["username"]);
	}
	$page = "profile";
?>
<html>
	<head>
		<title><?php print $profile["name"] . " " . "@" . $profile["username"] . " | Twitter";?></title>
		<?php require("resources/sections/head.php");?>
		<script src="/resources/js/user.js" type="text/javascript"></script>
	</head>
	<body>
		<?php require("resources/sections/header.php");?>
		<div id="userFixedContent">
			<div id="userCoverImageWrapper">
				<div class="userCoverImage" style="background:url(/resources/img/<?php print getUserCover($profile["id"]);?>) no-repeat center center; background-size:cover;"></div>
			</div>
			<div id="userMenuBar">
				<div class="wrapper">
					<div id="bigUserImageWrapper">
						<div class="profileUserImage" style="background:url(/resources/img/<?php print getUserImage($profile["id"]); ?>) no-repeat center center; background-size:cover;"></div>
					</div>
					<div id="smallUserInfoWrapper">
						<div id="smallUserInfoMoveWrapper">
							<div class="smallUserImageWrapper">
								<div class="shield"></div>
								<div class="profileUserImage" style="background:url(/resources/img/<?php print getUserImage($profile["id"]); ?>) no-repeat center center; background-size:cover;"></div>
							</div>
							<div class="smallUserTextWrapper">
								<h3 class="userSmallName"><?php print $profile["name"];?></h3>
								<h6 class="userSmallUsername"><?php print "@" . $profile["username"];?></h6>
							</div>
						</div>
					</div>
					<div class="moveRight">
						<?php if (isset($user)) {
							if ($user["username"] == $profile["username"]) {?>
							<a href="/settings/account">
								<div class="button greyButton">Settings</div>
							</a>
						<?php } else {?>
							<?php if (checkFollowing($_SESSION["userID"], $profile["id"])) { ?>
								<div class="blueButton" id="unfollowUser"></div>
							<?php } else { ?>
								<div class="blueButton" id="followUser">Follow</div>
							<?php }?>
						<?php } }?>
					</div>
				</div>
			</div>
		</div>
		<section id="userPage">
			<div id="fixedUserPageContent">
				<div class="wrapper">
					<article id="userSideBar">
						<div class="userTitleWrapper">
							<h2>
								<a href="/<?php print $profile["username"]; ?>"><?php print $profile["name"]; ?></a>
							</h2>
							<h4 class="textMargin">
								<a href="/<?php print $profile["username"]; ?>"><?php print "@".$profile["username"]; ?></a>
							</h4>
							<?php if ($profile["bio"] !== "") {?>
								<p class="textMargin"><?php print $profile["bio"]; ?></p>
							<?php }?>
							<?php if ($profile["url"] !== "") {?>
								<a href="<?php print $profile["url"]; ?>" target="_blank">
									<div class="website">
										<i class="fa fa-link linkIcon"></i>
										<p><?php print printUserLink($profile["url"]);?></p>
									</div>
								</a>
							<?php }?>
						</div>
					</article>
					<article id="profileTweets" class="wide">
						<div class="profielTweetsContainer">
							<?php
								$tweets = getTweetsFromUser($profile["id"]);
								if ($tweets) {
									$tweetNum = 1;
									foreach ($tweets as $tweet) {
										$tweetUser = $profile;
							?>
									<div class="tweetWrapper <?php if ($tweetNum === 1) { print "topUserTweet"; } ?>">
										<a href="/<?php print $profile["username"]; ?>/status/<?php print $tweet["id"]; ?>">
											<button hidden id="statusRed"></button>
										</a>
										<div class="tweetContentContainer" data-original-height="0">
											<div class="tweetPaddingContainer">
												<div class="tweetUserImageWrapper">
													<div class="userImage" style="background:url(/resources/img/<?php print getUserImage($tweetUser["id"]); ?>) no-repeat center center; background-size:cover;"></div>
												</div>
												<div class="tweetContentWrapper">
													<div class="tweetAuthor">
														<?php print $tweetUser["name"];?>
													</div>
													<div class="authorUsername"><?php print "&nbsp;@" . $tweetUser["username"]?></div>
													<div class="tweetDate"><?php print "· ".calcElapsedTweet($tweet["published"]);?></div>
													<div class="tweetText"><?php print nl2br(formatTweetContent($tweet["content"]));?></div>
													<div class="regularView">
														<?php if ($image = getTweetImage($tweetUser["id"], $tweet["tweet_id"])) {?>
															<div class="tweetViewImage" style="background:url(resources/img/users/<?php print $image; ?>) no-repeat center center; background-size:cover;"></div>
														<?php }?>
													</div>
												</div>
											</div>
										</div>
									</div>
								<?php $tweetNum++; }?>
								<div class="bottomTweetMessageWrapper">That's all folks!</div>
							<?php }?>
						</div>
					</article>
				</div>
			</div>
		</section>
	</body>
</html>

<?php

require_once("resources/functions/data.php");

$session = checkSession();
if ($session === false) {
	header("Location: login");
	die();
}
if (getPageName() == "index.php") {
	header("Location: /");
	die();
}
$user = getUserInfo($_SESSION["userID"]);

?>
<html>
	<head>
		<title>Twitter</title>
		<?php require("resources/sections/head.php");?>
		<script src="resources/js/home.js" type="text/javascript"></script>
	</head>
	<body>
		<?php require("resources/sections/header.php");?>

		<section id="homePage">
			<div class="wrapper">
				<article id="profileSideBar" class="sideBar">
					<div class="profileSideBarInner">
						<div class="userCoverImageSideWrapper">
							<div class="userCoverImage" style="background:url(resources/img/<?php print getUserCover($user["id"]);?>) no-repeat center center; background-size:cover;"></div>
						</div>
						<div class="userSideBarInfoWrapper">
							<a href="/<?php print $user["username"];?>">
								<div class="siderBarProfileImageWrapper">
									<div class="siderBarProfileImage" style="background:url(resources/img/<?php print getUserImage($user["id"]); ?>) no-repeat center center; background-size:cover;"></div>
								</div>
							</a>
							<div class="userSideBarInfo">
								<a href="/<?php print $user["username"];?>">
									<h3 class="userSideBarName"><?php print $user["name"];?></h3>
								</a>
								<a href="/<?php print $user["username"];?>">
									<h6 class="userSibeBarUsername"><?php print "@" . $user["username"];?></h6>
								</a>
							</div>
							<div class="userSideBarStatWrapper">
								<a href="">
									<div class="userSideBarStat" id="sideBarTweets">
										<div class="userSideBarStatTitle">Tweets</div>
										<div class="userSideBarStatValue"><?php print $user["tweets"];?></div>
									</div>
								</a>
								<a href="">
									<div class="userSideBarStat" id="sideBarFollowing">
										<div class="userSideBarStatTitle">Following</div>
										<div class="userSideBarStatValue"><?php print $user["following"];?></div>
									</div>
								</a>
								<a href="">
									<div class="userSideBarStat" id="sideBarFollowers">
										<div class="userSideBarStatTitle">Followers</div>
										<div class="userSideBarStatValue"><?php print $user["followers"];?></div>
									</div>
								</a>
							</div>
						</div>
					</div>
				</article>
				<article id="tweets" class="wide" data-active-tweet="">
					<div class="innerArticle">
						<div class="createTweetWrapper">
							<div class="createTweetUserWrapper">
								<div class="userImage" style="background:url(resources/img/users/<?php print getUserImage($user["id"]); ?>) no-repeat center center; background-size:cover;"></div>
							</div>
							<div class="createTweetContentWrapper">
								<div class="clickTweet">What's happening?</div>
								<div class="createTweetContent">
									<form method="post" id="homeTweetForm" enctype="multipart/form-data">
										<input type="hidden" name="message" value="newHomeTweet">
										<textarea name="tweet" class="createTweetTextarea" contenteditable></textarea>
										<input name="tweetImage[]" type="file" accept="image/*" hidden id="createTweetUpload">
										<input type="submit" hidden id="hiddenTweetButton">
									</form>
									<div class="tweetImagePreview">

									</div>
									<div class="createTweetAddImage">
										<svg class="createTweetAddImageIcon" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" xmlns:a="http://ns.adobe.com/AdobeSVGViewerExtensions/3.0/" x="0px" y="0px" width="100px" height="80px" viewBox="0 0 100 80" enable-background="new 0 0 100 80" xml:space="preserve"><defs></defs><g><path fill="#5BB7EA" d="M50,30c-8.3,0-15,6.7-15,15c0,8.3,6.7,15,15,15c8.3,0,15-6.7,15-15C65,36.7,58.3,30,50,30z M90,15H78 c-1.7,0-3.4-1.3-3.9-2.8l-3.1-9.3C70.4,1.3,68.7,0,67,0H33c-1.6,0-3.4,1.3-3.9,2.8l-3.1,9.3C25.4,13.7,23.6,15,22,15H10C4.5,15,0,19.5,0,25v45c0,5.5,4.5,10,10,10h80c5.5,0,10-4.5,10-10V25C100,19.5,95.5,15,90,15z M50,70c-13.8,0-25-11.2-25-25c0-13.8,11.2-25,25-25c13.8,0,25,11.2,25,25C75,58.8,63.8,70,50,70z M86.5,32c-1.9,0-3.5-1.6-3.5-3.5c0-1.9,1.6-3.5,3.5-3.5c1.9,0,3.5,1.6,3.5,3.5C90,30.4,88.4,32,86.5,32z"/></g></svg>
										<span>Add image</span>
									</div>
									<div class="moveRight">
										<div class="tweetChars">140</div>
										<div class="blueButton disabled" id="createHomeTweet">Tweet</div>
									</div>
								</div>
							</div>
						</div>
						<?php
						$tweets = getHomeTweets($_SESSION["userID"]);
						$tweetNum = 1;
						foreach ($tweets as $tweet) {
							require("resources/sections/tweet.php");
						}
						?>
						<div class="bottomTweetMessageWrapper">There were no more tweets to load</div>
					</div>
				</article>
				<article class="desktopOnly">
					<div class="test"></div>
				</article>
			</div>
		</section>
	</body>
</html>

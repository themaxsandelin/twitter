<?php
	require("resources/functions/data.php");
	$session = checkSession();
	if ($session === false) {
		header("Location: /login");
		die();
	}

	$url = $_SERVER['REQUEST_URI'];
	$url = str_replace("/", "", $url);

	if (strpos($url, "/?") !== false && strpos($url, "/?s=") === false ) {
		header("Location: /search/");
		die();
	}

	if (isset($_GET["s"])) {
		if ($_GET["s"] === "") {
			header("Location: /");
			die();
		} else {
			$foundUsers = false;
			$searchUsers = false;
			$tweets = [];
			$search = $_GET["s"];
			preg_match_all("/(@\w+)/", $search, $users);
			$users = $users[0];
			if ($users) {
				if (count($users) == 1) {
					$searchUsers = true;
				}
			}
		}
		if ($searchUsers) {
			$user = str_replace("@", "", $users[0]);
			$foundUsers = searchSpecificUser($user);
		} else {
			$foundUsers = searchUser($search);
		}
		if ($searchUsers && $foundUsers) {
			foreach ($foundUsers as $user) {
				$userTweets = getTweetsFromUser($user["user_id"]);
				if ($userTweets) {
					foreach ($userTweets as $userTweet) {
						$tweets[] = $userTweet;
					}
				}
			}
		}
		$nameUsers = searchUsersName($search);
		if ($nameUsers && $foundUsers) {
			foreach ($nameUsers as $nameUser) {
				if (in_array($nameUser, $foundUsers) === false) {
					$foundUsers[] = $nameUser;
				}
			}
		} else if ($nameUsers && $foundUsers === false) {
			$foundUsers = $nameUsers;
		}
		$result = searchTweets($search);
		foreach ($result as $tweetRes) {
			$tweets[] = $tweetRes;
		}
		arsort($tweets);
	} else {
		header("Location: /");
		die();
	}

	if ($session) {
		$user = getUserInfo($_SESSION["userID"]);
	}
?>
<html>
	<head>
		<title><?php if (isset($_GET["s"])) { print $_GET["s"] . " - "; }?>Twitter Search</title>
		<?php require("resources/sections/head.php");?>
		<script src="/resources/js/header.js" type="text/javascript"></script>
	</head>
	<body>
		<?php require("resources/sections/header.php");?>
		<section id="homePage">
			<div class="wrapper">
				<article id="profileSideBar" class="sideBar">
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
				<article class="wide" id="searchArticle">
					<div class="resultBanner">
						Results for <span><?php if (strlen($search) > 30) { print substr($search, 0, 30) . "..."; } else { print $search; } ?></span>
					</div>

					<?php if ($foundUsers) {?>
						<div class="resultUsersWrapper">
							<?php foreach ($foundUsers as $user) {?>
								<div class="resultUserBoxContainer">
									<a href="/<?php print $user["username"]; ?>">
										<div class="resultUserBox">
											<div class="resultUserCoverImage" style="background:url(/resources/img/<?php print getUserCover($user["user_id"]);?>) no-repeat center center; background-size:cover;"></div>
											<div class="resultUserInfoWrapper">
												<div class="resultUserImage" style="background:url(/resources/img/<?php print getUserImage($user["user_id"]); ?>) no-repeat center center; background-size:cover;"></div>
												<div class="resultUserCred">
													<h4><?php print $user["name"]; ?></h4>
													<h5><?php print "@" . $user["username"]?></h5>
												</div>
											</div>
										</div>
									</a>
								</div>
							<?php }?>
						</div>
					<?php }?>

					<?php if (empty($tweets) === false) {
						$tweetNum = 1;
						foreach ($tweets as $tweet) {
							if ($tweetNum === 1) {
								$borderTop = true;
							} else {
								$borderTop = false;
							}
							$tweetUser = getUserInfo($tweet["author_id"]);
					?>
					<div onclick="window.location.href='/<?php print $tweetUser["username"] . "/status/" . $tweet["id"];?>'" class="tweetWrapper <?php if ($tweetNum === 1) { print "top"; } if (isset($borderTop) && $borderTop && $foundUsers) { print " borderTop"; } ?>" id="post<?php print $tweetNum; ?>" data-activated="false" data-tweet-id="<?php print $tweet["tweet_id"]; ?>" data-author-id="<?php print $tweet["author_id"]; ?>">
						<div class="tweetContentContainer" data-original-height="0">
							<div class="tweetPaddingContainer">
								<a href="/<?php print $tweetUser["username"];?>" class="tweetAuthorHoverOutside">
									<div class="tweetUserImageWrapper">
										<div class="userImage" style="background:url(/resources/img/users/<?php print getUserImage($tweetUser["id"]); ?>) no-repeat center center; background-size:cover;"></div>
									</div>
								</a>
								<div class="tweetContentWrapper">
									<a href="/<?php print $tweetUser["username"];?>" class="tweetAuthorHover">
										<div class="tweetAuthor">
											<?php print $tweetUser["name"];?>
										</div>
										<div class="authorUsername"><?php print "&nbsp;@" . $tweetUser["username"]?></div>
									</a>
									<a href="/<?php print $tweetUser["username"];?>">
										<div class="tweetDate"><?php print " ".calcElapsedTweet($tweet["published"]);?></div>
									</a>
									<div class="tweetText"><?php print nl2br(formatTweetContent($tweet["content"]));?></div>
									<div class="regularView">
										<?php if ($image = getTweetImage($tweetUser["id"], $tweet["tweet_id"])) {?>
											<div class="tweetViewImage" style="background:url(/resources/img/users/<?php print $image; ?>) no-repeat center center; background-size:cover;"></div>
										<?php }?>
									</div>
								</div>
							</div>
						</div>
					</div>
					<?php $tweetNum++; }?>
						<div class="bottomTweetMessageWrapper">That's all folks!</div>
					<?php } else { ?>
						<div class="noTweetsSearchResult <?php if ($foundUsers) { print "borderTop"; }?>">
							<svg class="twitterLogo" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" xmlns:a="http://ns.adobe.com/AdobeSVGViewerExtensions/3.0/" x="0px" y="0px" width="50px" height="40.6px" viewBox="0 0 50 40.6" enable-background="new 0 0 50 40.6" xml:space="preserve"><defs></defs><path fill="#8899a6" d="M15.7,40.6C9.9,40.6,4.5,38.9,0,36c0.8,0.1,1.6,0.1,2.4,0.1c4.8,0,9.2-1.6,12.7-4.4c-4.5-0.1-8.3-3-9.6-7.1c0.6,0.1,1.3,0.2,1.9,0.2c0.9,0,1.8-0.1,2.7-0.4C5.5,23.5,2,19.4,2,14.4c0,0,0-0.1,0-0.1c1.4,0.8,3,1.2,4.6,1.3c-2.8-1.8-4.6-5-4.6-8.5c0-1.9,0.5-3.6,1.4-5.2c5.1,6.2,12.6,10.3,21.1,10.7c-0.2-0.8-0.3-1.5-0.3-2.3C24.4,4.6,29,0,34.6,0c3,0,5.6,1.2,7.5,3.2c2.3-0.5,4.5-1.3,6.5-2.5c-0.8,2.4-2.4,4.4-4.5,5.7c2.1-0.2,4.1-0.8,5.9-1.6c-1.4,2.1-3.1,3.9-5.1,5.3c0,0.4,0,0.9,0,1.3C44.9,25,34.6,40.6,15.7,40.6"/></svg>
							<p>No Tweet results for <span><?php if (strlen($search) > 30) { print substr($search, 0, 30) . "..."; } else { print $search; } ?></span></p>
						</div>
					<?php }?>
				</article>
			</div>
		</section>

	</body>
</html>

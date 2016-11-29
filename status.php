<?php
	require_once("resources/functions/data.php");
	$session = checkSession();
	if ($session === false) {
		header("Location: /login");
		die();
	}
    if (isset($_GET["id"]) === false) {
        header("Location: /");
        die();
    }
    if (isset($_GET["username"]) === false) {
        header("Location: /");
        die();
    }
	$user = getUserInfo($_SESSION["userID"]);
	$tweet = getTweet($_GET["id"]);
	$author = getUserInfo($tweet["author_id"]);
?>
<html>
	<head>
		<title>Twitter</title>
		<?php require("resources/sections/head.php");?>
		<script src="/resources/js/status.js" type="text/javascript"></script>
	</head>
    <body>
        <?php require("resources/sections/header.php");?>
        <section id="statusPage">
            <article id="statusContainer">
				<div class="statusWrapper" data-activated="false" data-tweet-id="<?php print $tweet["tweet_id"]; ?>" data-author-id="<?php print $tweet["author_id"]; ?>">
					<div class="statusTweetWrapper">
						<div class="statusTweetAuthorBar">
							<a href="/<?php print $author["username"]; ?>">
								<div class="statusTweetAuthorImage" style="background:url(/resources/img/<?php print getUserImage($author["id"]); ?>) no-repeat center center; background-size:cover;"></div>
							</a>
							<div class="statusTweetAuthorInfo">
								<a href="/<?php print $author["username"]; ?>">
									<h3><?php print $author["name"]; ?></h3>
								</a>
								<a href="/<?php print $author["username"]; ?>">
									<h4><?php print "@".$author["username"]; ?></h4>
								</a>
							</div>
							<?php if ($user["id"] !== $author["id"]) {?>
								<div class="followWrapper">
									<?php if (isset($user)) {
										if ($user["username"] == $author["username"]) {?>
										<a href="/settings/account">
											<div class="button greyButton">Settings</div>
										</a>
									<?php } else {?>
										<?php if (checkFollowing($_SESSION["userID"], $author["id"])) { ?>
											<div class="blueButton" id="unfollowUser"></div>
										<?php } else { ?>
											<div class="blueButton" id="followUser">Follow</div>
										<?php }?>
									<?php } }?>
								</div>
							<?php }?>
						</div>
						<div class="statusTweetContentWrapper">
							<div class="statusTweetContent"><?php print nl2br(formatTweetContent($tweet["content"])); ?></div>
							<div class="statusTweetToolbar tweetToolbar">
								<div class="favouriteTool  <?php if (checkFavourited($tweet["tweet_id"], $user["id"])) { print "favourited"; }?>">
									<div class="favourite favouriteClick ">
										<i class="fa fa-star favouriteIcon"></i>
									</div>
								</div>
								<?php if ($_SESSION["userID"] === $author["id"] || getAdmin($_SESSION["userID"]) === "1") {?>
									<div class="toolBarMargin removeTweet ">
										<i class="fa fa-trash removeIcon"></i>
									</div>
								<?php }?>
							</div>
						</div>
						<?php if ($image = getTweetImage($author["id"], $tweet["tweet_id"])) {?>
							<div class="statusTweetImageWrapper">
								<div class="shield"></div>
								<img src="/resources/img/users/<?php print $image; ?>" alt="Status tweet image" class="statusTweetImage">
							</div>
						<?php }?>
						<?php if (getTweetFavouriteNum($tweet["tweet_id"]) > 0) {?>
							<div class="responceDisplayWrapper">
								<div class="statCountWrapper">
									<div class="statCountTitle">Favourites</div>
									<div class="statCount"><?php getFormatedFavouriteCount($tweet["tweet_id"]); ?></div>
								</div>
								<div class="favouriteUsersWrapper">
									<?php
										$favUsers = getFavouriteUsers($tweet["tweet_id"]);
										foreach ($favUsers as $favUser) {
											$favUsername = getUserInfo($favUser["user_id"])["username"];
											?>
											<a href="/<?php print $favUsername; ?>">
												<div class="favouriteUserImageWrapper">
													<div class="favouriteUserImage" style="background:url(/resources/img/<?php print getUserImage($favUser["user_id"]); ?>) no-repeat center center; background-size:cover;"></div>
												</div>
											</a>
									<?php }?>
								</div>
							</div>
						<?php }?>
						<div class="tweetDetailTime">
							<?php print getFullTimeElapsed($tweet["published"]); ?>
						</div>
					</div>
					<div class="statusTweetInlineReplyWrapper inlineReplyWrapper preventTweetAction">
						<div class="replyUserImage" style="background:url(/resources/img/<?php print getUserImage($user["id"]); ?>) no-repeat center center; background-size:cover;"></div>
						<div class="clickToReply">Reply to <span>@<?php print $author["username"]; ?></span></div>
						<div class="createReplyWrapper">
							<input type="hidden" class="twid" value="<?php print $tweet["tweet_id"]; ?>">
							<textarea name="replyContent" class="replyContent" data-original-content="@<?php print $author["username"]; ?>"></textarea>
							<div class="replyTweetControls">
								<div class="replyChars"><?php print 140 - strlen("@".$author["username"]." ");?></div>
								<div class="button blueButton disabled replyTweet">Reply</div>
							</div>
						</div>
					</div>
					<div class="tweetReplyWrapper preventTweetAction">
						<?php
							$replies = getAllTweetReplies($tweet["tweet_id"]);
							foreach ($replies as $reply) {
								$replyUser = getUserInfo($reply["author_id"]);
						?>
						<div class="statusTweetReply tweetReply" data-reply-id="<?php print $reply["tweet_id"]; ?>" >
							<a href="/<?php print $replyUser["username"];?>">
								<div class="statusReplyImage replyUserImage" style="background:url(/resources/img/<?php print getUserImage($reply["author_id"]); ?>) no-repeat center center; background-size:cover;"></div>
							</a>
							<div class="replyContentWrapper">
								<a href="/<?php print $replyUser["username"];?>" class="tweetAuthorHover">
									<div class="tweetAuthor">
										<?php print $replyUser["name"];?>
									</div>
									<div class="authorUsername"><?php print "&nbsp;@" . $replyUser["username"]?></div>
								</a>
								<a href="/<?php print $replyUser["username"];?>">
									<div class="tweetDate"> <?php print calcElapsedTweet($reply["published"]);?></div>
								</a>
								<?php if ($_SESSION["userID"] === $replyUser["id"] || getAdmin($_SESSION["userID"]) === "1") {?>
									<div class="removeReply preventTweetAction">
										<i class="fa fa-trash removeIcon"></i>
									</div>
								<?php }?>
								<div class="replyText"><?php print nl2br(formatTweetContent($reply["content"]));?></div>
							</div>
						</div>
						<?php }?>
					</div>
				</div>
            </article>
        </section>
    </body>
</html>

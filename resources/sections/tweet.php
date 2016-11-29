<?php $tweetUser = getUserInfo($tweet["author_id"]); ?>
<div class="tweetWrapper <?php if ($tweetNum === 1) { print "top"; } else if (count($tweets) == $tweetNum) { print "bottom"; } ?>" id="post<?php print $tweetNum; ?>" data-activated="false" data-tweet-id="<?php print $tweet["tweet_id"]; ?>" data-author-id="<?php print $tweet["author_id"]; ?>">
	<div class="tweetContentContainer" data-original-height="0">
		<div class="tweetPaddingContainer">
			<a href="/<?php print $tweetUser["username"];?>" class="tweetAuthorHoverOutside">
				<div class="tweetUserImageWrapper">
					<div class="userImage" style="background:url(/resources/img/<?php print getUserImage($tweetUser["id"]); ?>) no-repeat center center; background-size:cover;"></div>
				</div>
			</a>
			<div class="tweetContentWrapper">
				<a href="/<?php print $tweetUser["username"];?>" class="tweetAuthorHover">
					<div class="tweetAuthor">
						<?php print $tweetUser["name"];?>
					</div>
					<div class="authorUsername"><?php print "&nbsp;@" . $tweetUser["username"]?></div>
				</a>
				<a href="/<?php print $tweetUser["username"] . "/status/" . $tweet["id"];?>">
					<div class="tweetDate">· <span><?php print calcElapsedTweet($tweet["published"]);?></span></div>
				</a>
				<div class="tweetText"><?php print nl2br(formatTweetContent($tweet["content"]));?></div>
				<div class="regularView">
					<?php if ($image = getTweetImage($tweetUser["id"], $tweet["tweet_id"])) {?>
						<div class="tweetViewImage" style="background:url(resources/img/users/<?php print $image; ?>) no-repeat center center; background-size:cover;"></div>
						<?php }?>
						<div class="tweetToolbar">
							<div class="replyTool replyClick">
								<div class="replyToTweet replyClick">
									<i class="fa fa-reply replyIcon replyClick"></i>
								</div>
								<div class="replyCount replyClick toolBarNum">
									<?php getFormatedReplyCount($tweet["tweet_id"]); ?>
								</div>
							</div>
							<div class="toolBarMargin visibleFavourite favouriteTool preventTweetAction <?php if (checkFavourited($tweet["tweet_id"], $user["id"])) { print "favourited"; }?>">
								<div class="favourite favouriteClick preventTweetAction">
									<i class="fa fa-star favouriteIcon"></i>
								</div>
								<div class="favouriteClick favouriteCount toolBarNum">
									<?php getFormatedFavouriteCount($tweet["tweet_id"]); ?>
								</div>
							</div>
							<?php if ($_SESSION["userID"] === $tweetUser["id"] || getAdmin($_SESSION["userID"]) === "1") {?>
								<div class="toolBarMargin removeTweet preventTweetAction">
									<i class="fa fa-trash removeIcon"></i>
								</div>
								<?php }?>
							</div>
						</div>
						<div class="activeView">
							<?php if ($image = getTweetImage($tweetUser["id"], $tweet["tweet_id"])) {?>
								<div class="tweetActiveImageWrapper">
									<div class="shield"></div>
									<img src="resources/img/users/<?php print $image;?>" alt="tweetImage" class="tweetActiveImageView">
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
											<?php print getFullTimeElapsed($tweet["published"]) . " · "; ?>
											<a href="/<?php print $tweetUser["username"] . "/status/" . $tweet["id"]; ?>">Details</a>
										</div>
										<div class="tweetToolbar">
											<div class="replyTool replyClick preventTweetAction">
												<div class="replyToTweet replyClick preventTweetAction">
													<i class="fa fa-reply replyIcon replyClick"></i>
												</div>
											</div>
											<div class="toolBarMargin hiddenFavourite favouriteTool preventTweetAction <?php if (checkFavourited($tweet["tweet_id"], $user["id"])) { print "favourited"; }?>">
												<div class="favourite favouriteClick preventTweetAction">
													<i class="fa fa-star favouriteIcon"></i>
												</div>
											</div>
											<?php if ($_SESSION["userID"] === $tweetUser["id"] || getAdmin($_SESSION["userID"]) === "1") {?>
												<div class="toolBarMargin removeTweet preventTweetAction">
													<i class="fa fa-trash removeIcon"></i>
												</div>
												<?php }?>
											</div>
										</div>
									</div>
								</div>
								<div class="activeBottomView">
									<div class="inlineReplyWrapper preventTweetAction">
										<div class="replyUserImage" style="background:url(resources/img/<?php print getUserImage($user["id"]); ?>) no-repeat center center; background-size:cover;"></div>
										<div class="clickToReply">Reply to <span>@<?php print $tweetUser["username"]; ?></span></div>
										<div class="createReplyWrapper">
											<input type="hidden" class="twid" value="<?php print $tweet["tweet_id"]; ?>">
											<textarea name="replyContent" class="replyContent" id="replyTo<?php print $tweetNum?>" data-original-content="@<?php print $tweetUser["username"]; ?>"></textarea>
											<div class="replyTweetControls">
												<div class="replyChars"><?php print 140 - strlen("@".$tweetUser["username"]." ");?></div>
												<div class="button blueButton disabled replyTweet">Reply</div>
											</div>
										</div>
									</div>
									<div class="tweetReplyWrapper preventTweetAction">
										<?php
										$replies = getTweetReplies($tweet["tweet_id"]);
										foreach ($replies as $reply) {
											$replyUser = getUserInfo($reply["author_id"]);
											?>
											<div class="tweetReply" data-reply-id="<?php print $reply["tweet_id"]; ?>" >
												<a href="/<?php print $replyUser["username"];?>">
													<div class="replyUserImage" style="background:url(resources/img/<?php print getUserImage($reply["author_id"]); ?>) no-repeat center center; background-size:cover;"></div>
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
												<?php }
												if (getTweetReplyNum($tweet["tweet_id"]) >= 5) {
													?>
													<div class="viewFullTweet">
														<a href="/<?php print $tweetUser["username"] . "/status/" . $tweet["id"]; ?>">
															View more in conversation
															<i class="fa fa-long-arrow-right"></i>
														</a>
													</div>
													<?php }?>
												</div>
											</div>
										</div>
									</div>
									<?php $tweetNum++; ?>

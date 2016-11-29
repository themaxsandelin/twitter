<header>
	<div class="wrapper">

		<?php if ($session) {?>

			<ul id="mainMenu">
				<?php if (getPageName() !== "index.php") { print '<a href="/">';}?>
					<li <?php if (getPageName() == "index.php" || getPageName() === false) { print 'class="selected"'; }?>>
						<i class="fa fa-home" id="homeIcon"></i>
						Home
						<div class="bar"></div>
					</li>
					<?php if (getPageName() !== "index.php") { print '</a>'; }?>
				</ul>

				<?php }?>

				<?php if ($session === false && $page === "profile") {?>

					<div class="twitterLogoWrapperLeft">

						<?php } else { ?>

							<div class="twitterLogoWrapperCenter">

								<?php }?>

								<a href="/">
									<svg class="twitterLogo" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" xmlns:a="http://ns.adobe.com/AdobeSVGViewerExtensions/3.0/" x="0px" y="0px" width="50px" height="40.6px" viewBox="0 0 50 40.6" enable-background="new 0 0 50 40.6" xml:space="preserve"><defs></defs><path fill="#5EB6E6" d="M15.7,40.6C9.9,40.6,4.5,38.9,0,36c0.8,0.1,1.6,0.1,2.4,0.1c4.8,0,9.2-1.6,12.7-4.4c-4.5-0.1-8.3-3-9.6-7.1c0.6,0.1,1.3,0.2,1.9,0.2c0.9,0,1.8-0.1,2.7-0.4C5.5,23.5,2,19.4,2,14.4c0,0,0-0.1,0-0.1c1.4,0.8,3,1.2,4.6,1.3c-2.8-1.8-4.6-5-4.6-8.5c0-1.9,0.5-3.6,1.4-5.2c5.1,6.2,12.6,10.3,21.1,10.7c-0.2-0.8-0.3-1.5-0.3-2.3C24.4,4.6,29,0,34.6,0c3,0,5.6,1.2,7.5,3.2c2.3-0.5,4.5-1.3,6.5-2.5c-0.8,2.4-2.4,4.4-4.5,5.7c2.1-0.2,4.1-0.8,5.9-1.6c-1.4,2.1-3.1,3.9-5.1,5.3c0,0.4,0,0.9,0,1.3C44.9,25,34.6,40.6,15.7,40.6"/></svg>
								</a>
							</div>
							<div class="moveRight">

								<?php if (getPageName() !== "login") {?>

									<form action="/search/" method="get" class="searchForm">
										<input type="text" class="search" name="s" placeholder="Search Twitter" <?php if (isset($search) && $search !== "") { print 'value="'.$search.'"'; }?>>
										<button type="submit" id="searchTwitter">
											<i class="fa fa-search" id="searchIcon"></i>
										</button>
									</form>

									<?php }?>

									<?php if ($session) {?>

										<div class="userHead">
											<div class="userHeadImageWrapper">
												<div class="userImage" style="background:url(/resources/img/<?php print getUserImage($user["id"]); ?>) no-repeat center center; background-size:cover;"></div>
											</div>
											<div class="userDropdown hide">
												<div class="userDropdownTable">
													<svg class="userDropdownArrow" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" xmlns:a="http://ns.adobe.com/AdobeSVGViewerExtensions/3.0/" x="0px" y="0px" width="14px" height="13px" viewBox="0 0 14 13" enable-background="new 0 0 14 13" xml:space="preserve"><defs></defs><polygon fill="#E6E6E6" points="14,13 13.3,13 7,7.1 0.7,13 0,13 7,0 "/><polygon fill="#FFFFFF" points="7,1.3 13.3,13 0.7,13 "/></svg>
													<ul class="userDropdownMenu">

														<?php print '<a href="/'.$user["username"].'">'; ?>
															<li>
																<div class="largeItem"><?php print $user["name"];?></div>
																<input type="hidden" id="heusna" value="<?php print $user["username"]; ?>">
																<input type="hidden" id="heusn" value="<?php print $user["name"]; ?>">
																<input type="hidden" id="heusi" value="<?php print $user["id"]; ?>">
																<input type="hidden" id="heusp" value="<?php print getUserImage($user["id"]); ?>">
																<div class="smallItem">View profile</div>
															</li>
															<?php print "</a>";?>
															<hr>
															<?php if (preg_match("/^settings\/.*/", getPageName()) === 0) {print '<a href="/settings/account">'; }?>
																<li>
																	<div class="regularItem">Settings</div>
																</li>
																<?php if (preg_match("/^settings\/.*/", getPageName()) === 0) {print '</a>'; }?>
																<a href="/logout">
																	<li>
																		<div class="regularItem">Log out</div>
																	</li>
																</a>
															</ul>
														</div>
													</div>
												</div>
												<div class="blueButton" id="newTweet">Tweet</div>

												<?php } else { ?>

													<?php if ($page !== "login") { ?>

														<div class="userLoginWrapper">
															<div class="userLoginTextWrapper">
																<span>Have an account?</span>
																Log in
															</div>
															<div class="loginArrowWrapper">
																<div class="loginArrow"></div>
															</div>
															<div id="userLoginDropdownWrapper">
																<svg class="userLoginDropdownArrow" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" xmlns:a="http://ns.adobe.com/AdobeSVGViewerExtensions/3.0/" x="0px" y="0px" width="14px" height="13px" viewBox="0 0 14 13" enable-background="new 0 0 14 13" xml:space="preserve"><defs></defs><polygon fill="#E6E6E6" points="14,13 13.3,13 7,7.1 0.7,13 0,13 7,0 "/><polygon fill="#FFFFFF" points="7,1.3 13.3,13 0.7,13 "/></svg>
																<form action="login.php" method="post" id="userLoginForm">
																	<label for="userLoginUsername">Email or username</label>
																	<input type="text" name="username" class="userLoginField" id="userLoginUsername">
																	<label for="userLoginPassword">Password</label>
																	<input type="password" name="password" class="userLoginField" id="userLoginPassword">
																	<input type="submit" id="hiddenUserLoginButton" hidden>
																	<input type="text" name="origin" hidden value="<?php print getPageName();?>">
																	<div class="blueButton" id="userLoginButton">Log in</div>
																</form>
															</div>
														</div>

														<?php }?>

														<?php }?>

													</div>
												</div>
											</header>
											<div id="createHeaderTweetContainer" class="hide">
												<div id="createHeaderTweetWrapper" class="hide">
													<div class="createHeaderTweetHeader">
														Compose a new Tweet
														<div class="closeHeaderTweet">
															<i class="fa fa-times"></i>
														</div>
													</div>
													<div class="createHeaderTweetContentPart">
														<form method="post" id="headerTweetForm" enctype="multipart/form-data">
															<input type="hidden" name="message" value="newHeaderTweet">
															<textarea id="createHeaderTweetContent" name="tweet" contenteditable></textarea>
															<input name="tweetImage[]" type="file" accept="image/*" hidden id="createHeaderTweetUpload">
															<input type="submit" hidden value="tweet" id="hiddenHeaderTweetButton">
														</form>
														<div class="headerTweetImagePreview">

														</div>
														<div class="createHeaderTweetAddImage">
															<svg class="createHeaderTweetAddImageIcon" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" xmlns:a="http://ns.adobe.com/AdobeSVGViewerExtensions/3.0/" x="0px" y="0px" width="100px" height="80px" viewBox="0 0 100 80" enable-background="new 0 0 100 80" xml:space="preserve"><defs></defs><g><path fill="#5BB7EA" d="M50,30c-8.3,0-15,6.7-15,15c0,8.3,6.7,15,15,15c8.3,0,15-6.7,15-15C65,36.7,58.3,30,50,30z M90,15H78 c-1.7,0-3.4-1.3-3.9-2.8l-3.1-9.3C70.4,1.3,68.7,0,67,0H33c-1.6,0-3.4,1.3-3.9,2.8l-3.1,9.3C25.4,13.7,23.6,15,22,15H10C4.5,15,0,19.5,0,25v45c0,5.5,4.5,10,10,10h80c5.5,0,10-4.5,10-10V25C100,19.5,95.5,15,90,15z M50,70c-13.8,0-25-11.2-25-25c0-13.8,11.2-25,25-25c13.8,0,25,11.2,25,25C75,58.8,63.8,70,50,70z M86.5,32c-1.9,0-3.5-1.6-3.5-3.5c0-1.9,1.6-3.5,3.5-3.5c1.9,0,3.5,1.6,3.5,3.5C90,30.4,88.4,32,86.5,32z"/></g></svg>
															<span>Add image</span>
														</div>
														<div class="moveRight">
															<div class="headerTweetChars">140</div>
															<div class="blueButton disabled" id="createHeaderTweet">Tweet</div>
														</div>
													</div>
												</div>
											</div>

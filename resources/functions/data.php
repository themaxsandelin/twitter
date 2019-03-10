<?php

require_once("database.php");

$loginErrors = [
	"Windows error. Login credentials invalid. Windows shutting down.",
	"Invalid login, come on dude..",
	"Guess what? Invalid login, b*tch.",
	"Error 404. Login credentials not found.",
	"Game over. Try again.",
	"Wrong login...really? How hard can it be?",
	"These aren't the login credentials you are looking for.",
	"Howdie neighbourino. Seems you forgot your login credentielinos.",
	"Beep. Boop. Boop. Beep. Boop. Beep. Wrong."
];

function generateSalt () {
	$uniqueRandomString = md5(uniqid(mt_rand(), true));
	$base64String = base64_encode($uniqueRandomString);
	$modifiedBase64String = str_replace('+', '.', $base64String);
	$salt = substr($modifiedBase64String, 0, 22);
	return $salt;
}

function passwordEncrypt ($password) {
	$hashFormat = "$2y$10$";
	$salt = generateSalt();
	$formatAndSalt = $hashFormat . $salt;
	$hash = crypt($password, $formatAndSalt);
	$hash = md5($hash);
	return array("key" => $formatAndSalt, "password" => $hash);
}

function checkSession () {
	session_start();
	if (isset($_SESSION["login"])) {
		if ($_SESSION["login"] == "yes") {
			return true;
		}
	}
	return false;
}

function createSession ($user) {
	$_SESSION = $user;
	$_SESSION["login"] = "yes";
}

function generateSessionID () {
	$sessionID = "";
	$IDChars = array_merge(range('A','Z'), range('a', 'z'), range(0, 9));
	for ($i = 0; $i <= 22; $i++) {
		$sessionID .= $IDChars[array_rand($IDChars)];
	}
	return $sessionID;
}

function validatePassword ($input, $password, $key) {
	if (md5(crypt($input, $key)) == $password) {
		return true;
	}
	return false;
}

function validateUser ($username, $password) {
	if (filter_var($username, FILTER_VALIDATE_EMAIL)) {
		$user = getDBContent("SELECT * FROM user_login WHERE email = '$username'");
	} else {
		$user = getDBContent("SELECT * FROM user_login WHERE username = '$username'");
	}
	if ($user) {
		$validPass = validatePassword($password, $user[0]["password"], $user[0]["hash_key"]);
		$array = [];
		if ($validPass) {
			$array["sessionID"] = generateSessionID();
			$array["userID"] = $user[0]["user_id"];
			return $array;
		}
		return false;
	}
	return false;
}

function validateUserWithID ($userID, $password) {
	$user = getDBContent("SELECT * FROM user_login WHERE user_id = '$userID'");
	if ($user) {
		$validPass = validatePassword($password, $user[0]["password"], $user[0]["hash_key"]);
		$array = [];
		if ($validPass) {
			return true;
		}
		return false;
	}
	return false;
}

function getUserInfo ($userID) {
	$user_login = getDBContent("SELECT * FROM user_login WHERE user_id = '$userID'");
	$user_info = getDBContent("SELECT * FROM user_info WHERE user_id = '$userID'");
	$array = [];
	$array["id"] = $user_login[0]["user_id"];
	$array["username"] = $user_login[0]["username"];
	$array["email"] = $user_login[0]["email"];
	$array["name"] = $user_info[0]["name"];
	$array["bio"] = $user_info[0]["bio"];
	$array["url"] = $user_info[0]["website_url"];
	$array["followers"] = $user_info[0]["followers"];
	$array["following"] = $user_info[0]["following"];
	$array["tweets"] = $user_info[0]["tweets"];
	$array["admin"] = $user_info[0]["admin"];
	return $array;
}

function redirectLogin () {
	if (isset($_POST["origin"]) === false) {
		header("Location: index.php");
		die();
	} else {
		header("Location: " . $_POST["origin"]);
		die();
	}
}

function generateID () {
	$IDChars = array_merge(range('A','Z'), range('a', 'z'), range(0, 9));
	$id = "";
	$tempID = "";
	for ($i = 0; $i <= 60; $i++) {
		$tempID .= $IDChars[array_rand($IDChars)];
	}
	$id .= $tempID . "_" . date("ymdHis") . "_";
	$tempID = "";
	for ($i = 0; $i <= 60; $i++) {
		$tempID .= $IDChars[array_rand($IDChars)];
	}
	$id .= $tempID;
	return $id;
}

function createRegularTweet ($tweet, $userID) {
	$tweetID = generateID();
	$content = $tweet;
	preg_match_all("/(#\w+)/", $content, $matches);
	$hashtags = $matches[0];
	foreach ($hashtags as $hashtag) {
		$hashtag = str_replace("#", "", $hashtag);
		if ($hashCount = getDBContent("SELECT count FROM hashtags WHERE hashtag = '$hashtag'")) {
			$hashCount = $hashCount[0]["count"];
			$hashCount++;
			$array = [$hashCount, $hashtag];
			updateDBContent("UPDATE hashtags SET count = ? WHERE hashtag = ?", $array);
		} else {
			$array = [$hashtag, 1];
			updateDBContent("INSERT INTO hashtags (hashtag, count) VALUES (?, ?)", $array);
		}
		$hashtagID = getDBContent("SELECT id FROM hashtags WHERE hashtag = '$hashtag'")[0]["id"];
		if ($hashLinkCount = getDBContent("SELECT count FROM hash_link WHERE tweet_id = '$tweetID' AND hashtag_id = '$hashtagID'")) {
			$hashLinkCount = $hashLinkCount[0]["count"];
			$hashLinkCount++;
			$array = [$hashLinkCount, $hashtagID, $tweetID];
			updateDBContent("UPDATE hash_link SET count = ? WHERE hashtag_id = ? AND tweet_id = ?", $array);
		} else {
			$array = [$hashtagID, $tweetID, 1];
			updateDBContent("INSERT INTO hash_link (hashtag_id, tweet_id, count) VALUES (?, ?, ?)", $array);
		}
	}
	$authorID = $userID;
	$tweetCount = getUserInfo($_SESSION["userID"])["tweets"] + 1;
	$array = [$tweetID, $content, $authorID];
	updateDBContent("INSERT INTO tweets (tweet_id, content, author_id) VALUES (?, ?, ?)", $array);
	$array = [$tweetCount, $userID];
	updateDBContent("UPDATE user_info SET tweets = ? WHERE user_id = ?", $array);
	return $tweetID;
}

function getTweetsFromUser ($userID) {
	return getDBContent("SELECT * FROM tweets WHERE author_id = '$userID' AND reply_id IS NULL ORDER BY published DESC");
}

function getUserID ($username) {
	return getDBContent("SELECT user_id FROM user_login WHERE username = '$username'");
}

function checkUsername ($username) {
	if ($user = getDBContent("SELECT * FROM user_login WHERE username = '$username'")) {
		return true;
	}
	return false;
}

function getPageName () {
	return substr($_SERVER["REQUEST_URI"], 9, strlen($_SERVER["REQUEST_URI"]));
}

function saveAccountSettings ($userID, $name, $username, $email, $bio, $url) {
	$array = [$username, $email, $userID];
	updateDBContent("UPDATE user_login SET username = ?, email = ? WHERE user_id = ?", $array);
	$array = [$name, $bio, $url, $userID];
	updateDBContent("UPDATE user_info SET name = ?, bio = ?, website_url = ? WHERE user_id = ?", $array);
}

function checkFollowing ($userID, $followID) {
	$follow = getDBContent("SELECT * FROM following WHERE user_id = '$userID' AND follow_id = '$followID'");
	if ($follow) {
		return true;
	}
	return false;
}

function getUserImage ($id) {
	$images = scandir("./resources/img/users/" . $id . "/", 0);
	$images = array_diff($images, array('.', '..', '.DS_Store'));
	$imageStart = "p";
	foreach ($images as $image) {
		$imageName = explode(".", $image)[0];
		if (strpos($imageName, $imageStart) !== false) {
			return "users/$id/$image";
		}
	}
	return "content/avatar.jpg";
}

function getUserCover ($id) {
	$images = scandir("./resources/img/users/" . $id . "/", 0);
	$images = array_diff($images, array('.', '..', '.DS_Store'));
	$imageStart = "c";
	foreach ($images as $image) {
		$imageName = explode(".", $image)[0];
		if (strpos($imageName, $imageStart) !== false) {
			return "users/$id/$image";
		}
	}
	return "content/cover.jpg";
}

function removeCoverImage ($id) {
	if ($image = getUserCover($id) !== "content/cover.jpg") {
		unlink("resources/img/users/".$image);
	}
}

function removeProfileImage ($id) {
	if ($image = getUserImage($id) !== "content/avatar.jpg") {
		unlink("resources/img/users/".$image);
	}
}

function saveNewPassword ($id, $password) {
	$passwordInfo = passwordEncrypt($password);
	$password = $passwordInfo["password"];
	$key = $passwordInfo["key"];
	$array = [$password, $key, $id];
	updateDBContent("UPDATE user_login SET password = ?, hash_key = ? WHERE user_id = ?", $array);
}

function printUserLink ($link) {
	if (strpos($link, "http://") !== false) {
		$link = str_replace("http://", "", $link);
	} else if (strpos($link, "https://") !== false) {
		$link = str_replace("https://", "", $link);
	}
	if (strpos($link, "www.") !== false) {
		$link = str_replace("www.", "", $link);
	}
	$link = explode("/", $link)[0];
	return $link;
}

function getTweetImage ($u_id, $t_id, $dir = "./resources/img/users/") {
	$images = scandir($dir . $u_id . "/tweets/", 0);
	$images = array_diff($images, array('.', '..', '.DS_Store'));
	$imageStart = $t_id;
	foreach ($images as $image) {
		$imageName = explode(".", $image)[0];
		if (strpos($imageName, $imageStart) !== false) {
			return "$u_id/tweets/$image";
		}
	}
	return false;
}

function getAuthorID ($username) {
	$id = getDBContent("SELECT user_id FROM user_login WHERE username = '$username'")[0]["user_id"];
	if ($id) {
		return $id;
	}
	return false;
}

function getTweetReplyNum ($id) {
	$replyCount = getDBContent("SELECT replies FROM tweets WHERE tweet_id = '$id'")[0]["replies"];
	return (int) "$replyCount";
}

function getTweetFavouriteNum ($id) {
	$favouriteCount = getDBContent("SELECT favourites FROM tweets WHERE tweet_id = '$id'");
	if ($favouriteCount) {
		return $favouriteCount[0]["favourites"];
	}
}

function checkReplyID ($id) {
	$tweet = getDBContent("SELECT * FROM tweets WHERE tweet_id = '$id'");
	if ($tweet) {
		return true;
	}
	return false;
}

function createReply ($replyID, $author, $message) {
	if (checkReplyID($replyID)) {
		$tweetID = generateID();
		if ($authorID = getAuthorID($author)) {
			if (strlen($message) <= 140 && strlen($message) > 0) {
				$published = date("Y-m-d H:i:s");
				$array = [$tweetID, $message, $authorID, $replyID, $published];
				updateDBContent("INSERT INTO tweets (tweet_id, content, author_id, reply_id, published) VALUES (?, ?, ?, ?, ?)", $array);
				$replyCount = getTweetReplyNum($replyID) + 1;
				$array = [$replyCount, $replyID];
				updateDBContent("UPDATE tweets SET replies = ? WHERE tweet_id = ?", $array);
				return true;
			}
		}
	}
	return false;
}

function getTweetReplies ($tweetID) {
	return getDBContent("SELECT * FROM tweets WHERE reply_id = '$tweetID' ORDER BY published DESC LIMIT 5");
}

function calcElapsedTweet ($publishedTime) {
	$datetime1 = new DateTime(date("Y-m-d H:i:s"));
	$datetime2 = new DateTime($publishedTime);
	$interval = $datetime1->diff($datetime2);

	$years = $interval->format("%y");
	$months = $interval->format("%m");
	$days = $interval->format("%d");
	$hours = $interval->format("%h");
	$minutes = $interval->format("%i");
	$seconds = $interval->format("%s");

	$year = substr($publishedTime,0,4);
	$date = substr($publishedTime,8,2);
	$monthNum = substr($publishedTime,5,2);

	$dateObj = DateTime::createFromFormat('!m', $monthNum);
	$monthName = $dateObj->format('M');

	if ($years > 0) {
		$elapsed = "$date $monthName $year";
	} else {
		if ($months > 0) {
			$elapsed = "$date $monthName";
		} else {
			if ($days > 0) {
				$elapsed = "$date $monthName";
			} else {
				if ($hours > 0) {
					$elapsed = $hours . "h";
				} else {
					if ($minutes > 0) {
						$elapsed = $minutes . "m";
					} else {
						$elapsed = $seconds . "s";
					}
				}
			}
		}
	}

	return $elapsed;
}

function getFullTimeElapsed ($publishedTime) {
	$datetime2 = new DateTime($publishedTime);
	$year = substr($publishedTime,0,4);
	$date = substr($publishedTime,8,2);
	$monthNum = substr($publishedTime,5,2);
	$dateObj = DateTime::createFromFormat('!m', $monthNum);
	$monthName = $dateObj->format('M');
	$time = substr($publishedTime,11,8);

	return (date("g:i A", strtotime($time))) . " - $date $monthName $year";
}

function getFormatedReplyCount ($id) {
	$num = getTweetReplyNum($id);
	if ($num > 999) {
		$x = round($num);
		$x_number_format = number_format($x);
		$x_array = explode(',', $x_number_format);
		$x_parts = array('K', 'M', 'B', 'T');
		$x_count_parts = count($x_array) - 1;
		$x_display = $x;
		$x_display = $x_array[0] . ((int) $x_array[1][0] !== 0 ? '.' . $x_array[1][0] : '');
		$x_display .= $x_parts[$x_count_parts - 1];
		print $x_display;
	} else {
		if ($num > 0) {
			print $num;
		}
	}
}

function getFormatedFavouriteCount ($id) {
	$num = getTweetFavouriteNum($id);
	if ($num > 999) {
		$x = round($num);
		$x_number_format = number_format($x);
		$x_array = explode(',', $x_number_format);
		$x_parts = array('K', 'M', 'B', 'T');
		$x_count_parts = count($x_array) - 1;
		$x_display = $x;
		$x_display = $x_array[0] . ((int) $x_array[1][0] !== 0 ? '.' . $x_array[1][0] : '');
		$x_display .= $x_parts[$x_count_parts - 1];
		print $x_display;
	} else {
		if ($num > 0) {
			print $num;
		}
	}
}

function getHomeTweets ($id) {
	$following = getDBContent("SELECT * FROM following WHERE user_id = '$id'");
	$tweetList = [];
	if ($following) {
		foreach ($following as $follow) {
			$followID = $follow["follow_id"];
			$tweets = getDBContent("SELECT * FROM tweets WHERE author_id = '$followID' AND reply_id IS NULL ORDER BY published DESC");
			foreach ($tweets as $tweet) {
				$tweetList[] = $tweet;
			}
		}
  }
  $userTweets = getDBContent("SELECT * FROM tweets WHERE author_id = '$id' AND reply_id IS NULL ORDER BY published DESC");
	if ($userTweets) {
		foreach ($userTweets as $tweet) {
			$tweetList[] = $tweet;
		}
	}
	arsort($tweetList);
	return $tweetList;
}

function getUserInfoLimited ($id) {
	$user_login = getDBContent("SELECT * FROM user_login WHERE user_id = '$id'");
	$user_info = getDBContent("SELECT * FROM user_info WHERE user_id = '$id'");
	$array = [];
	$array["id"] = $user_login[0]["user_id"];
	$array["username"] = $user_login[0]["username"];
	$array["name"] = $user_info[0]["name"];
	return $array;
}

function getAdmin ($id) {
	$admin = getDBContent("SELECT admin FROM user_info WHERE user_id = '$id'");
	if ($admin) {
		return $admin[0]["admin"];
	}
}

function createFollow ($follower, $followed) {
	$userID = getAuthorID($follower);
	$followID = getAuthorID($followed);
	$array = [$userID, $followID];
	if (updateDBContent("INSERT INTO following (user_id, follow_id) VALUES (?, ?)", $array)) {
		$followingCount = getDBContent("SELECT following FROM user_info WHERE user_id = '$userID'")[0]["following"];
		$followingCount++;
		$array = [$followingCount, $userID];
		updateDBContent("UPDATE user_info SET following = ? WHERE user_id = ?", $array);
		$followersCount = getDBContent("SELECT followers FROM user_info WHERE user_id = '$followID'")[0]["followers"];
		$followersCount++;
		$array = [$followersCount, $followID];
		updateDBContent("UPDATE user_info SET followers = ? WHERE user_id = ?", $array);
		return true;
	}
	return false;
}

function deleteFollow ($follower, $followed) {
	$userID = getAuthorID($follower);
	$followID = getAuthorID($followed);
	$array = [$userID, $followID];
	if (updateDBContent("DELETE FROM following WHERE user_id = ? AND follow_id = ?", $array)) {
		$followingCount = getDBContent("SELECT following FROM user_info WHERE user_id = '$userID'")[0]["following"];
		$followingCount--;
		$array = [$followingCount, $userID];
		updateDBContent("UPDATE user_info SET following = ? WHERE user_id = ?", $array);
		$followersCount = getDBContent("SELECT followers FROM user_info WHERE user_id = '$followID'")[0]["followers"];
		$followersCount--;
		$array = [$followersCount, $followID];
		updateDBContent("UPDATE user_info SET followers = ? WHERE user_id = ?", $array);
		return true;
	}
	return false;
}

function formatTweetContent ($content) {
	$content = preg_replace('/(@(\w+))/', '<a href="/\2">\1</a>', $content);
	$string = preg_replace('/(#(\w+))/', '<a href="/search/?s=%23\2">\1</a>', $content);
	return $string;
}

function removeTweet ($tweet, $author) {
	$tweetCount = getDBContent("SELECT tweets FROM user_info WHERE user_id = '$author'")[0]["tweets"];
	$tweetCount--;
	if ($image = getTweetImage($author, $tweet, "../img/users/")) {
		unlink("../img/users/".$image);
	}
	$array = [$tweetCount, $author];
	updateDBContent("UPDATE user_info SET tweets = ? WHERE user_id = ?", $array);
	$array = [$tweet];
	updateDBContent("DELETE FROM tweets WHERE tweet_id = ?", $array);
	updateDBContent("DELETE FROM tweets WHERE reply_id = ?", $array);
	updateDBContent("DELETE FROM favourites WHERE tweet_id = ?", $array);
	if ($hashLink = getDBContent("SELECT * FROM hash_link WHERE tweet_id = '$tweet'")) {
		foreach ($hashLink as $link) {
			$hashtagID = $link["hashtag_id"];
			$hashCount = getDBContent("SELECT count FROM hashtags WHERE id = '$hashtagID+'")[0]["count"];
			$hashCount--;
			$array = [$hashCount, $hashtagID];
			updateDBContent("UPDATE hashtags SET count = ? WHERE id = ?", $array);
		}
		$array = [$tweet];
		updateDBContent("DELETE FROM hash_link WHERE tweet_id = ?", $array);
	}
}

function checkFavourited ($tweetID, $userID) {
	$favourited = getDBContent("SELECT * FROM favourites WHERE user_id = '$userID' AND tweet_id = '$tweetID'");
	if ($favourited) {
		return true;
	}
	return false;
}

function addFavourite ($tweetID, $userID) {
	$favouriteCount = getDBContent("SELECT favourites FROM tweets WHERE tweet_id = '$tweetID'")[0]["favourites"];
	$favouriteCount++;
	$array = [$favouriteCount, $tweetID];
	updateDBContent("UPDATE tweets SET favourites = ? WHERE tweet_id = ?", $array);
	$array = [$userID, $tweetID];
	updateDBContent("INSERT INTO favourites (user_id, tweet_id) VALUES (?, ?)", $array);
}

function removeFavourite ($tweetID, $userID) {
	$favouriteCount = getDBContent("SELECT favourites FROM tweets WHERE tweet_id = '$tweetID'")[0]["favourites"];
	$favouriteCount--;
	$array = [$favouriteCount, $tweetID];
	updateDBContent("UPDATE tweets SET favourites = ? WHERE tweet_id = ?", $array);
	$array = [$userID, $tweetID];
	updateDBContent("DELETE FROM favourites WHERE user_id = ? AND tweet_id = ?", $array);
}

function getFavouriteUsers ($id) {
	$users = getDBContent("SELECT * FROM favourites WHERE tweet_id = '$id' ORDER BY id DESC LIMIT 10");
	if ($users) {
		return $users;
	}
	return false;
}

function removeReply ($replyID, $tweetID) {
	$array = [$replyID, $tweetID];
	updateDBContent("DELETE FROM tweets WHERE tweet_id = ? AND reply_id = ?", $array);
	$replyCount = getDBContent("SELECT replies FROM tweets WHERE tweet_id = '$tweetID'")[0]["replies"];
	$replyCount--;
	$array = [$replyCount, $tweetID];
	updateDBContent("UPDATE tweets SET replies = ? WHERE tweet_id = ?", $array);
}

function searchSpecificUser ($username) {
	$userInfo = [];
	$user = getDBContent("SELECT user_id, username FROM user_login WHERE username = '$username'")[0];
	if ($user) {
		$userID = $user["user_id"];
		$userName = getDBContent("SELECT name FROM user_info WHERE user_id = '$userID'")[0];
		$user["name"] = $userName["name"];
		$userInfo[] = $user;
		return $userInfo;
	}
	return false;
}

function searchUser ($username) {
	$userInfo = [];
	$users = getDBContent("SELECT user_id, username FROM user_login WHERE username LIKE '%{$username}%'");
	if ($users) {
		foreach ($users as $user) {
			$userID = $user["user_id"];
			$userName = getDBContent("SELECT name FROM user_info WHERE user_id = '$userID'")[0];
			$user["name"] = $userName["name"];
			$userInfo[] = $user;
		}
		return $userInfo;
	}
	return false;
}

function searchTweets ($content) {
	return getDBContent("SELECT * FROM tweets WHERE reply_id IS NULL AND content LIKE '%{$content}%' ORDER BY published DESC");
}

function getTweet ($tweetID) {
	$tweet = getDBContent("SELECT * FROM tweets WHERE id = '$tweetID'");
	if ($tweet) {
		return $tweet[0];
	}
	return false;
}

function getAllTweetReplies ($tweetID) {
	return getDBContent("SELECT * FROM tweets WHERE reply_id = '$tweetID' ORDER BY published DESC");
}

function validateUsername ($username) {
	if ($username === "") {
		return "empty";
	}
	$getUsernames = getDBContent("SELECT username FROM user_login");
	$usernames = [];
	foreach ($getUsernames as $getUsername) {
		$usernames[] = $getUsername["username"];
	}
	if (in_array($username, $usernames)) {
		return "exists";
	}
	return true;
}

function validateEmail ($email) {
	if ($email === "") {
		return "empty";
	}
	$getEmails = getDBContent("SELECT email FROM user_login");
	$emails = [];
	foreach ($getEmails as $getEmail) {
		$emails[] = $getEmail["email"];
	}
	if (in_array($email, $emails)) {
		return "exists";
	}
	return true;
}

function validateRegPassword ($password) {
	if ($password === "") {
		return "empty";
	}
	return true;
}

function validateName ($name) {
	if ($name === "") {
		return "empty";
	}
	return true;
}

function registerUser ($name, $username, $email, $password) {
	$userID = generateID();
	$passwordInfo = passwordEncrypt($password);
	$password = $passwordInfo["password"];
	$key = $passwordInfo["key"];

	if (!file_exists("./resources/img/users")) {
		mkdir("./resources/img/users");
	}

	$path = "./resources/img/users/".$userID."/";
	mkdir($path);

	$path = "./resources/img/users/".$userID."/tweets/";
	mkdir($path);

	updateDBContent("INSERT INTO user_login (user_id, username, email, password, hash_key) VALUES (?, ?, ?, ?, ?)", [ $userID, $username, $email, $password, $key ]);
	updateDBContent("INSERT INTO user_info (user_id, name) VALUES (?, ?)", [ $userID, $name ]);
}

function searchUsersName ($search) {
	$userInfo = [];
	$users = getDBContent("SELECT user_id, name FROM user_info WHERE name = '$search'");
	if ($users) {
		foreach ($users as $user) {
			$userID = $user["user_id"];
			$user["username"] = getDBContent("SELECT username FROM user_login WHERE user_id = '$userID'")[0]["username"];
			$userInfo[] = $user;
		}
		return $userInfo;
	}
	return false;
}

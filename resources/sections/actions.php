<?php
	if ($_SERVER["REQUEST_METHOD"] === "POST") {
		require("../functions/data.php");
		checkSession();
		$return = [];
		if ($_POST["message"] === "newReply") {
			$message = $_POST["reply"];
			$replyID = $_POST["twid"];
			$author = $_POST["user"];
			$reply = createReply($replyID, $author, $message);
			$return = [];
			if ($reply) {
				$return["reply"] = true;
			} else {
				$return["reply"] = false;
			}
			print json_encode($return);
		} else if ($_POST["message"] === "newHomeTweet") {
			if ($_FILES["tweetImage"]["error"] == "4") {
				createRegularTweet($_POST["tweet"], $_SESSION["userID"]);
			} else {
				$id = createRegularTweet($_POST["tweet"], $_SESSION["userID"]);
				$uploads_dir = "../img/users/".$_SESSION["userID"]."/tweets/";
				$imageNumber = 1;
				foreach ($_FILES["tweetImage"]["error"] as $key => $image_error) {
					if ($image_error == UPLOAD_ERR_OK) {
						$tmp_name = $_FILES["tweetImage"]["tmp_name"][$key];
						$end = explode(".", $_FILES["tweetImage"]["name"][$key])[1];
						$name = $id;
						$fullName = $name.".".$end;
						if (move_uploaded_file($tmp_name, $uploads_dir.$fullName)) {
							$imageNumber++;
						}
					}
				}
			}
		} else if ($_POST["message"] === "newHeaderTweet") {
			if ($_FILES["tweetImage"]["error"] == "4") {
				createRegularTweet($_POST["tweet"], $_SESSION["userID"]);
			} else {
				$id = createRegularTweet($_POST["tweet"], $_SESSION["userID"]);
				$uploads_dir = "../img/users/".$_SESSION["userID"]."/tweets/";
				$imageNumber = 1;
				foreach ($_FILES["tweetImage"]["error"] as $key => $image_error) {
					if ($image_error == UPLOAD_ERR_OK) {
						$tmp_name = $_FILES["tweetImage"]["tmp_name"][$key];
						$end = explode(".", $_FILES["tweetImage"]["name"][$key])[1];
						$name = $id;
						$fullName = $name.".".$end;
						if (move_uploaded_file($tmp_name, $uploads_dir.$fullName)) {
							$imageNumber++;
						}
					}
				}
			}
		} else if ($_POST["message"] === "getTweetCount") {
			$userID = getAuthorID($_POST["username"]);
			$tweetCount = getDBContent("SELECT tweets FROM user_info WHERE user_id = '$userID'")[0]["tweets"];
			print $tweetCount;
		} else if ($_POST["message"] === "getUserInfo") {
			$author = $_POST["author"];
			$user = getUserInfoLimited($author);
			$user = json_encode($user);
			print_r($user);
		} else if ($_POST["message"] === "userFollow") {
			$follower = $_POST["follower"];
			$followed = $_POST["followed"];
			$follow = createFollow($follower, $followed);
			if ($follow) {
				$return["message"] = "success";
			} else {
				$return["message"] = "fail";
			}
			$return = json_encode($return);
			print_r($return);
		} else if ($_POST["message"] === "userUnfollow") {
			$follower = $_POST["follower"];
			$followed = $_POST["followed"];
			$delete = deleteFollow($follower, $followed);
			if ($delete) {
				$return["message"] = "success";
			} else {
				$return["message"] = "fail";
			}
			$return = json_encode($return);
			print_r($return);
		} else if ($_POST["message"] === "removeTweet") {
			$tweetID = $_POST["code"];
			$authorID = $_POST["id"];
			removeTweet($tweetID, $authorID);
		} else if ($_POST["message"] === "addFavourite") {
			$tweetID = $_POST["tweetID"];
			$userID = $_POST["userID"];
			addFavourite($tweetID, $userID);
		} else if ($_POST["message"] === "removeFavourite") {
			$tweetID = $_POST["tweetID"];
			$userID = $_POST["userID"];
			removeFavourite($tweetID, $userID);
		} else if ($_POST["message"] === "removeReply") {
			$replyID = $_POST["replyID"];
			$tweetID = $_POST["tweetID"];
			removeReply($replyID, $tweetID);
		}
	} else {
		header("Location: /");
		die();
	}
?>

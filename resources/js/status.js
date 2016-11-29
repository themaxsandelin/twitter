$(window).load(function(){

	var tweetID = $(".statusWrapper").attr("data-tweet-id");
	var authorID = $(".statusWrapper").attr("data-author-id");
	var username = $("#heusna").val();
	var userName = $("#heusn").val();
	var userID = $("#heusi").val();
	var userProfileImage = $("#heusp").val();
	var creatingReply = false;

	function replyRemoval (replyID, tweetID, reply) {
		var message = "removeReply";
		$.post('/resources/sections/actions.php', {
			message: message,
			replyID: replyID,
			tweetID: tweetID
		}).success(function(data){
			document.location.reload(true);
		});
	}

	function sendReply (reply, user, twid) {
		var message = "newReply";
		$.post('/resources/sections/actions.php', {
			message: message,
			twid: twid,
			reply: reply,
			user: user
		}).success(function(data){
			decoded = $.parseJSON(data);
			if (decoded.reply) {
				creatingReply = false;
				$(".clickToReply").show();
				$(".createReplyWrapper").hide();
				reply = reply.replace(/\B@([\w-]+)/gm, '<a href="/$1">@$1</a>');
				reply = reply.replace(/\B#([\w-]+)/gm, '<a href="/search/?s=$1">#$1</a>');
				$(".tweetReplyWrapper").prepend(
					'<div class="statusTweetReply tweetReply">'+
					'<a href="/'+username+'">'+
					'<div class="statusReplyImage replyUserImage" style="background:url(/resources/img/'+userProfileImage+') no-repeat center center; background-size:cover;"></div>'+
					'</a>'+
					'<div class="replyContentWrapper">'+
					'<a href="/'+username+'" class="tweetAuthorHover">'+
					'<div class="tweetAuthor">'+userName+'</div>'+
					'<div class="authorUsername">&nbsp;@'+username+'</div>'+
					'</a>'+
					'<a href="/'+username+'">'+
					'<div class="tweetDate"> 0 Sec</div>'+
					'</a>'+
					'<div class="tweetText">'+reply+'</div>'+
					'</div>'+
					'</div>'
				);
			}
		});
	}

	function addFavourite (tweetID, userID) {
		var message = "addFavourite";
		$.post('/resources/sections/actions.php', {
			message: message,
			tweetID: tweetID,
			userID: userID
		}).success(function(data){
			document.location.reload(true);
		});
	}

	function removeFavourite (tweetID, userID) {
		var message = "removeFavourite";
		$.post('/resources/sections/actions.php', {
			message: message,
			tweetID: tweetID,
			userID: userID
		}).success(function(data){
			document.location.reload(true);
		});
	}

	function tweetRemoval (code, id) {
		var message = "removeTweet";
		$.post('/resources/sections/actions.php', {
			message: message,
			code: code,
			id: id
		}).success(function(data){
			window.location.href="/";
		});
	}

	$(document).on("tap", function(event){
		if (creatingReply === true) {
			if ($(".replyContent").val() === $(".replyContent").attr("data-original-content") || $(".replyContent").val() === $(".replyContent").attr("data-original-content") + " " || $(".replyContent").val() === "") {
				creatingReply = false;
				$(".clickToReply").show();
				$(".createReplyWrapper").hide();
			}
		}
	});

	$("body").on("tap", ".preventTweetAction", function(e){
		e.stopPropagation();
	});

	$("body").on("tap", ".removeReply", function(){
		var remove = confirm("Are you sure that you want to delete this reply?");
		if (remove) {
			replyRemoval($(this).parents(".tweetReply").attr("data-reply-id"), tweetID, $(this).parents(".tweetReply"));
		}
	});

	$("body").on("tap", ".clickToReply", function(){
		$(".createReplyWrapper").show();
		$(".clickToReply").hide();
		$(".replyContent").focus().val($(".replyContent").attr("data-original-content")+" ");
		creatingReply = true;
	});

	$("body").on("input", ".replyContent", function(){
		var replyChars = $(".replyChars");
		var replyButton = $(".replyTweet");
		var charsLeft = 140 - parseInt($(this).val().length);
		if ($(this).val() === $(this).attr("data-original-content") || $(this).val() === $(this).attr("data-original-content")+" " || $(this).val() === "") {
			if ($(replyButton).hasClass("disabled") === false) {
				$(replyButton).addClass("disabled");
			}
		} else {
			if ($(replyButton).hasClass("disabled")) {
				$(replyButton).removeClass("disabled");
			}
		}
		if (charsLeft <= 0) {
			if (!$(replyButton).hasClass("disabled")) {
				$(replyButton).addClass("disabled");
			}
		}
		if (charsLeft < 140 && charsLeft > 0) {
			if ($(replyButton).hasClass("disabled")) {
				$(replyButton).removeClass("disabled");
			}
		}
		if (charsLeft == 140) {
			if (!$(replyButton).hasClass("disabled")) {
				$(replyButton).addClass("disabled");
			}
		}
		if (charsLeft < 11) {
			if ($(replyChars).hasClass("dark")) {
				$(replyChars).removeClass("dark");
			}
			$(replyChars).addClass("red");
		} else if (charsLeft >= 11 && charsLeft < 21) {
			if ($(replyChars).hasClass("red")) {
				$(replyChars).removeClass("red");
			}
			$(replyChars).addClass("dark");
		} else if (charsLeft >= 21) {
			if ($(replyChars).hasClass("dark")) {
				$(replyChars).removeClass("dark");
			}
			if ($(replyChars).hasClass("red")) {
				$(replyChars).removeClass("red");
			}
		}

		$(replyChars).html(charsLeft);
	});

	$("body").on("tap", ".replyTweet", function(){
		if ($(this).hasClass("disabled") === false) {
			if ($(".replyContent").val() !== "" && $(".replyContent").val().length <= 140) {
				var twid = $(".replyContent").parent().find(".twid").val();
				sendReply($(".replyContent").val(), username, twid);
			}
		}
	});

	$("body").on("tap", ".favouriteClick", function(){
		var fav = $(this).parent();
		if ($(fav).hasClass("favourited")) {
			removeFavourite(tweetID, userID);
			$(".favouriteTool").removeClass("favourited");
		} else {
			$(".favouriteTool").addClass("favourited animateFavourite");
			addFavourite(tweetID, userID);
			setTimeout(function(){
				$(".favouriteTool").removeClass("favourited");
			}, 400);
		}
	});

	$("body").on("tap", ".removeTweet", function(){
		var remove = confirm("Are you sure you want to delete this post?");
		if (remove) {
			tweetRemoval(tweetID, authorID);
		}
	});

});

$(window).load(function(){

	userDropdownMenu = "hidden";
	userDropdownAnimation = "done";
	creatingNewTweet = false;
	creatingReply = false;
	username = $("#heusna").val();
	userName = $("#heusn").val();
	userID = $("#heusi").val();
	userProfileImage = $("#heusp").val();

	function showUserDropdown () {
		$(".userDropdown").show();
		userDropdownAnimation = "running";
		setTimeout(function(){
			$(".userDropdown").removeClass("hide");
			setTimeout(function(){
				// The dropdown animation is done. Do this!
				userDropdownMenu = "showing";
				userDropdownAnimation = "done";
			}, 310);
		}, 10);
	}

	function hideUserDropdown () {
		$(".userDropdown").addClass("hide");
		userDropdownAnimation = "running";
		setTimeout(function(){
			$(".userDropdown").hide();
			userDropdownMenu = "hidden";
			userDropdownAnimation = "done";
		}, 310);
	}

	function userDropdown () {
		if (userDropdownMenu == "hidden" && userDropdownAnimation == "done") {
			showUserDropdown();
		} else if (userDropdownMenu == "showing" && userDropdownAnimation == "done") {
			hideUserDropdown();
		}
	}

	function countTweetChars (input) {
		charsLeft = 140 - input.length;
		$(".tweetChars").html(charsLeft);
		if (charsLeft <= 0) {
			if (!$("#createHomeTweet").hasClass("disabled")) {
				$("#createHomeTweet").addClass("disabled");
			}
		}
		if (charsLeft < 140 && charsLeft > 0) {
			if ($("#createHomeTweet").hasClass("disabled")) {
				$("#createHomeTweet").removeClass("disabled");
			}
		}
		if (charsLeft == 140) {
			if (!$("#createHomeTweet").hasClass("disabled")) {
				$("#createHomeTweet").addClass("disabled");
			}
		}
		if (charsLeft < 11) {
			if ($(".tweetChars").hasClass("dark")) {
				$(".tweetChars").removeClass("dark");
			}
			$(".tweetChars").addClass("red");
		} else if (charsLeft >= 11 && charsLeft < 21) {
			if ($(".tweetChars").hasClass("red")) {
				$(".tweetChars").removeClass("red");
			}
			$(".tweetChars").addClass("dark");
		} else if (charsLeft >= 21) {
			if ($(".tweetChars").hasClass("dark")) {
				$(".tweetChars").removeClass("dark");
			}
			if ($(".tweetChars").hasClass("red")) {
				$(".tweetChars").removeClass("red");
			}
		}
	}

	function previewTweetImage (allFiles){
		var fileList = allFiles.files;
		var anyWindow = window.URL || window.webkitURL;
		for(var i = 0; i < fileList.length; i++){
			var objectUrl = anyWindow.createObjectURL(fileList[i]);
			$('.tweetImagePreview').empty().append('<img src="'+objectUrl+'" alt="tweet preview image" class="previewTweetImage">');
			window.URL.revokeObjectURL(fileList[i]);
		}
	}

	function sendReply (reply, user, twid) {
		var message = "newReply";
		$.post('resources/sections/actions.php', {
			message: message,
			twid: twid,
			reply: reply,
			user: user
		}).success(function(data){
			decoded = $.parseJSON(data);
			if (decoded.reply) {
				var activeTweet = $("#tweets").attr("data-active-tweet");
				creatingReply = false;
				$("#"+activeTweet).find(".clickToReply").show();
				$("#"+activeTweet).find(".createReplyWrapper").hide();
				var count = $("#"+activeTweet).find(".replyCount").html();
				count++;
				$("#"+activeTweet).find(".replyCount").html(count);
				reply = reply.replace(/\B@([\w-]+)/gm, '<a href="/$1">@$1</a>');
				reply = reply.replace(/\B#([\w-]+)/gm, '<a href="/search/?s=$1">#$1</a>');
				$("#"+activeTweet).find(".tweetReplyWrapper").prepend(
					'<div class="tweetReply">'+
					'<a href="/'+username+'">'+
					'<div class="replyUserImage" style="background:url(resources/img/'+userProfileImage+') no-repeat center center; background-size:cover;"></div>'+
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

	function tweetRemoval (code, id) {
		var message = "removeTweet";
		$.post('resources/sections/actions.php', {
			message: message,
			code: code,
			id: id
		}).success(function(data){
			document.location.reload(true);
		});
	}

	function addFavourite (tweetID, userID) {
		var message = "addFavourite";
		$.post('resources/sections/actions.php', {
			message: message,
			tweetID: tweetID,
			userID: userID
		}).success(function(data){

		});
	}

	function removeFavourite (tweetID, userID) {
		var message = "removeFavourite";
		$.post('resources/sections/actions.php', {
			message: message,
			tweetID: tweetID,
			userID: userID
		}).success(function(data){

		});
	}

	function replyRemoval (replyID, tweetID, reply) {
		var message = "removeReply";
		$.post('resources/sections/actions.php', {
			message: message,
			replyID: replyID,
			tweetID: tweetID
		}).success(function(data){
			var count = $(reply).parents(".tweetWrapper").find(".replyCount").html();
			count--;
			$(reply).parents(".tweetWrapper").find(".replyCount").html(count);
			$(reply).remove();
		});
	}

	$("body").on("input", ".createTweetTextarea", function(){
		var input = $(this).val();
		countTweetChars(input);
	});

	$("body").on("tap", ".userHeadImageWrapper", function(){
		userDropdown();
	});

	$("body").on("tap", ".userHead", function(e){
		e.stopImmediatePropagation();
	});

	$("body").on("tap", ".createTweetWrapper", function(e){
		e.stopImmediatePropagation();
	});

	$("body").on("tap", ".clickTweet", function(){
		$(this).hide();
		$(".createTweetContent").show();
		$(".createTweetTextarea").focus();
		creatingNewTweet = true;
	});

	$("body").on("tap", ".createTweetAddImage", function(){
		$("#createTweetUpload").click();
	});

	$(document).on("tap", function(event){
		if (userDropdownMenu == "showing" && userDropdownAnimation == "done") {
			hideUserDropdown();
		}
		if (creatingNewTweet === true && $(".createTweetTextarea").val() == "") {
			$(".createTweetContent").hide();
			$(".clickTweet").show();
		}
		if (creatingReply === true) {
			var activeTweet = $("#tweets").attr("data-active-tweet");
			var replyID = activeTweet.replace("post", "replyTo");
			if ($("#"+replyID).val() === $("#"+replyID).attr("data-original-content") || $("#"+replyID).val() === $("#"+replyID).attr("data-original-content") + " " || $("#"+replyID).val() === "") {
				creatingReply = false;
				$("#"+activeTweet).find(".clickToReply").show();
				$("#"+activeTweet).find(".createReplyWrapper").hide();
			}
		}
	});

	$("body").on("tap", "#createHomeTweet", function(){
		var tweet = $(".createTweetTextarea").val();
		if (tweet !== "" && tweet.length <= 140) {
			$("#hiddenTweetButton").click();
		}
	});

	$("#createTweetUpload").on("change", function(){
		previewTweetImage(this);
		$(".createTweetAddImage span").html("Change image");
	});

	$("#homeTweetForm").submit(function(e){
		$.ajax({
			url: 'resources/sections/actions.php',
			type: 'POST',
			data: new FormData(this),
			processData: false,
			contentType: false,
			success: function (data) {
				document.location.reload(true);
			}
		});
		e.preventDefault();
	});

	$("body").on("tap", ".tweetWrapper", function(){
		var active = $(this).attr("data-activated");
		var origHeight = $(".tweetContentContainer", this).attr("data-original-height");
		var idNum = parseInt($(this).attr("id").substr(4, $(this).attr("id").length));
		var topSibl = idNum - 1;
		var bottomSibl = idNum + 1;
		var topSiblActive = $("#post"+topSibl).attr("data-activated");
		var bottomSiblActive = $("#post"+bottomSibl).attr("data-activated");
		var oldHeight = 0;
		var newHeight = 0;
		var extra = 1;
		var tweet = $(this);
		var activeView = $(".activeView", this);
		var activeBottomView = $(".activeBottomView", this);
		var regularView = $(".regularView", this);

		var currentHeight = $(tweet).find(".tweetContentContainer").height() + extra;
		if (origHeight === "0") {
			$(".tweetContentContainer", tweet).attr("data-original-height", currentHeight);
		}

		oldHeight = $(tweet).find(".tweetContentContainer").height() + extra;

		if (active === "false") {
			$(tweet).css("height", oldHeight);
			setTimeout(function(){
				$(tweet).addClass("animate");
				$(tweet).attr("data-activated", "true").addClass("activeTop activeBottom active");
				$(regularView).hide();
				$(activeView).show();
				$(activeBottomView).show();
				newHeight = $(tweet).find(".tweetContentContainer").height() + extra;
				$(tweet).css("height", newHeight);
				if (topSiblActive === "false") {
					$("#post"+topSibl).addClass("activeBottom");
				}
				if (bottomSiblActive === "false") {
					$("#post"+bottomSibl).addClass("activeTop");
				}
			}, 10);
			setTimeout(function(){
				$(tweet).removeClass("animate").css("height", "auto");
				setTimeout(function(){
				}, 10);
			}, 200);
		} else if (active === "true") {
			$(tweet).attr("data-activated", "false").removeClass("active");
			$(tweet).css("height", $(".tweetContentContainer", tweet).height());
			setTimeout(function(){
				$(tweet).addClass("animate");
				setTimeout(function(){
					$(tweet).css("height", origHeight);
				}, 10);
			}, 10);
			if (topSiblActive === "false") {
				$(tweet).removeClass("activeTop");
				$("#post"+topSibl).removeClass("activeBottom");
			}
			if (bottomSiblActive === "false") {
				$(tweet).removeClass("activeBottom");
				$("#post"+bottomSibl).removeClass("activeTop");
			}
			setTimeout(function(){
				$(activeView).hide();
				$(activeBottomView).hide();
				$(regularView).show();
				if (creatingReply) {
					$(tweet).find(".createReplyWrapper").hide();
					$(tweet).find(".clickToReply").show();
				}
			}, 200);
			$(".tweetContentContainer", this).attr("data-original-height", "0");
		}
	});

	$("body").on("tap", ".clickToReply", function(){
		creatingReply = true;
		$(this).parent().find(".createReplyWrapper").show();
		$(this).hide();
		$(this).parent().find(".replyContent").focus().val($(".replyContent.creatingReply").attr("data-original-content")+" ");
	});

	$(".replyContent").focusout(function(){
		$(this).removeClass("creatingReply");
	});

	$(".replyContent").focusin(function(){
		creatingReply = true;
		$("#tweets").attr("data-active-tweet", $(this).parents(".tweetWrapper").attr("id"));
		$(this).addClass("creatingReply");
	});

	$("body").on("tap", ".preventTweetAction", function(e){
		e.stopPropagation();
	});

	$("body").on("input", ".creatingReply", function(){
		var replyChars = $(this).parent().find(".replyChars");
		var replyButton = $(this).parent().find(".replyTweet");
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
			var replyContent = $(this).parent().parent().find(".replyContent");
			if ($(replyContent).val() !== "" && $(replyContent).val().length <= 140) {
				var twid = $(replyContent).parent().find(".twid").val();
				sendReply($(replyContent).val(), username, twid);
			}
		}
	});

	$("body").on("tap", ".replyClick", function(){
		var tool = $(this);
		var replyContent = $(tool).parents(".tweetWrapper").find(".createReplyWrapper .replyContent");
		$(tool).parents(".tweetWrapper").find(".createReplyWrapper").show();
		$(tool).parents(".tweetWrapper").find(".clickToReply").hide();
		setTimeout(function(){
			$(replyContent).focus().val($(replyContent).attr("data-original-content")+" ");
		}, 50);
	});

	$("body").on("tap", ".removeTweet", function(){
		var remove = confirm("Are you sure you want to delete this post?");
		if (remove) {
			tweetRemoval($(this).parents(".tweetWrapper").attr("data-tweet-id"), $(this).parents(".tweetWrapper").attr("data-author-id"));
		}
	});

	$("body").on("tap", ".favouriteClick", function(){
		var fav = $(this).parent();
		var favCo = $(fav).parents(".tweetWrapper").find(".favouriteCount");
		if ($(fav).hasClass("favourited")) {
			var favAm = $(favCo).html();
			favAm--;
			if (favAm == 0) {
				$(favCo).html("");
			} else {
				$(favCo).html(favAm);
			}
			var tweetID = $(fav).parents(".tweetWrapper").attr("data-tweet-id");
			removeFavourite(tweetID, userID);
			$(fav).parents(".tweetWrapper").find(".visibleFavourite").removeClass("favourited");
			$(fav).parents(".tweetWrapper").find(".hiddenFavourite").removeClass("favourited");
		} else {
			var favAm = $(favCo).html();
			favAm++;
			$(favCo).html(favAm);
			$(fav).parents(".tweetWrapper").find(".visibleFavourite").addClass("favourited animateFavourite");
			$(fav).parents(".tweetWrapper").find(".hiddenFavourite").addClass("favourited animateFavourite");
			var tweetID = $(fav).parents(".tweetWrapper").attr("data-tweet-id");
			addFavourite(tweetID, userID);
			setTimeout(function(){
				$(fav).parents(".tweetWrapper").find(".visibleFavourite").removeClass("animateFavourite");
				$(fav).parents(".tweetWrapper").find(".hiddenFavourite").removeClass("animateFavourite");
			}, 400);
		}
	});

	$("body").on("tap", ".removeReply", function(){
		var remove = confirm("Are you sure that you want to delete this reply?");
		if (remove) {
			replyRemoval($(this).parents(".tweetReply").attr("data-reply-id"), $(this).parents(".tweetWrapper").attr("data-tweet-id"), $(this).parents(".tweetReply"));
		}
	});

});

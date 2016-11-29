$(window).load(function(){

	scrolling();

	var loggedUsername = $("#heusna").val();
	var profileUsername = $(".userTitleWrapper h4 a").html().replace("@", "");

	function fixProfileHeight () {
		var headerHeight = $("header").height();
		var fixedContentHeight = $("#userFixedContent").height();
		var fixedUserPageHeight = $("#fixedUserPageContent").height();
		var userPageHeight = headerHeight + fixedContentHeight + fixedUserPageHeight + 30;
		$("#userPage").css("height", userPageHeight);
	}

	fixProfileHeight();

	function scrolling () {
		var scrolled = $(window).scrollTop();
		var fixedPageContentMove = 0;
		var fixedContentMove = 0;
		var coverMove = 0;
		if (scrolled < 0) {
			$("#bigUserImageWrapper").css({
				'-webkit-transform': 'translate3d(0px,0px,0px)',
				'-moz-transform': 'translate3d(0px,0px,0px)',
				'-ms-transform': 'translate3d(0px,0px,0px)',
				'-o-transform': 'translate3d(0px,0px,0px)',
				'transform': 'translate3d(0px,0px,0px)'
			});
			$("#smallUserInfoMoveWrapper").css({
				'-webkit-transform': 'translate3d(0px,60px,0px)',
				'-moz-transform': 'translate3d(0px,60px,0px)',
				'-ms-transform': 'translate3d(0px,60px,0px)',
				'-o-transform': 'translate3d(0px,60px,0px)',
				'transform': 'translate3d(0px,60px,0px)'
			});
		} else if (scrolled > 0 && scrolled < 370) {
			fixedContentMove = scrolled;
			fixedPageContentMove = fixedContentMove;
			coverMove = scrolled / 2;
			$("#bigUserImageWrapper").css({
				'-webkit-transform': 'translate3d(0px,0px,0px)',
				'-moz-transform': 'translate3d(0px,0px,0px)',
				'-ms-transform': 'translate3d(0px,0px,0px)',
				'-o-transform': 'translate3d(0px,0px,0px)',
				'transform': 'translate3d(0px,0px,0px)'
			});
			$("#smallUserInfoMoveWrapper").css({
				'-webkit-transform': 'translate3d(0px,60px,0px)',
				'-moz-transform': 'translate3d(0px,60px,0px)',
				'-ms-transform': 'translate3d(0px,60px,0px)',
				'-o-transform': 'translate3d(0px,60px,0px)',
				'transform': 'translate3d(0px,60px,0px)'
			});
			if ($("#fixedUserPageContent").css("position") !== "fixed") {
				$("#fixedUserPageContent").css({
					'position': 'fixed',
					'top': '520px'
				});
			}

			$("#fixedUserPageContent").css({
				'-webkit-transform': 'translate3d(0px,-'+fixedContentMove+'px,0px)',
				'-moz-transform': 'translate3d(0px,-'+fixedContentMove+'px,0px)',
				'-ms-transform': 'translate3d(0px,-'+fixedContentMove+'px,0px)',
				'-o-transform': 'translate3d(0px,-'+fixedContentMove+'px,0px)',
				'transform': 'translate3d(0px,-'+fixedContentMove+'px,0px)'
			});
		} else if (scrolled >= 370) {
			fixedContentMove = 370;
			fixedPageContentMove = 0;
			coverMove = 370 / 2;
			$("#fixedUserPageContent").css({
				'position': 'relative',
				'top': '0'
			});
			$("#bigUserImageWrapper").css({
				'-webkit-transform': 'translate3d(0px,-200px,0px)',
				'-moz-transform': 'translate3d(0px,-200px,0px)',
				'-ms-transform': 'translate3d(0px,-200px,0px)',
				'-o-transform': 'translate3d(0px,-200px,0px)',
				'transform': 'translate3d(0px,-200px,0px)'
			});
			$("#smallUserInfoMoveWrapper").css({
				'-webkit-transform': 'translate3d(0px,0px,0px)',
				'-moz-transform': 'translate3d(0px,0px,0px)',
				'-ms-transform': 'translate3d(0px,0px,0px)',
				'-o-transform': 'translate3d(0px,0px,0px)',
				'transform': 'translate3d(0px,0px,0px)'
			});

		}
		$("#fixedUserPageContent").css({
			'-webkit-transform': 'translate3d(0px,-'+fixedPageContentMove+'px,0px)',
			'-moz-transform': 'translate3d(0px,-'+fixedPageContentMove+'px,0px)',
			'-ms-transform': 'translate3d(0px,-'+fixedPageContentMove+'px,0px)',
			'-o-transform': 'translate3d(0px,-'+fixedContentMove+'px,0px)',
			'transform': 'translate3d(0px,-'+fixedPageContentMove+'px,0px)'
		});
		$("#userFixedContent").css({
			'-webkit-transform': 'translate3d(0px,-'+fixedContentMove+'px,0px)',
			'-moz-transform': 'translate3d(0px,-'+fixedContentMove+'px,0px)',
			'-ms-transform': 'translate3d(0px,-'+fixedContentMove+'px,0px)',
			'-o-transform': 'translate3d(0px,-'+fixedContentMove+'px,0px)',
			'transform': 'translate3d(0px,-'+fixedContentMove+'px,0px)'
		});
		$(".userCoverImage").css({
			'-webkit-transform': 'translate3d(0px,'+coverMove+'px,0px)',
			'-moz-transform': 'translate3d(0px,'+coverMove+'px,0px)',
			'-ms-transform': 'translate3d(0px,'+coverMove+'px,0px)',
			'-o-transform': 'translate3d(0px,'+coverMove+'px,0px)',
			'transform': 'translate3d(0px,'+coverMove+'px,0px)'
		});
	}

	function followUserAction (followerUN, followedUN) {
		var message = "userFollow";
		$.post('resources/sections/actions.php', {
			message: message,
			follower: followerUN,
			followed: followedUN
		}).success(function(data){
			data = $.parseJSON(data);
			if (data.message === "success") {
				$("#followUser").remove();
				$("#userMenuBar .wrapper .moveRight").append('<div class="blueButton" id="unfollowUser"></div>');
			}
		});
	}

	function unFollowUserAction (followerUN, followedUN) {
		var message = "userUnfollow";
		$.post('resources/sections/actions.php', {
			message: message,
			follower: followerUN,
			followed: followedUN
		}).success(function(data){
			data = $.parseJSON(data);
			if (data.message === "success") {
				$("#unfollowUser").remove();
				$("#userMenuBar .wrapper .moveRight").append('<div class="blueButton" id="followUser">Follow</div>');
			}
		});
	}

	$(window).scroll(function(){
		scrolling();
	});

	$(window).resize(function(){
		scrolling();
	});

	$("body").on("tap", "#userLoginButton", function(){
		$("#hiddenUserLoginButton").click();
	});

	$("body").on("tap", "#followUser", function(){
		followUserAction(loggedUsername, profileUsername);
	});

	$("body").on("tap", "#unfollowUser", function(){
		unFollowUserAction(loggedUsername, profileUsername);
	});

	$("body").on("tap", ".tweetWrapper", function(){
		$(this).find("#statusRed").click();
	});

});

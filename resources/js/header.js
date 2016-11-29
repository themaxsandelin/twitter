$(window).load(function(){
	
	userDropdownMenu = "hidden";
	userDropdownAnimation = "done";
	
	userLoginDropdown = "hidden";
	userLoginDropdownAnimation = "done";
	
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
	
	function showloginDropdown () {
		$("#userLoginDropdownWrapper").show();
		userLoginDropdownAnimation = "running";
		setTimeout(function(){
			$("#userLoginDropdownWrapper").css({
				'-webkit-transform': 'translate3d(0px,0px,0px)',
				'-moz-transform': 'translate3d(0px,0px,0px)',
				'-ms-transform': 'translate3d(0px,0px,0px)',
				'-o-transform': 'translate3d(0px,0px,0px)',
				'transform': 'translate3d(0px,0px,0px)',
				'opacity': '1'
			});
			setTimeout(function(){
				// The dropdown animation is done. Do this!
				userLoginDropdown = "showing";
				userLoginDropdownAnimation = "done";
			}, 310);
		}, 10);
	}
	
	function hideloginDropdown () {
		$("#userLoginDropdownWrapper").css({
			'-webkit-transform': 'translate3d(0px,10px,0px)',
			'-moz-transform': 'translate3d(0px,10px,0px)',
			'-ms-transform': 'translate3d(0px,10px,0px)',
			'-o-transform': 'translate3d(0px,10px,0px)',
			'transform': 'translate3d(0px,10px,0px)',
			'opacity': '0'
		});
		userLoginDropdownAnimation = "running";
		setTimeout(function(){
			$("#userLoginDropdownWrapper").hide();
			userLoginDropdown = "hidden";
			userLoginDropdownAnimation = "done";
		}, 310);
	}
	
	function userDropdown () {
		if (userDropdownMenu == "hidden" && userDropdownAnimation == "done") {
			showUserDropdown();
		} else if (userDropdownMenu == "showing" && userDropdownAnimation == "done") {
			hideUserDropdown();
		}
	}
	
	function loginDropdown () {
		if (userLoginDropdown == "hidden" && userLoginDropdownAnimation == "done") {
			showloginDropdown();
		} else if (userLoginDropdown == "showing" && userLoginDropdownAnimation == "done") {
			hideloginDropdown();
		}
	}
	
	function showHeaderTweet () {
		$("body").css("overflow", "hidden");
		$("#createHeaderTweetContainer").show();
		setTimeout(function(){
			$("#createHeaderTweetContainer, #createHeaderTweetWrapper").removeClass("hide");
		}, 10);
	}
	
	function hideHeaderTweet () {
		$("#createHeaderTweetContainer, #createHeaderTweetWrapper").addClass("hide");
		setTimeout(function(){
			$("#createHeaderTweetContainer").hide();
			$("body").css("overflow", "visible");
		}, 400);
	}
	
	function countTweetChars (input) {
		charsLeft = 140 - input.length;
		$(".headerTweetChars").html(charsLeft);
		if (charsLeft <= 0) {
			if (!$("#createHeaderTweet").hasClass("disabled")) {
				$("#createHeaderTweet").addClass("disabled");
			}
		}
		if (charsLeft < 140 && charsLeft > 0) {
			if ($("#createHeaderTweet").hasClass("disabled")) {
				$("#createHeaderTweet").removeClass("disabled");
			}
		}
		if (charsLeft == 140) {
			if (!$("#createHeaderTweet").hasClass("disabled")) {
				$("#createHeaderTweet").addClass("disabled");
			}
		}
		if (charsLeft < 11) {
			if ($(".headerTweetChars").hasClass("dark")) {
				$(".headerTweetChars").removeClass("dark");
			}
			$(".headerTweetChars").addClass("red");
		} else if (charsLeft >= 11 && charsLeft < 21) {
			if ($(".headerTweetChars").hasClass("red")) {
				$(".headerTweetChars").removeClass("red");
			}
			$(".headerTweetChars").addClass("dark");
		} else if (charsLeft >= 21) {
			if ($(".headerTweetChars").hasClass("dark")) {
				$(".headerTweetChars").removeClass("dark");
			}
			if ($(".headerTweetChars").hasClass("red")) {
				$(".headerTweetChars").removeClass("red");
			}
		}
	}
	
	function previewTweetImage (allFiles){
		var fileList = allFiles.files;
		var anyWindow = window.URL || window.webkitURL;
		for(var i = 0; i < fileList.length; i++){
			var objectUrl = anyWindow.createObjectURL(fileList[i]);
			$('.headerTweetImagePreview').empty().append('<img src="'+objectUrl+'" alt="tweet preview image" class="previewHeaderTweetImage">');
			window.URL.revokeObjectURL(fileList[i]);
		}
	}
	
	$("body").on("tap", ".userHeadImageWrapper", function(){
		userDropdown();
	});
	
	$("body").on("tap", ".userLoginTextWrapper", function(){
		loginDropdown();
	});
	
	$("body").on("tap", ".userHead", function(e){
		e.stopImmediatePropagation();
	});
	
	$("body").on("tap", ".userLoginWrapper", function(e){
		e.stopImmediatePropagation();
	});
	
	$(document).on("tap", function(event){
		if (userDropdownMenu == "showing" && userDropdownAnimation == "done") {
			hideUserDropdown();
		} else if (userLoginDropdown == "showing" && userLoginDropdownAnimation == "done") {
			hideloginDropdown();
		}
	});
	
	$("body").on("tap", "#newTweet", function(){
		showHeaderTweet();
	});
	
	$("body").on("tap", ".closeHeaderTweet", function(){
		hideHeaderTweet();
	});
	
	$("body").on("input", "#createHeaderTweetContent", function(){
		var input = $(this).val();
		countTweetChars(input);
	});
	
	$("body").on("tap", ".createHeaderTweetAddImage", function(){
		$("#createHeaderTweetUpload").click();
	});
	
	$("#createHeaderTweetUpload").on("change", function(){
		previewTweetImage(this);
		$(".createHeaderTweetAddImage span").html("Change image");
	});
	
	$("body").on("tap", "#createHeaderTweet", function(){
		var tweet = $("#createHeaderTweetContent").val();
		if (tweet !== "" && tweet.length <= 140) {
			$("#hiddenHeaderTweetButton").click();
		}
	});
	
	$("#headerTweetForm").submit(function(e){
		$.ajax({
			url: '/resources/sections/actions.php',
			type: 'POST',
			data: new FormData(this),
			processData: false,
			contentType: false,
			success: function (data) {
				hideHeaderTweet();
				setTimeout(function(){
					document.location.reload(true);
				}, 400);
			}
		});
		e.preventDefault();
	});

});
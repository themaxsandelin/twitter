$(window).load(function(){

	function validateAccountSettingsChange () {
		var correctInfo = true;
		if ($("#currentAccountPassword").val() == "") {
			$("#currentAccountPassword").addClass("error");
			correctInfo = false;
		}
		if ($("#setName").val() == "") {
			$("#setName").addClass("error");
			correctInfo = false;
		}
		if ($("#setUsername").val() == "") {
			$("#setUsername").addClass("error");
			correctInfo = false;
		}
		if ($("#setEmail").val() == "") {
			$("#setEmail").addClass("error");
			correctInfo = false;
		}
		if (correctInfo) {
			$("#accountChanges").submit();
		}
	}

	function previewImages (allFiles, id){
		var fileList = allFiles.files;
		var anyWindow = window.URL || window.webkitURL;
		for(var i = 0; i < fileList.length; i++){
			var objectUrl = anyWindow.createObjectURL(fileList[i]);
			$("#"+id).attr("src", objectUrl);
			window.URL.revokeObjectURL(fileList[i]);
		}
	}

	function validatePasswordChange () {
		var newPass = $("#newPassword").val();
		var newPassVal = $("#newPasswordVal").val();
		var password = $("#currentAccountPassword").val();
		var valid = true;
		if (newPass === "") {
			$("#newPassword").addClass("error");
			valid = false;
		}
		if (newPassVal === "") {
			$("#newPasswordVal").addClass("error");
			valid = false;
		}
		if (password === "") {
			$("#currentAccountPassword").addClass("error");
			valid = false;
		}
		if (valid) {
			if (newPass !== newPassVal) {
				$("#newPassword, #newPasswordVal").addClass("error");
				$("#messageWrapper").empty().append('<div class="errorBox">The passwords doesn\'t match<div id="closeError"><i class="fa fa-times" id="closeErrorIcon"></i></div></div>');
				valid = false;
			}
		}
		return valid;
	}

	$("#saveAccountSettings").on("tap", function(){
		validateAccountSettingsChange();
	});

	$(".settingsFieldSet input, .settingsFieldSet textarea").on("focus", function(){
		$(this).removeClass("error");
	});

	$("body").on("tap", "#closeError", function(){
		$(this).parent().remove();
	});

	$("body").on("tap", "#closeMessage", function(){
		$(this).parent().remove();
	});

	$("#changeCover").on("tap", function(){
		$("#coverImageFile").click();
	});

	$("#setName").keypress(function(event){
		if(event.keyCode == 13){
			$(this).blur();
			$("#setUsername").focus();
		}
	});

	$("#setUsername").keypress(function(event){
		if(event.keyCode == 13){
			$(this).blur();
			$("#setEmail").focus();
		}
	});

	$("#setEmail").keypress(function(event){
		if(event.keyCode == 13){
			$(this).blur();
			$("#setBio").focus();
		}
	});

	$("#setBio").keypress(function(event){
		if(event.keyCode == 13){
			$(this).blur();
			$("#setURL").focus();
		}
	});

	$("#setURL").keypress(function(event){
		if(event.keyCode == 13){
			$(this).blur();
			$("#currentAccountPassword").focus();
		}
	});

	$("#currentAccountPassword").keypress(function(event){
		if(event.keyCode == 13){
			validateAccountSettingsChange();
		}
	});

	$("#coverImageFile").on("change", function(){
		previewImages(this, "coverImagePreview");
	});

	$("#saveImagesSettings").on("tap", function(){
		if ($("#currentAccountPassword").val() == "") {
			$("#currentAccountPassword").addClass("error");
		} else {
			$("#saveImagesHidden").click();
		}
	});

	$("#changeProfileImage").on("tap", function(){
		$("#profileImageFile").click();
	});

	$("#profileImageFile").on("change", function(){
		previewImages(this, "profileImagePreview");
	});

	$("#savePasswordSettings").on("tap", function(){
		var validPassChange = validatePasswordChange();
		if (validPassChange) {
			$("#hiddenSavePassword").click();
		}
	});

});

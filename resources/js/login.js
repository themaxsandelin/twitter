$(window).load(function(){
	
	function validateEmail(email) { 
	    var pattern = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
	    return pattern.test(email);
	} 
	
	$("body").on("tap", "#loginButton", function(){
		var validLogin = true;
		var username = $("#loginUsername").val();
		var password = $("#loginPassword").val();
		if (username === "") {
			$("#loginUsername").addClass("error");
			validLogin = false;
		}
		if (password === "") {
			$("#loginPassword").addClass("error");
			validLogin = false;
		}
		if (validLogin) {
			$("#hiddenLogin").click();
		}
	});
	
	$("body").on("tap", "#registerButton", function(){
		var validRegister = true;
		var name = $("#registerName").val();
		var email = $("#registerEmail").val();
		var username = $("#registerUsername").val();
		var password = $("#registerPassword").val();
		if (name === "") {
			$("#registerName").addClass("error");
			validRegister = false;
		}

		if (email === "" || validateEmail(email) === false) {
			$("#registerEmail").addClass("error");
			validRegister = false;
		}
		if (username === "") {
			$("#registerUsername").addClass("error");
			validRegister = false;
		}
		if (password === "") {
			$("#registerPassword").addClass("error");
			validRegister = false;
		}
		if (validRegister) {
			$("#hiddenRegister").click();
		}
	});
	
	$("input").on("focus", function(){
		$(this).removeClass("error");
	});
	
});
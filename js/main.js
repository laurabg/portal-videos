
function beforeSubmit(formData, jqForm, options) { 
	var queryString = $.param(formData); 

	console.log('enviando... ('+queryString+')');

	if (queryString.indexOf('logout') != -1) {
		location.reload();
	}

	return true;
} 
 
function submitDone(responseText, statusText, xhr, $form)  { 
	console.log('done!!!');
	
	if (responseText != '') {
		$('.form-error').show();
		loadAjaxForm();
	} else {
		location.reload();
	}
} 

function loadAjaxForm() {
	$('form[name="userSession"]').ajaxForm({
		target: 		'.form-error',
		beforeSubmit: 	beforeSubmit,
		success: 		submitDone
	});
}

function getCookie(cname) {
	var name = cname + "=";
	var ca = document.cookie.split(';');
	for(var i=0; i<ca.length; i++) {
		var c = ca[i];
		while (c.charAt(0)==' ') c = c.substring(1);
		if (c.indexOf(name) == 0) return c.substring(name.length,c.length);
	}
	return "";
}

$(window).load(function() {
	loadAjaxForm();
	
	if (getCookie('MoodleUserFaltaCorreo') != '') {
		$('#pedirEmail').modal({
			show: true,
			backdrop: 'static'
		});
	}
	
	var sPageURL = window.location.search.substring(1);
	var sURLVariables = sPageURL.split('&');

	firstPlay = 1;
	$('video').on('play', function() {
		if (firstPlay == 1) {
			$.ajax({
				type: 'POST',
				async: true,
				url: 'modules/videoPlayed.php',
				data: sPageURL
			});
			
			firstPlay = 0;
		}
	});
});
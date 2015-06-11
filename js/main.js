
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

$(window).load(function() {
	loadAjaxForm();

	var sPageURL = window.location.search.substring(1);
	var sURLVariables = sPageURL.split('&');

	firstPlay = 1;
	$('video').on('play', function() {
		if (firstPlay == 1) {

			$.ajax({
				type: 'POST',
				async: true,
				url: 'ajax/videoPlayed.php',
				data: sPageURL,
				success: function(msg) {
					alert(msg);
				}
			});
			
			firstPlay = 0;
		}
	});
	/*
	// Tabs para cursos:
	$('#admin-cursos a').click(function() {
		divCurso = $(this).attr('href');
		IDcurso = divCurso.replace('#curso-','');

		$(this).tab('show');
		$(divCurso).load('ajax/admin-curso.php?IDcurso='+IDcurso, function() {});
	});
	
	// Cargar el contenido de la primera pestaña:
	divCurso = $('div.tab-content').children('.active').attr('id');
	if (divCurso) {
		IDcurso = divCurso.replace('curso-','');

		$('#'+divCurso).load('ajax/admin-curso.php?IDcurso='+IDcurso, function() {});
	}

	if ($('.datepicker').length > 0) {
		// Datepickers:
		$('.datepicker').datepicker({
			weekStart: 1,
			format: 'yyyy-mm-dd',
			locale: 'es'
		});
	}*/
});
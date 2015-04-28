function loadTree() {
    // Ocultar todos los li
    $('.tree li').hide();
    // Mostrar solo los de primer nivel:
    $('.tree li.firstChild').show();

    $('.tree li:has(ul)').addClass('parent_li').find(' > span').attr('title', 'Collapse this branch');
    
    // Al entrar, si estamos viendo algún elemento en concreto, expandir lo que sea necesario:
    $('.tree ul > li.parent_li').each(function() {
    	if ($(this).hasClass('expanded') == true) {
	        var children = $(this).find(' > ul > li');
            children.show();
            $(this).children('div.item').attr('title', 'Collapse this branch').find('.glyphicon-folder-close').addClass('glyphicon-folder-open').removeClass('glyphicon-folder-close');
    	}
    });

    // Al hacer click en un elemento, expandir su contenido:
    $('.tree li.parent_li > div.item').on('click', function (e) {
        var children = $(this).parent('li.parent_li').find(' > ul > li');
        if (children.is(":visible")) {
            children.hide('fast');
            $(this).attr('title', 'Expand this branch').find('.glyphicon-folder-open').addClass('glyphicon-folder-close').removeClass('glyphicon-folder-open');
        } else {
            children.show('fast');
            $(this).attr('title', 'Collapse this branch').find('.glyphicon-folder-close').addClass('glyphicon-folder-open').removeClass('glyphicon-folder-close');
        }
        e.stopPropagation();
    });
}

$(window).load(function() {
	var sPageURL = window.location.search.substring(1);
	var sURLVariables = sPageURL.split('&');

	loadTree();

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
	// Datepickers:
	$('.datepicker').datepicker({
		weekStart: 1,
		format: 'yyyy-mm-dd',
		locale: 'es'
	});
});
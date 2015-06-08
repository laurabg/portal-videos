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
	loadTree();
	
	if ($('.datepicker').length > 0) {
		// Datepickers:
		$('.datepicker').datepicker({
			weekStart: 1,
			format: 'yyyy-mm-dd',
			locale: 'es'
		});
	}

	$('form[name="config"] .add-ub').click(function() {
		newUb = '<div class="row"><div class="col-md-2"></div><div class="col-md-10">';
		newUb = newUb + '<input type="text" class="form-control" name="ubicacion-new[]" id="ubicacion" value="" /></div></div>';
		$('.listaUbicaciones').append(newUb);
	});


	$('form[name="config"] .add-ext').click(function() {
		newUb = '<div class="row"><div class="col-md-2"></div><div class="col-md-10">';
		newUb = newUb + '<input type="text" class="form-control" name="extension-new[]" id="extension" value="" /></div></div>';
		$('.listaExtensiones').append(newUb);
	});
	/*
	$('.check-delete').click(function() {
		todosOff = true;
		listaChecks = document.getElementsByClassName('check-delete');

		for (i = 0; i < listaChecks.length; i++) {
			if (listaChecks[i].checked == true) {
				todosOff = false;
			}
		}

		if (todosOff == true) {
			$('.del-ub').addClass('disabled');
		} else {
			$('.del-ub').removeClass('disabled');
		}
	});
	*/
	$('.btn-cancel').click(function() {
		window.location.reload();
	});
});
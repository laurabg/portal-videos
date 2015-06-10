function loadMenu() {
	/*$('ul.menu-sortable').sortable({
		containment: 'parent',
		items: 'li:not(.ui-state-disabled)',
		start: function(event, ui) {
			if ($(ui.item[0]).children('.item').children('span').hasClass('glyphicon-folder-open')) {
				$(ui.item[0]).children('.item').trigger('click');
			}
		},
		stop: function(event, ui) {
			var sorted = $('ul.menu-sortable').sortable( "toArray", { attribute: "IDcurso" } );;
			console.log(sorted);
		}
	});*/
	
	$('.nav-sidebar a').click(function() {
		$('.nav-sidebar li').removeClass('active');
		$(this).closest('li').addClass('active');
		template = getUrlParameter('opt', $(this).attr('href'));

		url = 'modules-admin/templates/'+template+'.php?opt='+template;

		if ($(this).attr('href').indexOf('IDcurso') != -1) {
			url = url + '&IDcurso='+getUrlParameter('IDcurso', $(this).attr('href'));
		}
		if ($(this).attr('href').indexOf('IDtema') != -1) {
			url = url + '&IDtema='+getUrlParameter('IDtema', $(this).attr('href'));
		}
		if ($(this).attr('href').indexOf('IDvideo') != -1) {
			url = url + '&IDvideo='+getUrlParameter('IDvideo', $(this).attr('href'));
		}

		$('.main').html('').load(url, function () {
			loadAjaxForm();
		});
		return false;
	});

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

function getUrlParameter(sParam, fullURL) {
	if (fullURL == '') {
		fullURL = window.location.search.substring(1);
	}
	if ( (fullURL.indexOf('?') != -1)&&(fullURL.split('?').length > 0) ) {
		sPageURL = fullURL.split('?')[1];
	} else {
		sPageURL = fullURL;
	}
	var sURLVariables = sPageURL.split('&');
	for (var i = 0; i < sURLVariables.length; i++)  {
		var sParameterName = sURLVariables[i].split('=');

		if (sParameterName[0] == sParam)  {
			return sParameterName[1];
		}
	}
}       

function loadAjaxForm() {
	/*if ($('.datepicker').length > 0) {
		// Datepickers:
		$('.datepicker').datepicker({
			weekStart: 1,
			format: 'yyyy-mm-dd',
			locale: 'es'
		});
	}*/

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

	$('.btn-cancel').click(function() {
		window.location.reload();
	});

	// Cargar funcionalidad ajax para todos los formularios:
	$('form').unbind().ajaxForm({
		target: 		'.main',
		beforeSubmit: 	beforeSubmit,
		success: 		submitDone
	});
}

function beforeSubmit(formData, jqForm, options) { 
	var queryString = $.param(formData); 

	console.log('enviando... ('+queryString+')');

	// Si se ha pulsado "eliminar", pedir confirmacion:
	if (queryString.indexOf('formDel') != -1) {
		if (confirm('¿Desea eliminar este elemento?')) {
			return true
		}
	} else {
		return true;
	}

	return false;
} 
 
function submitDone(responseText, statusText, xhr, $form)  { 
	console.log('done!!!');

	opt = document.getElementsByName('form')[0].value;
	IDcurso = '';
	if (document.getElementsByName('IDcurso').length > 0) {
		IDcurso = document.getElementsByName('IDcurso')[0].value;
	}
	IDtema = '';
	if (document.getElementsByName('IDtema').length > 0) {
		IDtema = document.getElementsByName('IDtema')[0].value;
	}
	IDvideo = '';
	if (document.getElementsByName('IDvideo').length > 0) {
		IDvideo = document.getElementsByName('IDvideo')[0].value;
	}
	
	// Si ha ido todo bien, recargar el menu:
	if ( (responseText.indexOf('alert-success') != -1)||(responseText.indexOf('alert-danger') != -1) ) {
		$('.sidebar').html('').load('modules-admin/menu.php?opt='+opt+'&IDcurso='+IDcurso+'&IDtema='+IDtema+'&IDvideo='+IDvideo, function() {
			loadMenu();
		});
	}

	loadAjaxForm();
} 

$(window).load(function() {
	loadMenu();
	loadAjaxForm();
});
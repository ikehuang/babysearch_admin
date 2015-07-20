(function($, window) {
	
	$('.imei-desc').on('click', function() {
		var w = $(window).width() > 600 ? 600 : $(window).width();
		var l = ($(window).width()-600)/2 > 0 ? ($(window).width()-600)/2 : 0;

		$.blockUI({
			message: $('#dialog'),
			css: { width: w, height: w, top: '5%', left: l }

		});
	});
	$('.del').on('click', function() {
		$.unblockUI();
	});
	$('.petinfo--continue').on('click', function() {
		alert('尚不需續用');
	});
	if( $('.select-lang').length ) {
		$('.select-lang').on('change', function() {
			window.location.href= '/i18n/'+$(this).val();
		});
	}
})(jQuery, window);
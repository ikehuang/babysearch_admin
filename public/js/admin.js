(function($, window, undefined) {
	$('#stop').on('click', function() {
		
		var list = '';
		  
		  $('input[name="choose"]:checked').each(function(){
		   list += $(this).val()+",";
		  });
		  
		  list = list.replace(/,$/,'');

		  if(list.length == 0)
		   return false;
		  
		  window.location.href = '/admin/invalid/' + list;
	});
	
	/*
	* Choice All
	**/
	$('.choice-all').on('click', function() {
		if( this.checked === true ) {
			$('.choice:checkbox').prop('checked', true);
		} else {
			$('.choice:checkbox').prop('checked', false);
		}
	});
	$('.change-status').on('click', function() {
		var items = $('.choice:checked');
		var imieArr = [];
		if( items.length ) {
			items.each(function(k, v) {
				imieArr.push(v.value);
			});
			$.ajax({
				url: '/admin/active',
				type: 'POST',
				data: { 
					csrf_key: csrf_token,
					imeis: imieArr
				},
				dataType: 'json'
			}).done(function(response) {
				alert('裝置開通完成');
				window.location.reload();
			}).fail(function() {
				alert('裝置開通失敗');
			});
		} else {
			alert('請選擇要變更之選項！');
		}
	});

	$('.edit').on('click', function() {
		$.blockUI({
			message: $('.update-buyer'),
			css: {
				top: '5%',
				left: '10%',
				width: '80%'
			}
		});
	});
	$('.cancel').on('click', function() {
		$.unblockUI();
	});
})(jQuery, window);
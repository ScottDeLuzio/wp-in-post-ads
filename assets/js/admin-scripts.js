jQuery(document).ready(function ($) {

	// Tooltips
	$('.wpipa-help-tip').tooltip({
		content: function() {
			return $(this).prop('title');
		},
		tooltipClass: 'wpipa-ui-tooltip',
		position: {
			my: 'center top',
			at: 'center bottom+10',
			collision: 'flipfit',
		},
		hide: {
			duration: 200,
		},
		show: {
			duration: 200,
		},
	});
});
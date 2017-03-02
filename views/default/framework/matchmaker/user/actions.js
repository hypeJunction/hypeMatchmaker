define(function (require) {

	var $ = require('jquery');
	var elgg = require('elgg');

	$(document).on('click', '.elgg-menu-matchmaker-actions .elgg-menu-item-details', function (e) {
		e.preventDefault();
		var $elem = $(this);
		var $stats = $elem.closest('.elgg-item').find('.matchmaker-stats');
		if ($elem.is('.elgg-state-active')) {
			$stats.slideUp();
			$elem.removeClass('elgg-state-active');
			$elem.find('a').text(elgg.echo('matchmaker:details:show'));
		} else {
			$elem.closest('.elgg-item').find('.matchmaker-stats').slideDown();
			$elem.addClass('elgg-state-active');
			$elem.find('a').text(elgg.echo('matchmaker:details:hide'));
		}
	});
});
$(document).ready(function () {

	// Setup mobile main menu
	$('#btn-topbar-toggle').toggle(
		function() {
			$('#topbar .wrapper').slideDown();
		},
		function() {
			$('#topbar .wrapper').slideUp();
		}
	);

	// Setup mobile main menu
	$('#btn-mobile-toggle').toggle(
		function() {
			$('#mainmenu').slideDown();
		},
		function() {
			$('#mainmenu').slideUp();
		}
	);
	
	// Smooth drop-down main menu
	$('#mainmenu').superfish({
		autoArrows: false,
		animation: {height: 'show'},
		speed: 300,
		delay: 0,
	});

	// Smooth drop-down view cart
	var $cart = $('#cart');

	$cart.find('> .heading a').die('click').live('click', function () {
		$cart.addClass('active').load('index.php?route=module/cart #cart > *', {}, function () {
			$cart.find('.content').stop(true, false).animate({height: 'show'}, 'fast');
		});
	})

	.end().live('mouseleave', function(evt) {
		$cart.removeClass('active').find('.content').stop(true, false).animate({height: 'hide'}, 'fast');
	});

});
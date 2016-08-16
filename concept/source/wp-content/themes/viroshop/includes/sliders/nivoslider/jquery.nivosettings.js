if(nivo.randomStart == null) {nivo.randomStart = false;}
if(nivo.manualAdvance == null) {nivo.manualAdvance = false;}
if(nivo.pauseOnHover == null) {nivo.pauseOnHover = false;}
if(nivo.directionNav == null) {nivo.directionNav = false;}
if(nivo.directionNavHide == null) {nivo.directionNavHide = false;}
if(nivo.controlNav == null) {nivo.controlNav = false;}

jQuery(window).bind("load", function() {
	jQuery('#slider').nivoSlider({
		effect: nivo.effect, // Specify sets like: 'fold,fade,sliceDown'
		slices: nivo.slices, // For slice animations
		boxCols: 6, // For box animations
		boxRows: nivo.boxRows, // For box animations
		animSpeed: 500, // Slide transition speed
		pauseTime: nivo.pauseTime, // How long each slide will show
		startSlide: 0, // Set starting Slide (0 index)
		directionNav: nivo.directionNav, // Next & Prev navigation
		controlNav: nivo.controlNav, // 1,2,3... navigation
		controlNavThumbs: false, // Use thumbnails for Control Nav
		pauseOnHover: nivo.pauseOnHover, // Stop animation while hovering
		manualAdvance: nivo.manualAdvance, // Force manual transitions
		prevText: 'Prev', // Prev directionNav text
		nextText: 'Next', // Next directionNav text
		randomStart: nivo.randomStart // Start on a random slide
	});
});

  
jQuery(function ($) //Wait until the page has finished loading
{
	$('div.slick-carousel').slick(GLOBAL_SLICK_OPTIONS); //GLOBAL_SLICK_OPTIONS is defined in Carousel.php by the Carousel::Requirements() method.
});

<?php

class Carousel extends Object
{
	/**
	 * How many slides to list in a single page in the Carousel tab in the CMS.
	 *
	 * @conf int
	 */
	private static $cms_slides_per_page	= 50;
	
	/**
	 * Options to pass to the Slick jQuery plugin during initialization. With these you can greatly affect the behaviour
	 * of the carousel in the frontend.
	 *
	 * This array can contain any option that is mentioned here: http://kenwheeler.github.io/slick/#settings
	 * The options are passed as-is, so if new versions of Slick define new options, no PHP code modifications are needed.
	 * You just need to manually upgrade the included Slick JavaScript library.
	 *
	 * @conf array
	 */
	private static $slick_options		= array();
	
	
	public static function Requirements()
	{
		Requirements::css('slickcarousel/vendor/slick/slick/slick.css');
		Requirements::css('slickcarousel/vendor/slick/slick/slick-theme.css');
		Requirements::javascript('framework/thirdparty/jquery/jquery.min.js');
		Requirements::javascript('slickcarousel/vendor/slick/slick/slick.min.js');
		Requirements::javascript('slickcarousel/js/slick-init.js');
		Requirements::customScript("
		//Slick carousel options:
		var GLOBAL_SLICK_OPTIONS = ".self::options2js().";
		", 'Define Slick options only once, please :).'); //The last parameter is a unique script ID. It does not appear anywhere in the frontend or backend.
	}
	
	private static function options2js()
	{
		return Convert::array2json(self::config()->get('slick_options'));
	}
}

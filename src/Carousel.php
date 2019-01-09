<?php

namespace Taitava\SlickCarousel;

use SilverStripe\Core\Config\Configurable;
use SilverStripe\Core\Convert;
use SilverStripe\View\Requirements;

class Carousel
{
	use Configurable;
	
	/**
	 * How many slides to list in a single page in the Carousel tab in the CMS.
	 *
	 * @conf int
	 */
	private static $cms_slides_per_page		= 50;
	
	/**
	 * Where to place the slide's image inside the carousel slide <div> element:
	 * - 'background': the image will be used as the <div>'s background-image.
	 * - 'before-content': the image will be used as an <img> element before the HTML contained in the Content field.
	 * - 'after-content': the image will be used as an <img> element after the HTML contained in the Content field.
	 *
	 * @conf string
	 */
	private static $image_placement			= 'background';
	
	
	/**
	 * Whether or not to set the slide <div> element's width and/or height to be the same as the image's width and/or
	 * height. Can be useful when $image_placement is 'background' and you are not using constant dimensions that you
	 * define in your CSS. Possible values:
	 * - false
	 * - 'width-only'
	 * - 'height-only'
	 * - true (both width and height)
	 *
	 * @conf bool
	 */
	private static $use_image_dimensions		= false;
	
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
	private static $slick_options			= [];
	
	
	public static function Requirements()
	{
		Requirements::css('taitava/silverstripe-slickcarousel: vendor/slick/slick/slick.css');
		Requirements::css('taitava/silverstripe-slickcarousel: vendor/slick/slick/slick-theme.css');
		Requirements::javascript('silverstripe/admin: thirdparty/jquery/jquery.min.js');
		Requirements::javascript('taitava/silverstripe-slickcarousel: vendor/slick/slick/slick.min.js');
		Requirements::javascript('taitava/silverstripe-slickcarousel: js/slick-init.js');
		$eol = PHP_EOL; //Try to make the custom script work in windows servers too. Not sure if this helps though, as I'm not able to test it.
		Requirements::customScript("
		//Slick carousel options:$eol
		var GLOBAL_SLICK_OPTIONS = ".self::options2js().";
		", 'Define Slick options only once, please :).'); //The last parameter is a unique script ID. It does not appear anywhere in the frontend or backend.
	}
	
	/**
	 * Tells whether the slide's image's width should be injected to the slide <div>'s HTML style attribute.
	 *
	 * @return bool
	 */
	public static function UseImageWidth()
	{
		$use_image_dimensions = self::config()->get('use_image_dimensions');
		return $use_image_dimensions === true || $use_image_dimensions == 'width-only';
	}
	
	/**
	 * Tells whether the slide's image's height should be injected to the slide <div>'s HTML style attribute.
	 *
	 * @return bool
	 */
	public static function UseImageHeight()
	{
		$use_image_dimensions = self::config()->get('use_image_dimensions');
		return $use_image_dimensions === true || $use_image_dimensions == 'height-only';
	}
	
	/**
	 * Exports Slim specific settings from YAML to JavaScript so that they can be easily used when initialising Slick.
	 *
	 * @return string
	 */
	private static function options2js()
	{
		self::validate_options(); //Even if the validation fails, convert and return the options so that the developer can see from the output how the options come out.
		return Convert::array2json(self::config()->get('slick_options'));
	}
	
	/**
	 * Just checks that the Slick configuration options in YAML is listed in a correct format without preceding dashes
	 * in option lines. Otherwise the options would render as nested arrays inside the settings array, which would render
	 * the options useless. Perhaps not very much needed check, but as I made this mistake once, I don't want to make
	 * it again without getting any notifications! :)
	 *
	 * WRONG:
	 * Carousel:
	 *   slick_options:
	 *     - autoplay: true
	 *     - autoplaySpeed: 3000
	 *
	 * CORRECT:
	 * Carousel:
	 *   slick_options:
	 *     autoplay: true
	 *     autoplaySpeed: 3000
	 */
	private static function validate_options()
	{
		$slick_options = self::config()->get('slick_options');
		if (isset($slick_options[0]))
		{
			user_error('Slick carousel options are defined in an incorrect format in YAML. Option lines should not be preceded with dashes.', E_USER_WARNING);
		}
	}
}

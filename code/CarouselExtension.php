<?php


/**
 * Class CarouselExtension
 *
 * @method DataList CarouselSlides
 *
 * @property SiteTree|CarouselExtension $owner
 */
class CarouselExtension extends DataExtension
{
	private static $has_many = array(
		'CarouselSlides' => 'CarouselSlide',
	);
	
	public function updateCMSFields(FieldList $fields)
	{
		$fields->addFieldToTab('Root', new Tab('Carousel', _t('Carousel.CMSTabName', 'Carousel')));
		
		$gridfield_config = new GridFieldConfig_RelationEditor(Carousel::config()->get('cms_slides_per_page'));
		if (ClassInfo::exists('GridFieldSortableRows'))
		{
			//The SortableGridField module is installed - which is nice :)
			//See: https://github.com/UndefinedOffset/SortableGridField
			$gridfield_config->addComponent(new GridFieldSortableRows('Sort'));
		}
		
		$gridfield = new GridField('CarouselSlides', _t('Carousel.CMSTabName', 'Carousel'), $this->owner->CarouselSlides(), $gridfield_config);
		$fields->addFieldToTab('Root.Carousel', $gridfield);
	}
	
	/**
	 * Renders the carousel using Carousel.ss (which will lead to include CarouselSlide.ss for each slide). If you wish
	 * to implement some custom iteration for the carousel slides, you can call $CarouselSlides in your template instead
	 * of this method.
	 *
	 * @return HTMLText
	 */
	public function Carousel()
	{
		self::InitCarousel();
		return $this->owner->renderWith('Carousel');
	}
	
	/**
	 * Handles all CSS and JS requirements for the carousel and initializes every carousel element on the page. No matter
	 * where you call this - anywhere in you template or PHP code. Or if you use $Carousel in yor template, this gets
	 * called automatically. Multiple calls do no harm.
	 */
	public function InitCarousel()
	{
		Carousel::Requirements();
	}
	
}
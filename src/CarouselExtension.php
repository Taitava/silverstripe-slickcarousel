<?php

namespace Taitava\SlickCarousel;

use SilverStripe\CMS\Model\SiteTree;
use SilverStripe\Core\ClassInfo;
use SilverStripe\Core\Injector\Injector;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\GridField\GridField;
use SilverStripe\Forms\GridField\GridFieldConfig_RelationEditor;
use SilverStripe\Forms\Tab;
use SilverStripe\ORM\DataExtension;
use SilverStripe\ORM\DataList;
use SilverStripe\ORM\FieldType\DBHTMLText;

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
		'CarouselSlides' => CarouselSlide::class,
	);
	
	public function updateCMSFields(FieldList $fields)
	{
		$fields->addFieldToTab('Root', new Tab('Carousel', _t('Taitava\SlickCarousel\Carousel.CMSTabName', 'Carousel')));
		
		$gridfield_config = new GridFieldConfig_RelationEditor(Carousel::config()->get('cms_slides_per_page'));
		$sortable_rows_class = 'UndefinedOffset\SortableGridField\Forms\GridFieldSortableRows';
		if (ClassInfo::exists($sortable_rows_class))
		{
			//The SortableGridField module is installed - which is nice :)
			//See: https://github.com/UndefinedOffset/SortableGridField
			$gridfield_config->addComponent(Injector::inst()->create($sortable_rows_class,['Sort']));
		}
		
		$gridfield = new GridField('CarouselSlides', _t('Taitava\SlickCarousel\Carousel.CMSTabName', 'Carousel'), $this->owner->CarouselSlides(), $gridfield_config);
		$fields->addFieldToTab('Root.Carousel', $gridfield);
	}
	
	/**
	 * Renders the carousel using Carousel.ss (which will lead to include CarouselSlide.ss for each slide). If you wish
	 * to implement some custom iteration for the carousel slides, you can call $CarouselSlides in your template instead
	 * of this method.
	 *
	 * @return DBHTMLText
	 */
	public function Carousel()
	{
		self::InitCarousel();
		return $this->owner->renderWith('Taitava\SlickCarousel\Carousel');
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
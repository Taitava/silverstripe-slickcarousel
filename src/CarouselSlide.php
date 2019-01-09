<?php

namespace Taitava\SlickCarousel;

use SilverStripe\Assets\Image;
use SilverStripe\CMS\Model\SiteTree;
use SilverStripe\Core\Convert;
use SilverStripe\Forms\CheckboxField;
use SilverStripe\Forms\OptionsetField;
use SilverStripe\ORM\DataObject;

/**
 * Class CarouselSlide
 *
 * @method SiteTree ContainerPage Returns a SiteTree object in which this slide belongs to. This is mandatory.
 * @method SiteTree LinkPage Returns a SiteTree object used as a clickable link target in the frontend. This can be null.
 * @method Image Image
 *
 * @property int ImageID
 * @property int $ContainerPageID A SiteTree ID to which this slide belongs to.
 * @property string $Content
 * @property int $LinkPageID A SiteTree ID used as a clickable link target in the frontend.
 * @property string $LinkURL A free form link URL not restricted to an internal page on the same website.
 * @property boolean $LinkTargetBlank Whether or not to open the link in a new tab.
 * @property int $Sort Used if the SortableGridField module is installed. Otherwise this is always zero.
 */
class CarouselSlide extends DataObject
{
	private static $table_name = 'CarouselSlide';
	
	private static $db = [
		'Content'		=> 'HTMLText',
		'LinkURL'		=> 'Varchar(255)',
		'LinkTargetBlank'	=> 'Boolean',
		'Sort'			=> 'Int',
	];
	
	private static $has_one = [
		'Image' 		=> Image::class,
		'LinkPage'		=> SiteTree::class,
		'ContainerPage'		=> SiteTree::class, //This must become AFTER 'LinkPage'! Otherwise the GridField defined in CarouselExtension::updateCMSFields() will incorrectly use 'LinkPage' for the relation instead of 'ContainerPage'.
	];
	
	public function fieldLabels($includerelations = true)
	{
		$labels = parent::fieldLabels($includerelations);
		
		return array_merge($labels, [
			'Content'		=> _t('Taitava\SlickCarousel\CarouselSlide.Content', 'Custom content'),
			'PlainContent'		=> _t('Taitava\SlickCarousel\CarouselSlide.Content', 'Custom content'),
			'Image'			=> _t('Taitava\SlickCarousel\CarouselSlide.Image', 'Image'),
			'Image.CMSThumbnail'	=> _t('Taitava\SlickCarousel\CarouselSlide.Image', 'Image'),
			'Link'			=> _t('Taitava\SlickCarousel\CarouselSlide.Link', 'Link'),
			'LinkURL'		=> _t('Taitava\SlickCarousel\CarouselSlide.LinkURL', 'Link URL'),
			'LinkPageID'		=> _t('Taitava\SlickCarousel\CarouselSlide.LinkPageID', 'Link page'),
			'LinkTargetBlank'	=> _t('Taitava\SlickCarousel\CarouselSlide.LinkTargetBlank', 'Open the link in a new tab'),
		]);
	}
	
	private static $summary_fields = [
		'Image.CMSThumbnail',
		'PlainContent',
		'Link',
	];
	
	private static $casting = [
		'StyleAttribute' => 'HTMLText',
	];
	
	private static $searchable_fields = [];
	
	private static $default_sort = 'Sort ASC';
	
	/**
	 * @return string The URL of the link or '' (an empty string) if no link is defined for this CarouselSlide.
	 */
	public function Link()
	{
		if ($this->LinkPageID > 0) return $this->LinkPage()->Link();
		return $this->LinkURL ?: '';
	}
	
	/**
	 * Used in summary fields.
	 *
	 * @return string
	 */
	public function PlainContent()
	{
		return strip_tags($this->Content);
	}
	
	public function forTemplate()
	{
		return $this->renderWith('Taitava\SlickCarousel\CarouselSlide');
	}
	
	public function getCMSFields()
	{
		$fields = parent::getCMSFields();
		
		//Remove unnecessary fields
		$fields->removeByName([
			'Sort',
			'ContainerPageID',
			'FileTracking',
			'LinkTracking',
		]);
		
		//HTML content field
		$custom_content_used = '' != $this->Content;
		$fields->addFieldToTab('Root.Main', $checkbox = new CheckboxField('UseCustomContent', _t('Taitava\SlickCarousel\CarouselSlide.UseCustomContent', 'Use custom content'), $custom_content_used));
		$checkbox->setDescription(_t('Taitava\SlickCarousel\CarouselSlide.UseCustomContentDescription','Use this to input any kind of HTML content inside the slide. This can be used alongside with an image, or you can leave out the image and use only the custom content.'));
		$fields->dataFieldByName('Content')->displayIf('UseCustomContent')->isChecked();
		$checkbox->addExtraClass('no-change-track'); //Do not alert about 'unsaved work' if the only change in the editor was to check/uncheck this field.
		
		//Link fields
		$current_link_type = ($this->LinkPageID ? 1 : ($this->LinkURL ? 2 : 0));
		$link_options = [
			0 => _t('Taitava\SlickCarousel\CarouselSlide.LinkTypeNoLink',	'No link'),
			1 => _t('Taitava\SlickCarousel\CarouselSlide.LinkTypePage',	'Link to a page'),
			2 => _t('Taitava\SlickCarousel\CarouselSlide.LinkTypeURL',	'Link to a custom URL'),
		];
		$fields->addFieldToTab('Root.Main', $link_option_group = new OptionsetField('LinkType', _t('Taitava\SlickCarousel\CarouselSlide.LinkType', 'Link'), $link_options, $current_link_type));
		$fields->dataFieldByName('LinkURL')->displayIf('LinkType')->isEqualTo(2);
		$fields->dataFieldByName('LinkPageID')->displayIf('LinkType')->isEqualTo(1);
		$fields->dataFieldByName('LinkTargetBlank')->displayIf('LinkType')->isNotEqualTo(0);
		
		//Refine field order
		$fields->changeFieldOrder([
			'Image',
			'UseCustomContent',
			'Content',
			'LinkType',
			'LinkURL',
			'LinkPageID',
			'LinkTargetBlank',
		]);
		
		//Fix field translations (I don't know why SilverStripe does not translate them without this)
		$fix_field_titles = ['Image', 'Content', 'LinkURL', 'LinkPageID', 'LinkTargetBlank'];
		foreach ($fix_field_titles as $field_name)
		{
			$fields->dataFieldByName($field_name)->setTitle($this->fieldLabel($field_name));
		}
		
		return $fields;
	}
	
	
	/**
	 * Generates the content for the slide's <div style=""> attribute. Does not return the 'style=""' part, only the
	 * value. This way more style rules can be added in the template after or before these rules.
	 *
	 * @return string
	 */
	public function StyleAttribute()
	{
		$styles = [];
		if (Carousel::config()->get('image_placement') == 'background') $styles[] = "background-image: url('".Convert::raw2xml($this->Image()->Link())."');";
		if (Carousel::UseImageWidth()) $styles[] = 'width: '.$this->Image()->getWidth().'px;';
		if (Carousel::UseImageHeight()) $styles[] = 'height: '.$this->Image()->getHeight().'px;';
		return implode(' ', $styles);
	}
	
	public function ImagePlacement()
	{
		return Carousel::config()->get('image_placement');
	}
	
}
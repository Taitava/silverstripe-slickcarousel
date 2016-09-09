<?php

/**
 * Class CarouselSlide
 *
 * @method SiteTree ContainerPage Returns a SiteTree object in which this slide belongs to. This is mandatory.
 * @method SiteTree LinkPage Returns a SiteTree object used as a clickable link target in the frontend. This can be null.
 *
 * @property int $ContainerPageID A SiteTree ID to which this slide belongs to.
 * @property string $Content
 * @property int $LinkPageID A SiteTree ID used as a clickable link target in the frontend.
 * @property string $LinkURL A free form link URL not restricted to an internal page on the same website.
 * @property boolean $LinkTargetBlank Whether or not to open the link in a new tab.
 * @property int $Sort Used if the SortableGridField module is installed. Otherwise this is always zero.
 */
class CarouselSlide extends DataObject
{
	private static $db = array(
		'Content'		=> 'HTMLText',
		'LinkURL'		=> 'Varchar(255)',
		'LinkTargetBlank'	=> 'Boolean',
		'Sort'			=> 'Int',
	);
	
	private static $has_one = array(
		'Image' 		=> 'Image',
		'LinkPage'		=> 'SiteTree',
		'ContainerPage'		=> 'SiteTree', //This must become AFTER 'LinkPage'! Otherwise the GridField defined in CarouselExtension::updateCMSFields() will incorrectly use 'LinkPage' for the relation instead of 'ContainerPage'.
	);
	
	public function fieldLabels($includerelations = true)
	{
		$labels = parent::fieldLabels($includerelations);
		
		return $labels + array(
			'Content'		=> _t('CarouselSlide.Content', 'Custom content'),
			'PlainContent'		=> _t('CarouselSlide.Content', 'Custom content'),
			'Image'			=> _t('CarouselSlide.Image', 'Image'),
			'Image.CMSThumbnail'	=> _t('CarouselSlide.Image', 'Image'),
			'Link'			=> _t('CarouselSlide.Link', 'Link'),
			'LinkURL'		=> _t('CarouselSlide.LinkURL', 'Link URL'),
			'LinkPageID'		=> _t('CarouselSlide.LinkPageID', 'Link page'),
			'LinkTargetBlank'	=> _t('CarouselSlide.LinkTargetBlank', 'Open the link in a new tab'),
		);
	}
	
	private static $summary_fields = array(
		'Image.CMSThumbnail',
		'PlainContent',
		'Link',
	);
	
	private static $searchable_fields = array();
	
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
		return $this->renderWith('CarouselSlide');
	}
	
	public function getCMSFields()
	{
		$fields = parent::getCMSFields();
		
		//Remove unnecessary fields
		$fields->removeByName(array(
			'Sort',
			'ContainerPageID',
		));
		
		//HTML content field
		$fields->addFieldToTab('Root.Main', $checkbox = new CheckboxField('UseCustomContent', _t('CarouselSlide.UseCustomContent', 'Use custom content')));
		$checkbox->setDescription(_t('CarouselSlide.UseCustomContentDescription','Use this to input any kind of HTML content inside the slide. This can be used alongside with an image, or you can leave out the image and use only the custom content.'));
		$fields->dataFieldByName('Content')->displayIf('UseCustomContent')->isChecked();
		
		//Link fields
		$current_link_type = ($this->owner->LinkPageID ? 1 : ($this->owner->LinkURL ? 2 : 0));
		$link_options = array(
			0 => _t('CarouselSlide.LinkTypeNoLink',	'No link'),
			1 => _t('CarouselSlide.LinkTypePage',	'Link to a page'),
			2 => _t('CarouselSlide.LinkTypeURL',	'Link to a custom URL'),
		);
		$fields->addFieldToTab('Root.Main', $link_option_group = new OptionsetField('LinkType', _t('CarouselSlide.LinkType', 'Link'), $link_options, $current_link_type));
		$fields->dataFieldByName('LinkURL')->displayIf('LinkType')->isEqualTo(2);
		$fields->dataFieldByName('LinkPageID')->displayIf('LinkType')->isEqualTo(1);
		$fields->dataFieldByName('LinkTargetBlank')->displayIf('LinkType')->isNotEqualTo(0);
		
		//Refine field order
		$fields->changeFieldOrder(array(
			'Image',
			'UseCustomContent',
			'Content',
			'LinkType',
			'LinkURL',
			'LinkPageID',
			'LinkTargetBlank',
		));
		
		//Fix field translations (I don't know why SilverStripe does not translate them without this)
		$fix_field_titles = array('Image', 'Content', 'LinkURL', 'LinkPageID', 'LinkTargetBlank');
		foreach ($fix_field_titles as $field_name)
		{
			$fields->dataFieldByName($field_name)->setTitle($this->fieldLabel($field_name));
		}
		
		return $fields;
	}
}
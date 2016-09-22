# silverstripe-slickcarousel
A Slick carousel wrapper for SilverStripe. See: http://kenwheeler.github.io/slick/

THE README IS COMING! If you need it, please raise an issue about the readme, and I will hurry with it. But don't be afraid, the module is quite easy to use. The simplest setup contains just a few steps:

1. Extend your Page class with CarouselExtension (or SiteTree class or any other class derived from SiteTree).
2. Put $Carousel inside your Page.ss (or similar) layout template.
3. Run /dev/build?flush=all
4. Go to the CMS and go to edit some page and look for the new Carousel tab.

Slick carousel options can be configured via YAML:

```YAML
Carousel:
  slick_options:
    autoplay: true
```

The autoplay option is just an example. For a complete list of available options, please see: http://kenwheeler.github.io/slick/#settings

The module is still under development, so I don't consider it stable yet. However, Slick is stable so there's actually not much that can break, as the wrapper is quite simple! :)


## Optional settings

You can tweak these settings in `mysite/_config/slickcarousel.yml`. Here are the options are listed with default values.

```
Carousel:
  cms_slides_per_page: 50        #How many slides to list in a single page in the Carousel tab in the CMS.
  
  image_placement: background    #Where to place the slide's image inside the carousel slide <div> element:
	                         # - 'background': the image will be used as the <div>'s background-image.
	                         # - 'before-content': the image will be used as an <img> element before the HTML contained in the Content field.
	                         # - 'after-content': the image will be used as an <img> element after the HTML contained in the Content field.
	
  use_image_dimensions: false    # Whether or not to set the slide <div> element's width and/or height to be the same as the image's width and/or
	                         # height. Can be useful when $image_placement is 'background' and you are not using constant dimensions that you
	                         # define in your CSS. Possible values:
	                         # - false
	                         # - 'width-only'
	                         # - 'height-only'
	                         # - true (both width and height)
	                         
  slick_options:                 # Options to pass to the Slick jQuery plugin during initialization. With these you can greatly affect the behaviour
    (an empty array by default   # of the carousel in the frontend.
    so all Slick's default       # This array can contain any option that is mentioned here: http://kenwheeler.github.io/slick/#settings
    values apply by default)     # The options are passed as-is, so if new versions of Slick define new options, no PHP code modifications are needed.
	                         # You just need to manually upgrade the included Slick JavaScript library.
	
CarouselSlide:
  default_sort: Sort             #The default sort order is the order how the slides are drag&dropped in the CMS.
                                 #If you want to randomize the order, set this to 'RAND()'.
```
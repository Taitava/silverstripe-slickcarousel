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

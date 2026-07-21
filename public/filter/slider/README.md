Slider Filter
=========================
This filter allows You to include slider from block_slider anywhere in Moodle content.


Release notes
=========================

* 1.2 Fixed issues:
    https://github.com/limsko/moodle-filter_slider/issues/8
* 1.1 Fixed issues:
    https://github.com/limsko/moodle-filter_slider/issues/3
    https://github.com/limsko/moodle-filter_slider/issues/5
    https://github.com/limsko/moodle-filter_slider/issues/6
* 1.0.1 Fixed missing block_base class during page edit.
* 1.0 This is first release of this filter.


Requirements
=========================
First You have to install block Slider version 0.2 available at https://moodle.org/plugins/block_slider


Installation
=========================
Install the plugin like any other plugin to folder /filter/slider

If You're installing with Plugin installer using zip archive choose plugin type: Text filter (filter)

See http://docs.moodle.org/en/Installing_plugins for details on installing Moodle plugins


Usage
=========================
First install the filter_slider plugin, then activate the filter_slider plugin in Site Administration -> Plugins -> Filters -> Manage filters
Use this shortcode `[SLIDER-##]` - where ## put sliderid. The shortcode is displayed above the slideshow table 
when managing slides in the Slider block. 

Shortcode should look like this: `[SLIDER-25]`

The shortcode "[SLIDER-25]" will be replaced with slider content. Block instance should be created on other page
than the filter will be used on. Users must have capabilities of displaying the block for the filter to load it.

Filter is ignoring visibility of slider, so if block is hidden the slider still appers in place of shortcode.

Settings
=========================
There is no settings for this plugin.

Copyright
=========================
Written by Kamil Łuczak
kamil@limsko.pl
https://www.limsko.pl
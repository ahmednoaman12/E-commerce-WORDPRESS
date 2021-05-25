=== Dara ===

Tags: business, blog, bright, modern, professional, blue, light, one-column, two-columns, right-sidebar, author-bio, blog-excerpts, classic-menu, custom-background, custom-colors, custom-header, custom-menu, featured-content-with-pages, featured-images, flexible-header, full-width-template, infinite-scroll, post-slider, rtl-language-support, site-logo, social-menu, testimonials, theme-options, threaded-comments, translation-ready

Requires at least: 4.5
Tested up to: 4.7
Stable tag: 1.2.0-wpcom
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

== Description ==

With bold featured images and bright, cheerful colors, Dara is ready to get to work for your business.

== Installation ==

1. In your admin panel, go to Appearance > Themes and click the Add New button.
2. Click Upload and Choose File, then select the theme's .zip file. Click Install Now.
3. Click Activate to use your new theme right away.

== Frequently Asked Questions ==

= Does this theme support any plugins? =

Dara includes support for Jetpack's Infinite Scroll, Social Menus, Featured Content, and Testimonials.

== Setting Up Your Front Page ==

When you first activate Dara, your homepage will display posts in a traditional blog format unlike the demo site at http://darademo.wordpress.com, which uses a static front page. If you would like to copy the look of the demo site, follow these steps:

* Publish two pages with titles that are easy to remember, like Home and Blog. To publish a page, navigate to Pages → Add Page.
* Go to Customize → Static Front Page.
* Select "A static page" and choose the two pages you published in Step #1 as your Front page and Posts page.

The static front page includes the following:

* A Featured Content area with a slider.
* Three Featured Pages, set in Customize → Theme Options.
* The Testimonial area, configured under Customize → Testimonials. This area displays two testimonials. To add a testimonial, go to Testimonials → Add. If no testimonials are added, this section will not be displayed.

== Featured Content Slider (requires Jetpack: jetpack.me) ==

The Featured Content slider appears on the front of your site. To add a post or page to the slider, give it the tag featured or another tag of your choosing set from Customize → Featured Content.

If no Featured Content tag is set, the Featured Image for the page will display here instead.

== Front-page Featured Pages ==

On the theme demo (http://darademo.wordpress.com), this section is populated with three Featured Pages, each with a title, Featured Image, and excerpt. To re-create the same look, follow these directions:

* Publish the three pages you would like to feature on your front page, and upload Featured Images for each page.
* Go to Customizer → Theme Options.
* Use the three drop-down menus to select the pages you'd like to feature.
* Click Save and Publish.

The pages featured in this area will automatically occupy the entire width of your site. For example, a single featured page will occupy the entire column, two Featured Pages will divide the space into two columns, three Featured Pages will divide the space into three columns.

== Custom Page Templates ==

== Full-Width Template ==

The Full-Width template gives you more space for your content. It’s the perfect way to showcase a gallery of images or a video, Don't forget to take advantage of the beautiful full-width Featured Image, too. To use the Full Width template, select it under Page Options while editing a Page.

== Grid Page Template ==

The Grid Page template is designed to show child pages in a grid format. To get started, first create or edit a page, and assign it to the Grid Page template from the Page Options panel. The content of this page and Featured Image – if one is set – will be displayed above the grid.

To add items to the grid, create additional pages and use the Page Options box to set the grid page you just created as their parent page. Be sure to set a Featured Image for each child page if you want an image to show up inside the grid. Repeat these steps for every item you want to display in the grid.

== Custom Menus ==

Dara includes one Custom Menu in the header, which can be configured via My Sites → Menus. If no menu is assigned, this area will display a list of your pages by default.

== Social Links Menu (requires Jetpack: jetpack.com) ==

With Dara, you have the option to display links to your social media profiles in the header and footer. To display them, set up a Social Links Menu.

== Widget Areas ==

Dara offers four widget areas:

* The optional Sidebar, which appears on the right in blog view and on pages using the default template.
* Three optional Footer widget areas.

If the Sidebar area is not active, the theme automatically adjusts to have a single column.

== Site Logo ==

Brand identity is very important — that’s why Dara supports the Site Logo feature. To add your own image, go to Customize → Site Identity. The logo will appear in the header, above the site title.

== Testimonials (requires Jetpack: jetpack.me) ==

Dara features testimonials in two ways:

* The dedicated Testimonial Archive Page displays all testimonials in reverse chronological order, with the newest displayed first.
* The static front page displays the newest two testimonials.

To add a testimonial, go to Testimonials → New. Testimonials are composed of the testimonial text, the name of the customer (added as testimonial title), and an image or logo (which can be added as a Featured Image).

== Testimonial Archive Page ==

All testimonials are displayed on the testimonial archive page. This page can be added to a Custom Menu using the Links Panel.

The testimonial archive page can be found at http://mygroovysite.wordpress.com/testimonial/ — just replace http://mygroovysite.wordpress.com/ with the URL of your website.

This page can be further customized by adding a title, intro text, and Featured Image via Customize → Testimonials. This content will appear above the testimonials list.

== Custom Background ==

Custom color, pattern, or a beautiful image – the choice is yours. To change the background, visit Customize → Colors & Backgrounds from your dashboard.

== Quick Specs (all measurements in pixels) ==

* The main column width is 675 except in the full-width layout where it’s 778.
* A widget in the sidebar is 250.
* Footer widget widths vary depending on the number of active widget areas.
* The Featured Image on the front page and on pages works best with images at least 1180 wide.
* Featured Images for posts should be at least 880 wide by 312 tall.

== Credits ==

* Based on https://github.com/Automattic/theme-components/, (C) 2015-2017 Automattic, Inc., [GPLv2 or later](https://www.gnu.org/licenses/gpl-2.0.html)
* normalize.css http://necolas.github.io/normalize.css/, (C) 2012-2017 Nicolas Gallagher and Jonathan Neal, [MIT](http://opensource.org/licenses/MIT)
* Image used in screenshot is from Pexels (https://www.pexels.com/photo/apple-iphone-books-desk-232/) licensed CC0
* Bundled font, Genericons, from http://github.com/Automattic/genericons/ licensed GNU GPL v2 (https://github.com/Automattic/Genericons/blob/master/LICENSE.txt)


== Changelog ==

= 2 March 2018 =
* Use wp_kses_post rather than wp_filter_post_kses.

= 21 February 2018 =
* Fix the incorrect WooCommerce image sizes
* Define WooCommerce image sizes

= 25 January 2018 =
* Fix slider arrow directions when using RTL.

= 15 January 2018 =
* Simplify Headstart annotations for all themes in signup.

= 6 December 2017 =
* Manually update dara es translation files
* Manually update dara.pot
* Temporarily remove style comment to fix translations

= 4 October 2017 =
* Adds full WooCommerce integration.

= 3 October 2017 =
* Update version number in preparation for .org submission.

= 5 September 2017 =
* Filter content width for Jetpack gallery widgets to accommodate full-width footer areas.

= 31 August 2017 =
* Adjust content width when viewing the front page
* Fix content width on the front page when there is no sidebar.

= 28 August 2017 =
* Hide overflow on narrow screens so the slider contents don't push content down, creating unnecessary whitespace.

= 29 June 2017 =
* Adding excerpt support for pages.

= 23 June 2017 =
* Removes social-icon widget borders and centers icons for both footer and sidebar widget areas
* Fixing JetPack Social icon widget display, removes borders, and centers icons

= 8 June 2017 =
* Add JavaScript to fix aspect ratio issue in Video Widget. Add styles to fix list, too-long text issues in Text Widget.

= 5 June 2017 =
* Remove border-top of the list item if the widget is the Social Icons Widget.

= 26 May 2017 =
* Fix full width page separator size on large screens.

= 22 May 2017 =
* Improve contrast for menu-toggle.

= 27 April 2017 =
* Add support for Smarter Featured Images, off by default.

= 24 April 2017 =
* Update version number in preparation for .org submission
* Move flush rewrite rules function to wpcom.php to avoid core requirement that the theme not try to add plugin functionality.
* Ensure front page template displays comments if available/enabled.
* Don't affect excerpt_more filter on the admin side.
* Update footer credit link to point to wordpress.com/themes for consistency with Author URI in style.css
* Update Custom Header implementation to avoid use of constant in favor of add_theme_support( 'custom-header' )
* Update version number in preparation for .org submission; fix typo in Author URI

= 19 April 2017 =
* Update WooCommerce support to 3.0

= 22 March 2017 =
* add Custom Colors annotations directly to the theme
* move fonts annotations directly into the theme

= 21 March 2017 =
* Update version number in preparation for .org submission
* Update incorrect text domain in functions.php
* Add license info for Genericons and update version number in preparation for submission to .org
* Update version number in preparation for submission to .org
* Make sure Infinite Scroll works properly with the Testimonials archive type.
* Hide slider navigation controls when the slider only has one post.
* Make sure the featured post slider appears even if there is only one post.
* Add Continue reading link to replace the WordPress default ellipsis;

= 23 February 2017 =
* Update readme
* Fix Full-Width Page Template margin-bottom.
* Set max-width for custom-logo-link, not custom-logo.

= 22 February 2017 =
* Update gallery style with inline-block instead of float.

= 21 February 2017 =
* Add Javascript to allow submenu items to be accessed from touch devices.
* Prepare for core custom logo support.

= 15 February 2017 =
* Add more robust WooCommerce support
* Center Testimonials archive when no featured image or sidebar is present.
* Fix centering of pages without Featured Images when no sidebar is active Fixes #4351 ; fix centering of Testimonial entry titles when no sidebar and featured image are present.

= 2 February 2017 =
* Replace get_the_tag_list() with the_tags() for a more straightforward approach that prevents potential fatal errors.
* Ensure hero area for front page is only displayed if the front page is a page, not the list of posts.

= 1 February 2017 =
* Begin adding support for WooCommerce.
* Don't reload slider on resize; this was preventing it from starting with the correct height on load.

= 30 January 2017 =
* Add full readme.txt
* Update credits with correct Author URI and Theme URI
* Update theme description

= 27 January 2017 =
* Add .button class for links
* add screenshot
* Ensure height of slider stays smooth as screen is resized
* Adjust min-height for narrower screens again.
* Adjust min-height of slider for mobile devices.
* Add webkit styles for buttons
* More
* More
* Fix menu arrow direction for RTL styles
* Add POT file;
* Initial RTL styles.
* Additional
* Error 404 page with no sidebar has misaligned title; this removes the extra padding.
* Make sure front page, grid page, and full-width pages are wider when no sidebar is active.
* Better styles for authors widget.
* Author grid widget
* Stack post navigation links on small screens
* Give the submenus a bit more width to accomodate longer menu item titles.
* Fix bug with submenus covering each other
* Begin styling 404 page.
* Add some basic widget styles.
* Add themecolors and begin with style-wpcom.css for styling WP.com widgets and features.
* Use strict comparisons when necessary
* Remove unused function
* Escape attributes in page templates.
* Whitespace
* Whitespace
* Make sure to escape comment headings before output.

= 26 January 2017 =
* Remove unused sidebar template from theme.

= 25 January 2017 =
* Reduce left and right spacing on footer widget area for mobile devices
* Make sure full-width templates actually display full width. Add bottom margin to Testimonials entry title.
* Slightly more space between sidebar and content on medium-sized screens.
* Further simplification of media query breakpoints.
* Simplify breakpoints, make sure sidebar stays present on blog
* Begin simplifying media queries to reduce the number of breakpoints and make the behavior on breakpoint change more consistent across page/archive types.
* More even spacing for category links on mobile.
* More font size adjustments and spacing updates for mobile devices.
* Fix incorrect class on grid page template.
* Better font sizes for mobile devices; improved spacing/styles for Grid page template on mobile.
* Change grid page spacing and breakpoint to be a little less extreme
* Fix for too much padding on featured page area for mobile.
* Only display featured content slider on the front page (whether blog or static) not both blog and front page.

= 24 January 2017 =
* Adjust custom logo size and max-width/height; add custom logo support to HS annotation
* Styles for jetpack portfolio shortcode

= 19 January 2017 =
* Make sure spacing isn't outrageous for search results when pages/other post types are found.
* Simpler, more elegant solution for posts without categories but with a featured image.

= 17 January 2017 =
* Ensure site description stays light gray and that Customizer JS reflects correct color.
* Correct site title color.
* Make bullets between main navigation items a little bit more obvious.
* Add :focus and :active styles to links; standardize link transitions and make them specific to prevent conflicts.
* Spacing adjustments; remove unused styles
* Styles for Custom Headers and Custom Logos; add Headstart annotations; spacing and

= 16 January 2017 =
* Add smarter pingback LINK, hooked into wp_head rather than hard-coded into header.php
* Better heading title format for translation on comments heading.
* Add support for selective refresh of Customizer widgets.
* More consistent styles for edit links in the comments area
* Updated skip link focus fix; remove aria-haspopup from child menus.
* Use SVG social link menu icons.

= 13 January 2017 =
* Fix appearance of search-no-results template.
* Minor whitespace fixes; add orderby option parameter to front page; retina-ize Testimonial thumbnails.
* Add theme option for randomizing Testimonials on front page; make sure search results display correctly for non-post types; ensure widgets can still be activated on mobile devices when infinite scrolling.
* Lots of spacing/style fixes for search results, testimonials archive, front page, and no-sidebar case.

= 12 January 2017 =
* Fix footer widget area margins on home page on large screen
* Spacing improvements for small screens
* Hover transitions on featured content slider post info.
* Clean up mobile menu styling
* Make sure testimonials edit links are styled on mobile devices; adjust footer widget area margins for home page template only.
* Testimonials archive styles.
* Ensure mobile menu icon shows up properly; fix hero area on full-width page template.
* Style grid page template

= 11 January 2017 =
* Only display post meta on posts in the slider; adjustments to spacing/whitespace in style.css
* Fix footer widget areas on mobile screens to reduce padding
* cleaner mobile styles for the slider; delete unused stylesheet starter files in /assets, merge flexslider CSS into style.css-This line, and those below, will be ignored--
* Add more post information to slider, style slider posts

= 10 January 2017 =
* Experimenting with slider styling...not liking what I've done so far.

= 21 December 2016 =
* Add Genericons support; begin styling slider.

= 20 December 2016 =
* Initial Flexslider implementation in the Featured Content area.

= 15 December 2016 =
* Add support for featured pages from Theme Options on the front page instead of the widget areas.
* Make social icons a little bigger; remove widget areas from front page in favor of featured pages from Theme Options TBD; use blue instead of salmon for main color to better differentiate from Sela.
* Increase font sizes/padding on main navigation for easier targeting; add social links to the header; add lighter background to footer area
* Oops, remove test code.
* More styling for Testimonials archive page.
* Begin styling testimonials archive type.
* Better styles for no-sidebar blog and archives featured images.

= 9 December 2016 =
* Remove extra space after author on smaller screens
* Make sure search results match other archives; account for lack of category visiblity when using Content Options
* Make overall site area wider for more content width; adjust content width for featured images.
* Margin adjustment for footer widget area on smaller screens
* Style tweaks for category links, page titles, and author bio--This line, and those below, will be ignored--
* Styles for comment meta data and calendar widget.--This line, and those below, will be ignored--
* Begin styling comments
* Adjustments to header padding; styling tweaks for fonts in author bio.
* Lots of adjustments to spacing; styles for author bio
* Fix main navigation submenu alignment when no bullet divider is present; bananas logic for figuring out when Featured Images are present and Content Options are active/inactive.
* Add full support for Content Options with Author Bio and Featured Images; styling for tablet-sized screens.

= 7 December 2016 =
* more accounting for no sidebar scenario
* Fix blockquote styles
* Account for no sidebar active in the design.
* Initial commit to /pub

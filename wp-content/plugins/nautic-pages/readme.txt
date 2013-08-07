=== Nautic Pages ===
Contributors: Stur
Donate link: http://ili.com.ua/php/nautic-pages
Tags: widget, pages
Requires at least: 1.0.1
Stable tag: 1.0.1

Nautic widget for the web-pages displaying.
Wordpress is easy to use as simplest CMS for creation of small sites - “business card”. Easy installing, setting and friendly editor allows to administer web-site to ordinary user who don’t know HTML. Many similar sites include 10 or more static pages and column of news.
Standard widget always displays all present pages, it can’t hide the hierarchy of inactive elements and as result we get long detailed list which not conform into a general design.

    * Hides inactive hierarchy if we are on “page-1” and shows one sublevel:
      page-1.1, page-1.2, page-1.3, page-1.4 all elements of the first level. The sublevel of “page-3” and sublevel of “page-1.2” will not be displayed.
    * Allows to exclude unnecessary pages, it’s necessary to create the list of IDs of pages divided by comma in the settings of widget, for example: 1,2,3
    * To set root page which will displayed menu, for example if to specify ID of “page-2” we will get such list: page-2.1, page-2.2, page-2.3, page-2.4
    * Depth - this parameter is set level which starting our tree. For example if depth = 1 and we are on “page-2” widget will not displayed. But if to go to “page-1” it will be show only sublevel: page-1.1, page-1.2, page-1.3, page-1.4
      and inactive submenu: page-1.2.1, page-1.2.2, page-1.2.3, page-1.2.4 -
      not displayed. This option easy to use for constructing of difficult menus for example when zero-level of pages is displayed horizontally, but sublevels vertically in sidebar.
    * Option - to show one level(hide submenu).
    * To show the amount of daughter’s pages – clearly.
    * Show current page as link or text of heading.
    * This widget multiple.
    * CSS-class for every point and submenu formed in accordance with standard of Wordpress

Bread crumbs.

Additionally to widget there are 3 functions which can be use in template :

function nautic_pages_path($args) display path of current pages: “page-1>page-1.2>page-1.2.3” in $args it is possible to set 2 parameters:

’separator’ => ‘>’ it is page break and

’show_latest’ => to show main page or not.

function nautic_pages_next($sortby = ‘post_title’) displays next page in current level.

function nautic_pages_previous($sortby = ‘post_title’) displays previous page in current level.

! For “next” and “previous” it is necessary to specify sorting order the same as in your widget.

Values can be: post_title, menu_order, ID.

Example:

if( function_exists( 'nautic_pages_path' ))
echo nautic_pages_path( array( 'separator' => '>', 'show_latest' => true));

or

if( function_exists( 'nautic_pages_previous' ))
echo nautic_pages_previous( $sortby );
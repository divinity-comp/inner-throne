=== JPG, PNG Compression and Optimization ===
Contributors: pigeonhut, optimisation.io
Tags: GEO TAG IMAGES, PNG Images, Compress JPG Image, Image Optimisation, Image Optimization, Image compression, small, Resize Images, Images, Compression, Optimization.
Requires at least: 4.3
Tested up to: 5.0
Stable tag: 1.7.35
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Keep JPG & PNG Images Optimized & Compressed, <strong>GEO Locate and Geo Tag images</strong>, upto 1000 free images per month per user. Supports PHP 7.
Should also help with SEO and UX due to faster page loads, Potential to help with bandwidth costs as well due to smaller images. Built in free CDN (free during extended Beta)

== Description ==

<font colour="blue"><strong>Coming SOON: Automatically create WebP versions of images for even better compression</br>
Free Image Only CDN</br>
Coming SOON: On The Fly Image Manipulation via URL strings </strong></br></font>

Better SEO and user conversion by using smaller JPG and PNG images - Optimize your JPG and PNG Images with Image Compression upto 80%.
Smaller images, faster page loads, lower cost  - Also resize images on upload to help reduce storage and bandwidth before compressing.

<strong><font colour="blue">NEW -- </strong></font>
Added the ability to Geo Tag images so now you can geo locate all the image data on your website for absolute granular control over your location based SEO. No need for third party tools, you can do it direct from the media section.

<strong>Features:</strong></br>
Average Image Compression of 65% with little to no loss of image quality - a quick and easy way to optimize your WordPress Image Library.
Automated resizing of images to a fixed width/height images. Perfect for stopping clients uploading 4000px images

Coming soon - per image granular control for resizing and compression.

Still struggling? Visit <b><a href="https://optimisation.io">Optimisation.io</a></b> for a no obligation appraisal of your website.

== Installation ==

install via the plugins system in WordPress or manually
1. Upload `plugin-name.php` to the `/wp-content/plugins/` directory
then:
Activate the plugin through the 'Plugins' menu in WordPress

== Frequently asked questions ==

= My images are over 15MB now, will this still work =
Yes, we compress up-to 20MB original image sizes.  We do however recommend resizing the images first.  a 4000px image when compressed is still 4000px, so by resizing you immediately start off at the correst size, which can in some cases be 500% smaller


== Screenshots ==
1. Original 107KB
2. Compressed 82.6KB
3. Original 6000px 5.7MB
4. Resized and Compressed (1024px) 115KB
5  Compressed (still 6000px) 2.66MB

== Changelog ==
= 1.7.35 =
* Fixed display on all Image view
* Fixed Geolocation bug in IMAGES

= 1.7.34 =
* Removed and fixed curl as per WP requirements
* Removed paid image purchases

= 1.7.32 =
* Tested with WP Version 5.0
* Bug fixes with image CDN paths

= 1.7.31 =
Bug fixed in header output

= 1.7.30 =
* Complete re-write of image compression
* Image only CDN coming SOON
* On the Fly Image manipulation coming SOON

= 1.7.21 =
* More fixes on Geo location searching
* Added 1 second delay for search suggestions for a better user experience
* Fixed some bugs with image compression

= 1.7.20 =
* Added autocomplete for location search when geo-tagging images

= 1.7.11 =
* Added fall back to HERE maps for Geo Data if google maps times about

= 1.7.1 =
* Added ability to show GEO data for existing JPG images. Click on Marker icon to view and edit/update.

= 1.7.0 =
* Added ability to GEO Tag Images from within Media

= 1.6.29 =
* Added a note on dashboard about what to look for to disable our plugins.  People have started leaving negative feedback cause can't remember what they installed. Hopefully, this clears it up

= 1.6.28 =
* Lazy load tweaks
* Path issue for css/js
* PHP 7 Compatibility updated


= 1.6.27 =
* Lazy load bug fixes

= 1.6.26 =
* Bug - undefined index 'wpimage_optimumx' removed
* Lazy load issues fixed
* Saving settings fixed
* fixes for 'track load time'

= 1.6.25 =
* Bug fixed

= 1.6.24 =
* Minor edit to compression algo
* PHP 7.2 compatabile

= 1.6.23 =
* Started on documentation
* Added Donate button

= 1.6.22 =
PHP 7.1 compatabile
fixed error showing no availability left when using own API

= 1.6.21 =
* General code cleanup to bring inline with changes on optimiastion suite
* Bug fix that blocked Cloudinary API after 500 images

= 1.6.20 =
* Stats on dashbaord fixed
* Minor improvements

= 1.6.11 =
* Bug fix where images are not uploading on some sites
* Bug fix of stats not showing

= 1.6.1 =
* More visual cleanups
* Removed all webfonts
* Minor bug fix on reporting on dashboard
* All 3 plugins should now be seamless
* General cleanup of WP Repo removing old versions inline with WP guidelines
* Please note, if you use more than one of our Optimisation plugins, they all need to be updated to the latest versions to ensure a seamless working experience

= 1.6.0 =
* New visuals Dashboard
* Tighter integration with the full optimisation.io suite of plugins

= 1.5.1 =
* Navigation tidy up
* Dashboard tidy up

= 1.5.0 =
* Added the ability to use your own Cloudinary API key, giving you upto 75K free transformations/month
* Added the ability to re-compress already compressed images (Backups must be selected for this to work)
* Tighter integration with the rest of the optimisation suite,
* Export settings improved to work with our Cache Plugin, WP Disable and Image compression
* General Code tidy up


== Upgrade Notice ==
de-activate existing plugin
remove from dashboard
activate plugin and re-add domain

== License ==
WP Image Compression  is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.

WP Image Compression  is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.

You should have received a copy of the GNU General Public License along with WP Image Compression. If not, see <http://www.gnu.org/licenses/>.

License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

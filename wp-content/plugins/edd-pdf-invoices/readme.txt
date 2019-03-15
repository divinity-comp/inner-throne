=== Easy Digital Downloads - PDF Invoices ===
Plugin URI: http://easydigitaldownloads.com
Contributors: easydigitaldownloads, sunnyratilal
Requires at least: 4.0
Tested up to: 4.9
Stable Tag: 2.2.23
Requires at least: Easy Digital Downloads 1.7

Dynamically generate PDF Invoices for each purchase made. Also features invoice templates and email templates to match the design of the invoices.

== Description ==

Take your digital store to the next level of professionalism by providing your customers with PDF Invoices.  The add-on comes with 12 beautifully crafted invoice and email templates and they are very easily customisable.  To allow consistency between your invoices and purchase receipts, four email templates have also been provided.

The add-on has also been integrated with the Easy Digital Downloads Software Licensing Add-On making sure that you get updates quickly and easily.

Features of the plugin include:

* Dynamically generate PDF Invoices from each payment
* Integrates with the Payment History [payment_history] shortcode to give users a link to download the invoice
* A template tag can easily show a link to a downloadable invoice in the purchase receipt
* Includes 12 invoice templates and 12 email templates

More information at [Easy Digital Downloads.com](http://easydigitaldownloads.com/).

== Installation ==

1. Activate the plugin
2. Go to Downloads > Settings > Misc and configure the PDF Invoice options
3. Customers can now download invoices with any purchase on the Purchase History page or you can view the Payment History page

== Changelog ==

= 2.2.23 =
* New: Added country selector the PDF Invoices settings and the country is now included on the generated invoice.
* New: Added settings to change the Tax label and invoice heading.
* Fix: Added a comma between the customer city and state.
* Fix: Corrected an issue in the Traditional invoice template that caused overlaps to occur.
* Fix: Updated all invoice templates to use the Payment's currency, instead of the store's.

= 2.2.22 =

* Fix: Payment currency not respected; default shown instead.

= 2.2.21 =
* Use EDD_Payment to get user_info, to avoid nested calls to maybe_unserialize.

= 2.2.20 =
* Chinese characters now display correctly
* Settings have been moved to a separate subsection under Downloads > Extensions
* Add a filter to allow the company name to be filtered
* Display renewal notice on invoices for all license renewals
* Ensure that 'License Renewal Discount' is not displayed for all discounts

= 2.2.19 =
* Update customer name on the invoice when the customer record is updated

= 2.2.18 =
* Display renewal discounts

= 2.2.17 =
* Allow extensions to add additional fields

= 2.2.16 =
* Fix: Use home_url() instead of site_url()

= 2.2.15 =
* Fix: Bug when generating invoice with EDD 2.5
* Fix: Incorrect invoice links being generated

= 2.2.14 =
* FIX: XSS vulnerability in query args

= 2.2.12 =

* Fixed an issue with item prices including tax
* Changed "Total Due" to "Total Paid"

= 2.2.11 =

* Fixed a bug with 0 being displayed if no country was selected
* Display the country name instead of the country code on invoices

= 2.2.10 =

* Restored the callback function for the template tags setting

= 2.2.9 =

* Load the text domain on -1

= 2.2.8 =

* Updated the {invoice} email tag to use the EDD Email Tags API

= 2.2.7 =

* Fixed a bug that caused the download link for invoices for payments with a + sign in the email to return a not found error.

= 2.2.6 =

* Fixed an issue with the Purchase History table for Abandoned purchases

= 2.2.5 =

* Fixed an error with an undefined offset notice

= 2.2.4 =

= 2.2.3 =

* Removed a redundant payment status check

= 2.2.2 =

* Added price option names to invoice line items

= 2.2.1 =

* Fixed a bug that caused invoice links to be shown on pending payments

= 2.2 =

* Fixed a bug with Euro sign encoding
* Added support for sequential order numbers coming in EDD v2.0

= 2.1.13 =

* Fixed a bug with product title encoding

= 2.1.12 =

* Replace unserialize() with maybe_unserialize()

= 2.1.11 =

* Fixed a bug with blank purchase receipts

= 2.1.10 =

* Fixed a fatal error
* Don't show Download Invoice on non-complete purchases

= 2.1.9 =
* Make sure invoice link isn't dislayed for free downloads

= 2.1.8 =
* Fixed a bug with Euro signs encoding.

= 2.1.7 =
* Fixed a bug with Euro signs encoding.

= 2.1.6 =
* Fixed a fatal error bug caused by an incorrect function name

= 2.1.5 =
* Fixed a fatal error bug caused by an incorrect function name

= 2.1.4 =
* Fix bug where total was incorrect when using discount codes

= 2.1.3 =
* Fix bug where invoice generation failed on some hosts

= 2.1.2 =
* Display price option name on the invoice
* Add an option to disable invoices for free downloads

= 2.1.1 =
* Fixed a bug with an incorrect reference to a function name

= 2.1 =
* Display customer addresses
* Refactor a lot of code
* Performance improvements and bug fixes
* Fix bug with logo display
* Add a filter to allow changing of the filename of the generated PDF invoice

= 2.0.4 =
* Remove image size check because it fails when trying to retrieve the size of a remote image.

= 2.0.3 =
* Fixed localisation bug

= 2.0.2 =
* Added support for showing cart fees as invoice line items
* Fixed bug with payment meta not always displaying
* Fixed bug where 'Total Amount' wasn't showing correctly

= 2.0.1 =
* Fixed minor bug with internationalization

= 2.0 =
* Changed to using TCPDF, FPDF is no longer used (any custom templates that were built need to be upgraded to use TCPDF)
* Fixed major bug with characters in Greek alphabet not displaying properly
* Added option to change fonts to Free Sans/Free Serif for characters not displaying properly
* Major code refactoring
* Fixed a bug with the the verification of the invoice request
* Updated PHPDoc
* Changed Additional Notes to use date_i18n() rather than date()
* Updated language files
* Added WPML support

= 1.0.4 =
* Fixed syntax error and minor bug
* Invoice link now displays on [edd_receipt] shortcode

= 1.0.3 =
* Taxes now display on the invoices if they are enabled
* Invoice link now only shows in Payment History if the payment status is set to "Completed"

= 1.0.2 =
* Added filter for showing custom templates name in the Settings

= 1.0.1 =
* Added support for iOS Devices

= 1.0 =
* First offical release!

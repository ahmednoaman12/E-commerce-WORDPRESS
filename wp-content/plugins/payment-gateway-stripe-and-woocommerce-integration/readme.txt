=== Stripe Payment Gateway for WooCommerce (Credit/Debit Card, Apple Pay, Google Pay, Alipay, Stripe Checkout)  ===
Contributors: webtoffee
Donate link: https://www.webtoffee.com/plugins/
Tags: stripe, woocommerce, apple pay, alipay, payment gateway, credit card, SCA ready, Google Pay,
Requires at least: 3.0.1
Tested up to: 5.7
Requires PHP: 5.6
Stable tag: 3.5.8
License: GPLv3
License URI: http://www.gnu.org/licenses/gpl-3.0.html

Add Stripe payment gateway to your WooCommerce store to accept payments via credit/debit cards, Alipay, Apple Pay, Google Pay, and Stripe checkout.

== Description ==

= Introduction =
Do you want to accept Credit cards, Debit cards, Alipay, Apple Pay & Google Pay on your website? Our Stripe Payment Gateway Plugin for WooCommerce lets you accept payments from multiple channels directly on your website via the Stripe Payment Gateway.

With this Stripe plugin, your customer can use their credit/debit card during the checkout process and Stripe.com handles the rest. This means a smoother experience for your users as they never have to leave your website for making payments.

What else? This free payment plugin, a unique and intuitive Stripe overview page will provide you with a consolidated overview of Stripe Payments, where you can do one-click capture and refund of payments.

Integrating the Stripe Payments Gateway in your WooCommerce store would be the best way to enable a smooth payment flow for your customers and business.

For guidance on the installation and setup of the plugin, refer to the [plugin documentation](https://www.webtoffee.com/woocommerce-stripe-payment-gateway-plugin-user-guide/).

= MAJOR FEATURES OF THIS WOOCOMMERCE STRIPE PLUGIN =

 &#128312; SCA-READY - for user-initiated payments
 &#128312; 3D Secure
 &#128312; Pay using credit cards and debit cards within your WooCommerce store.
 &#128312; Supports Apple Pay, Google Pay/Saved cards in the supporting browser. 
 &#128312; Pay using Alipay. 
 &#128312; Pay via Stripe checkout. 
 &#128312; Stripe Overview Page: A dashboard where you can review transactions & do any payment actions.
 &#128312; Capture Later: Capture the authorized payment later.
 &#128312; Supports full & partial refunds.
 &#128312; Automatically send out email receipt after payment to customers.
 &#128312; Restrict payment only from allowed cards.

<blockquote>

= Premium version Features =

 &#9989; Supports WooCommerce Subscriptions.
 &#9989; Timely compatibility updates and bug fixes.
 &#9989; Premium support!

For complete list of features and details, please visit <a rel="nofollow" href="https://www.webtoffee.com/product/woocommerce-stripe-payment-gateway/">Stripe Payment Gateway for WooCommerce</a>
</blockquote>

= About Stripe =
Stripe is an online payment gateway, operating in over 25 countries, that allows both individuals and businesses to accept payments over the Internet. Stripe focuses on providing technical, fraud prevention, and banking infrastructure required to operate online payment systems.

Using Stripe Gateway, with one, unified platform, you’ll be ready to immediately support new tools like Apple Pay, sell products directly from tweets, accept 135+ currencies, and more.

= About Apple Pay =
Apple Pay offers an easy, secure, and private way to pay on iPhone, iPad, Apple Watch, and Mac. In stores, you can use Apple Pay on your iPhone or Apple Watch. Within apps, you can use Apple Pay on your iPhone, iPad, and Apple Watch. Within websites in Safari, you can use Apple Pay on your iPhone, iPad, and Mac. To use Apple Pay with Safari on a Mac model without built-in Touch ID, go to Settings > Wallet & Apple Pay and turn on Allow Payments on Mac.

= About Payment Request Button =
This WooCommerce stripe plugin accepts payments via Google Pay or chrome payment methods. It works if the customer has set up google pay on a device or have cards saved on a supporting browser.

= About Alipay =
Alipay, or Zhifubao in Chinese, is a third-party mobile and online payment platform, established in Hangzhou, China. Alipay operates with more than 65 financial institutions including Visa and MasterCard to provide payment services for more than 460,000 Chinese businesses. Internationally, more than 300 worldwide merchants use Alipay to sell directly to consumers in China. It currently supports transactions in 14 major foreign currencies.

= About Stripe Checkout =
Stripe Checkout creates a secure, Stripe-hosted payment page that lets you collect payments quickly in your WooCommerce store. It works across devices and help increase your conversion. 

= About WebToffee =

WebToffee is a reliable, efficient and focused WooCommerce extension developer firm. Our team comprises of profoundly experienced developers with a vast knowledge pool.

== Installation ==

1. Upload the plugin folder to the '/wp-content/plugins/' directory.
2. Activate the plugin through the 'Plugins' menu in WordPress.
3. That's it! you can now configure the plugin.

== Frequently Asked Questions ==

= Does this Stripe plugin support all cards? = 

No. The plugin has a restriction to use only up to five cards.

= Which countries does Stripe support? = 

Stripe is available for businesses in 25 countries. https://stripe.com/global

= What is SCA ? =

Strong Customer Authentication (SCA) is intended to make payments more secure, requiring online sellers to implement more stringent methods thereby ensuring the payments they are taking are genuine. The rules, which come into effect on September 14th, mean customers will have to approve online payments through a second level of authorisation where the cardholder’s bank and the business accepting the transaction are located in the European Economic Area.

== Screenshots ==

1. Stripe General Settings
2. Stripe Credit/Debit Card Checkout
3. Stripe Inline Credit Card Checkout
4. Stripe Apple Pay Checkout
5. Stripe Google Pay Checkout
6. Stripe Checkout
7. Stripe Alipay Checkout
8. Stripe Overview Page

== Changelog ==

=3.5.8 = 
* Integrated Apple pay and Payment request button (Google Pay).
* [Improvement] – Passing product image to stripe checkout.
* [Improvement] – Language support for stripe checkout landing page.
* Filter “eh_stripe_payment_intent_args”  added for altering payment intent params.
* UI changes and content updates.

=3.5.7 = 
* Tested OK with WC 5.2.2

=3.5.6 = 
* Tested OK with WC 5.1.0 and WP 5.7
* stripe api vendor folder updated

=3.5.5 = 
* Alipay payment suppport for Chinese yuan (CNY) and Malaysian ringgit (MYR) currencies.

=3.5.4 = 
* Tested OK with WC 5.0.0

=3.5.3 = 
* Tested OK with WC 4.9.1

=3.5.2 = 
* Tested OK with WC 4.8.0 and WP 5.6

=3.5.1 = 
* Tested OK with WC 4.7.0
* [bugfix] Error when checkout using 3D secure cards

=3.5.0 = 
* Tested OK with WC 4.6.2

=3.4.9 = 
* Tested OK with WC 4.5.2
* [bugfix] Issue with product image upload 

=3.4.8 = 
* [Bugfix] Payment fails if email address is not provided during checkout
* Tested OK with WC 4.4.1
* Tested OK with WordPress 5.5

=3.4.7 = 
* Changed order creation workflow of Stripe Checkout for fixing checkout validation issues
* Tested OK with WC 4.3.1

=3.4.6 = 
* [bugfix] Problem with fees
* Added payment description in stripe dashboard for Stripe Checkout

=3.4.5 = 
* Tested OK with WC 4.2.2
* [bugfix] Issue with string translation.

=3.4.4 = 
* Tested OK with WC 4.2.0

=3.4.3 = 
* [bugfix] for Payment status in Customer note
* Tested OK with WordPress 5.4

=3.4.2 =
* Improvements on Restricted card UI
* Improvements in Stripe overview page
* [Fix] Mandatory field validation issue
* Tested OK with WC 4.0.1

=3.4.1 =
* Security fix.

=3.4.0 =
* [bugfix] Fixed the plugin translation issue.

=3.3.6 =
* Minor UI changes.
* Fix included to make the plugin translation ready.
* Tested ok with WC 3.9.1

=3.3.5 =
* [bugfix] Fixed conflict with other payment methods.

=3.3.4 =
* Implemented Stripe checkout payment option.
* Added stripe checkout email address on stripe dashboard.
* Updated Stripe settings menu.
* Tested OK with WC 3.8.1
* Tested OK with WordPress 5.3.2

=3.3.3 =
* [bugfix] Fixed HTML validation issue
* Tested OK with WC 3.8.0
* Tested OK with WordPress 5.2.4

=3.3.2 =

* [bugfix] Fixed issues in Restrict card feature    
* [bugfix] Fixed Sidebar style Stripe Overview
* [bugfix] Fixed Alipay description in checkout. 
* [update] Tested OK with WC 3.7.1
* [update] Tested OK with WordPress 5.2.4


=3.3.1 =

* Support payment for Vietnamese dong (VID) currency

=3.3.0 =

* [bugfix] SCA ready for pay for order option
* Code Optimization in capture section.


=3.2.1 =

* SCA READY - for user-initiated payments.
* Removed the Modal Checkout option - in lieu of SCA compliance
* Updated Alipay Payment method into single step.
* Code Optimization
* Tested OK with WordPress 5.2.3
* Tested OK with WC 3.7.0


=3.2.0 =
* Implemented Statemnet Descriptor on stripe checkout
* Tested OK with WC 3.6.4
* Tested OK with wordpress 5.2.1


=3.1.10 =
 * Tested OK with WC 3.5.7
 * Tested OK with wordpress 5.1.1

=3.1.9 =
 * Bug Fix.

=3.1.8 =
 * Tested OK with WC 3.5.2
 * Tested OK with wordpress 5.0

=3.1.7 =
 * Fixed multiple email to the customer.

=3.1.6 =
 * Tested OK with WC 3.5.0

=3.1.5 =
 * Tested OK with WC 3.4.5 and WP4.9.8

=3.1.4 =
 * Tested OK with WC 3.4.4
 * Bitcoin support revoked

=3.1.3 =
 * Tested OK with WC 3.4.0

=3.1.2 =
 * Update for Google Finance API change
 
=3.1.1 =
 * Stripe checkout button with AliPay image

=3.1.0 =
 * Tested OK with WC 3.3.3
 * Filter for show/hide zip-code on stripe credit card pop-up. 

=3.0.9 =
 * Tested OK with WC 3.3.1
 * Added Swedish Language support.  

=3.0.8 =
 * Fix: issue with meta-data count.

=3.0.7 =
 * Fix: issue with Refund using AliPay

= 3.0.6 =
 * Fix: Stripe overview menu fix
 * Fix: Stripe overview table updates.

= 3.0.5 =
 * Update: Tested OK with WC 3.2.5 and WP4.9.
 * Fix: AliPay fixes.

= 3.0.4 =
 * Update: Added Hook for altering Stripe charge params.

= 3.0.3 =
 * Fix: Warning in Stripe Overview Page.

= 3.0.2 =
 * Fix: In Stripe Overview Page: After capturing the amount refund option was not showing.
 * Fix: Currency conversion was not working.
 * Fix: Stripe console error.
 * Fix: In Stripe Overview Page: After full refund amount was showing partially refunded .

= 3.0.1 =
 * Fix: Premium conflict message appearing incorrectly.

= 3.0.0 =

 * Stripe Overview Page: A dashboard where you can review transactions & do any payment actions.
 * Capture Later: Capture the authorized payment later.
 * Full & Partial refunds.
 * Customize each user facing elements by various settings options.
 * Customize Stripe Checkout Logo.
 * Automatic Email Receipt.
 * Save Cards to your Stripe account.
 * WPML Supported: French and German ( Deutschland ) language supported out of the box.
 * Various settings customization options.
 * Restrict payment only from preferred cards.
 * Basic currency conversation feature included.
 * Updated Support link and read-me.


= 2.1.0 =
 * Updated Ali-pay API

= 2.0.5 =
 * Minor Content Change

= 2.0.4 =
 * Marketing Content Change.

= 2.0.3 =
 * Stripe Library conflicts fixed.

= 2.0.2 =
 * Minor Content Change.

= 2.0.1 =
 * Minor Content Change.

= 2.0.0 =
 * Bug Fixes.

= 1.0.9 =
 * WooCommerce Compatible with Older and more V2.7 RC1 .

= 1.0.8 =
 * Version Tested.

= 1.0.7 =
 * Bug Fixes and Improvements.

= 1.0.6 =
 * Improvements and Content Updated.
 * Periodic Update.

= 1.0.5 =
 * Improvements.
 * Periodic Update.

= 1.0.4 =
 * Contents Updated.
 * Periodic Update.

= 1.0.3 =
 * Bug Fixes and Improvements.

= 1.0.2 =
 * Bug Fixes and Improvements.

= 1.0.1 =
 * Bug Fixes and Improvements.

= 1.0 =
 * Customize the user facing elements.
 * Stripe Overview page for Orders.
 * Make refund from Order admin page.
 
== Upgrade Notice ==

=3.5.8 = 
* Integrated Apple pay and Payment request button (Google Pay).
* [Improvement] – Passing product image to stripe checkout.
* [Improvement] – Language support for stripe checkout landing page.
* Filter “eh_stripe_payment_intent_args”  added for altering payment intent params.
* UI changes and content updates.

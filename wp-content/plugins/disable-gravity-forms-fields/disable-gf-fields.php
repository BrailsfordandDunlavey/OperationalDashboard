<?php
/**
 * Plugin Name: Disable Gravity Forms Fields
 * Plugin URI: http://www.engagewp.com/
 * Description: Disable form fields by adding the CSS class "disabled". Fields can also be set to the readonly attribute with a class of "readonly".
 * Author: Ren Ventura
 * Author URI: http://www.engagewp.com/
 * Version: 1.3
 * License: GPL 2.0
 */

/*

	This program is free software; you can redistribute it and/or modify
	it under the terms of the GNU General Public License, version 2, as
	published by the Free Software Foundation.

	Permission is hereby granted, free of charge, to any person obtaining a copy of this
	software and associated documentation files (the "Software"), to deal in the Software
	without restriction, including without limitation the rights to use, copy, modify, merge,
	publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons
	to whom the Software is furnished to do so, subject to the following conditions:

	The above copyright notice and this permission notice shall be included in all copies or
	substantial portions of the Software.

	THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
	IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
	FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
	AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
	LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
	OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
	THE SOFTWARE.

*/

//* Bail if accessed directly
if ( ! defined( 'WPINC' ) )
	die;

add_action( 'wp_enqueue_scripts', 'rv_disable_gf_fields' );
function rv_disable_gf_fields() {
	wp_enqueue_script( 'disable-gf-fields', plugins_url( '/disable-gf-fields.js', __FILE__ ), array( 'jquery' ) );
}
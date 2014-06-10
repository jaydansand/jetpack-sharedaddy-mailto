<?php
/*
Plugin Name: Jetpack Sharedaddy Mailto
Plugin URI: https://github.com/jaydansand/jetpack-sharedaddy-mailto
Description: Change the Jetpack plugin's Sharedaddy module's email share links to mailto links.
Version: 1.0
Author: Jay Dansand
Author URI: 
*/

/*
  Copyright (C) 2014 Lawrence University  

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License as published by
  the Free Software Foundation; either version 2 of the License, or
  (at your option) any later version.

  This program is distributed in the hope that it will be useful,
  but WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
  GNU General Public License for more details.

  You should have received a copy of the GNU General Public License along
  with this program; if not, write to the Free Software Foundation, Inc.,
  51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA.
*/

/**
 * Implement filters sharing_email_check and sharing_email_can_send.
 *
 * Return FALSE to prevent sending email.
 * From sharing-sources.php:
 *  if ( apply_filters( 'sharing_email_check', true, $post, $post_data ) ) { ... send email ... }
 *  if ( ( $data = apply_filters( 'sharing_email_can_send', $data ) ) !== false ) { ... send email ... }
 *
 * @see sharing-sources.php
 */
function jetpack_sharedaddy_mailto_email_filter($data = NULL, $data2 = NULL, $data3 = NULL) {
  echo _('Directly sending email has been disabled.');
  // Always return FALSE.
  return FALSE;
}

/**
 * Implement action wp_enqueue_scripts.
 *
 * Remove the "sharing_email_send_post" action, registered in sharedaddy.php:
 *  add_action( 'sharing_email_send_post', 'sharing_email_send_post' );
 *
 * @see sharedaddy.php
 */
function jetpack_sharedaddy_mailto_email_scripts() {
  // Mark our script as dependent on the Sharedaddy JS file, so we get loaded
  // after it.  This needs to match the ID specified in the line (as of Jetpack
  // 3.0.1): wp_register_script( 'sharing-js', ... );
  // @see sharing_display()
  // @see sharing-service.php
  $dependencies = array(
    'jquery',
    'sharing-js',
  );
  wp_enqueue_script(
    'jetpack-sharedaddy-mailto-unbind-email-click', // ID
    plugin_dir_url(__FILE__) . 'jetpack-sharedaddy-mailto/unbind_email_click.js', // path
    $dependencies
  );
}

/**
 * Implement action sanitize_comment_cookies.
 *
 * Remove the "sharing_email_send_post" action, registered in sharedaddy.php:
 *  add_action( 'sharing_email_send_post', 'sharing_email_send_post' );
 *
 * @see sharedaddy.php
 */
function jetpack_sharedaddy_mailto_email_action() {
  // Remove the action as registered.
  // Note: $function and $priority MUST match those used in the original
  // add_action() call. Make sure this always matches sharedaddy.php's usage.
  $function = 'sharing_email_send_post';
  $success = remove_action('sharing_email_send_post', $function);
}

/**
 * Implement filter sharing_services.
 *
 * @see Sharing_Service::get_all_services()
 */
function jetpack_sharedaddy_mailto_sharing_services($services) {
  require_once(dirname(__FILE__) . '/jetpack-sharedaddy-mailto/JPSDM_Share_Email.php');
  if (isset($services['email'])) {
    $services['email'] = 'JPSDM_Share_Email';
  }
  return $services;
}

// Enqueue our script to unbind sharing.js's "return false;" on the email link.
add_action('wp_enqueue_scripts', 'jetpack_sharedaddy_mailto_email_scripts');
// Add our filters to return FALSE to various "should I send an email?" checks,
// just in case somehow somewhere sometime somebody manages to post to the
// sendmail backend.
add_filter('sharing_email_check', 'jetpack_sharedaddy_mailto_email_filter');
add_filter('sharing_email_can_send', 'jetpack_sharedaddy_mailto_email_filter');
// Add our action to remove_action('sharing_email_send_post').
// We use sanitize_comment_cookies because it is the earliest possible action
// in which the "sharing_email_send_post" action has already been registered
// (and we can therefore remove it). plugins_loaded fires too late.
add_action('sanitize_comment_cookies', 'jetpack_sharedaddy_mailto_email_action');
// Also bind to init as redundancy in case order of operations changes later.
add_action('init', 'jetpack_sharedaddy_mailto_email_action');
// Add our filter to rewrite the email service to use our version of the class.
add_filter('sharing_services', 'jetpack_sharedaddy_mailto_sharing_services');

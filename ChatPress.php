<?php

/**
 * Plugin Name: ChatPress AI
 * Plugin URI:  chatpress.ai
 * Author:      ChatPress AI
 * Author URI:  chatpress.ai
 * Description: Train an AI bot on your website content so your website visitors can chat with your AI chat representative
 * Version:     0.3.5
 * License:     GPL-2.0+
 * License URL: http://www.gnu.org/licenses/gpl-2.0.txt
 * text-domain: chatpress-ai
 */

if (!defined('ABSPATH')) exit; // Exit if accessed directly

define('CPAIWP_WPRK_PATH', trailingslashit(plugin_dir_path(__FILE__)));


add_action('admin_menu', 'cpaiwp_add_menu_page');

function cpaiwp_add_menu_page()
{
  $cpaiwp_logo_svg = '<svg width="300" height="300" viewBox="0 0 800 900" fill="none" xmlns="http://www.w3.org/2000/svg">
  <path fill-rule="evenodd" clip-rule="evenodd" d="M390.699 450.494C390.699 469.591 374.672 485.071 354.902 485.071C335.132 485.071 319.105 469.591 319.105 450.494C319.105 431.397 335.132 415.917 354.902 415.917C374.672 415.917 390.699 431.397 390.699 450.494ZM501.159 450.494C501.159 469.591 485.132 485.071 465.362 485.071C445.592 485.071 429.565 469.591 429.565 450.494C429.565 431.397 445.592 415.917 465.362 415.917C485.132 415.917 501.159 431.397 501.159 450.494ZM575.821 485.071C595.591 485.071 611.618 469.591 611.618 450.494C611.618 431.397 595.591 415.917 575.821 415.917C556.051 415.917 540.024 431.397 540.024 450.494C540.024 469.591 556.051 485.071 575.821 485.071Z" fill="#EC4899"/>
  <path fill-rule="evenodd" clip-rule="evenodd" d="M708.145 599.072C656.833 676.513 566.995 727.845 464.745 727.845C441.031 727.845 417.984 725.084 395.93 719.876L262.25 781.698V647.774C208.995 597.174 175.971 526.767 175.971 448.91C175.971 294.859 305.26 169.976 464.745 169.976C567.434 169.976 657.605 221.75 708.804 299.748L800 136.412C715.349 52.273 596.931 0 465.873 0C208.579 0 0 201.472 0 450C0 698.528 208.579 900 465.873 900C596.931 900 715.349 847.727 800 763.588L708.145 599.072Z" fill="#EC4899"/>
  </svg>';

  $menu_icon = 'data:image/svg+xml;base64,' . base64_encode($cpaiwp_logo_svg);

  add_menu_page('ChatPress AI', 'ChatPress AI', 'manage_options', 'chatpress-ai', 'initialise_plugin', $menu_icon);
}

function initialise_plugin()
{
  require_once plugin_dir_path(__FILE__) . 'templates/app.php';
}

add_action('admin_enqueue_scripts', 'cpaiwp_admin_enqueue_scripts');
add_action('admin_enqueue_scripts', 'cpaiwp_admin_dequeue_scripts');

register_deactivation_hook(__FILE__, 'cpaiwp_plugin_deactivate');
register_uninstall_hook(__FILE__, 'cpaiwp_plugin_uninstall');

/**
 * Enqueue scripts and styles.
 *
 * @return void
 */
function cpaiwp_admin_enqueue_scripts()
{

  $js_ver  = date("ymd-Gis", filemtime(plugin_dir_path(__FILE__) . 'build/index.js'));
  $css_ver = date("ymd-Gis", filemtime(plugin_dir_path(__FILE__) . 'build/index.css'));

  wp_enqueue_style('chatpress-style', plugin_dir_url(__FILE__) . 'build/index.css', false, $css_ver);
  wp_enqueue_script('chatpress-script', plugin_dir_url(__FILE__) . 'build/index.js', array('wp-element'), $js_ver, true);

  wp_localize_script('chatpress-script', 'appLocalizer', [
    'apiUrl' => home_url('/wp-json'),
    'nonce' => wp_create_nonce('wp_rest'),
    'rest_url' => get_rest_url(null, '/cpai/v1')
  ]);
}


/**
 * Enqueue scripts and styles.
 *
 * @return void
 */
function cpaiwp_admin_dequeue_scripts()
{
  // Check if we are not on the desired admin page
  if (is_admin() && (!isset($_GET['page']) || $_GET['page'] !== 'chatpress-ai')) {
    wp_dequeue_style('chatpress-style');
    wp_dequeue_script('chatpress-script');
  }
}


function cpaiwp_plugin_deactivate()
{
  $cpaiwp_api_key = get_option('cpaiwp_api_key');
  $cpaiwp_chatbot_id = get_option('cpaiwp_chatbot_id');

  if ($cpaiwp_api_key && $cpaiwp_chatbot_id) {

    $api_url = 'https://chatpress.ai/api/chatbots/' . $cpaiwp_chatbot_id . '/deactivate';

    $request_args = array(
      'method'    => 'POST',
      'headers'   => array(
        'Content-Type' => 'application/json',
        'x-api-key'    => $cpaiwp_api_key,
      ),
      'blocking'  => true, // Wait for the request to complete
    );

    $response = wp_remote_request($api_url, $request_args);
  }
  wp_dequeue_script('chatpress-embed');
}

function cpaiwp_plugin_uninstall()
{
  $settingOptions = array('cpaiwp_api_key', 'cpaiwp_chatbot_id', 'cpaiwp_pages_added', 'cpaiwp_page_ids', 'cpaiwp_post_ids', 'cpaiwp_products_added', 'cpaiwp_product_ids');


  //TODO: get cpaiwp_api_key and cpaiwp_chatbot_id, call chatpress /api/chatbots/$chatbotId/uninstall
  $cpaiwp_api_key = get_option('cpaiwp_api_key');
  $cpaiwp_chatbot_id = get_option('cpaiwp_chatbot_id');
  if ($cpaiwp_api_key && $cpaiwp_chatbot_id) {
    $api_url = 'https://chatpress.ai/api/chatbots/' . $cpaiwp_chatbot_id . '/uninstall';

    $request_args = array(
      'method'    => 'POST',
      'headers'   => array(
        'Content-Type' => 'application/json',
        'x-api-key'    => $cpaiwp_api_key,
      ),
      'blocking'  => true, // Wait for the request to complete
    );

    $response = wp_remote_request($api_url, $request_args);
  }
  wp_dequeue_script('chatpress-embed');


  foreach ($settingOptions as $settingName) {
    delete_option($settingName);
  }
}

require_once CPAIWP_WPRK_PATH . 'classes/class-create-settings-routes.php';
require_once CPAIWP_WPRK_PATH . 'classes/class-add-pages-routes.php';
require_once CPAIWP_WPRK_PATH . 'classes/class-publish-chatbot.php';
require_once CPAIWP_WPRK_PATH . 'classes/class-create-wc-routes.php';

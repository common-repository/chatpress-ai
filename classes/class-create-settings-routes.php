<?php

/**
 * This file will create Custom Rest API End Points.
 */
if (!defined('ABSPATH')) exit; // Exit if accessed directly
class CPAIWP_React_Settings_Rest_Route
{

    public function __construct()
    {
        add_action('rest_api_init', [$this, 'create_rest_routes']);
    }

    public function create_rest_routes()
    {
        register_rest_route('cpai/v1', '/settings', [
            'methods' => 'GET',
            'callback' => [$this, 'retrieve_settings'],
            'permission_callback' => [$this, 'retrieve_settings_permission']
        ]);
        register_rest_route('cpai/v1', '/settings/api_key', [
            'methods' => 'GET',
            'callback' => [$this, 'retrieve_settings_api_key'],
            'permission_callback' => [$this, 'retrieve_settings_permission']
        ]);
        register_rest_route('cpai/v1', '/settings/api_key', [
            'methods' => 'POST',
            'callback' => [$this, 'save_settings_api_key'],
            'permission_callback' => [$this, 'save_settings_permission']
        ]);
        register_rest_route('cpai/v1', '/settings/chatbot_created', [
            'methods' => 'POST',
            'callback' => [$this, 'save_settings_chatbot_created'],
            'permission_callback' => [$this, 'save_settings_permission']
        ]);

        register_rest_route('cpai/v1', '/settings/pages_added', [
            'methods' => 'POST',
            'callback' => [$this, 'save_settings_pages_added'],
            'permission_callback' => [$this, 'save_settings_permission']
        ]);

        register_rest_route('cpai/v1', '/settings/update_page_ids', [
            'methods' => 'POST',
            'callback' => [$this, 'save_settings_pages_updated'],
            'permission_callback' => [$this, 'save_settings_permission']
        ]);

        register_rest_route('cpai/v1', '/settings/update_posts_ids', [
            'methods' => 'POST',
            'callback' => [$this, 'save_settings_posts_updated'],
            'permission_callback' => [$this, 'save_settings_permission']
        ]);

        register_rest_route('cpai/v1', '/settings/update_custom_posts_ids', [
            'methods' => 'POST',
            'callback' => [$this, 'save_settings_custom_posts_updated'],
            'permission_callback' => [$this, 'save_settings_permission']
        ]);

        register_rest_route('cpai/v1', '/settings/reset', [
            'methods' => 'POST',
            'callback' => [$this, 'reset_settings'],
            'permission_callback' => [$this, 'reset_settings_permission']
        ]);

        register_rest_route('cpai/v1', '/settings/restore', [
            'methods' => 'POST',
            'callback' => [$this, 'restore_settings'],
            'permission_callback' => [$this, 'restore_settings_permission']
        ]);

        register_rest_route('cpai/v1', '/settings/products_added', [
            'methods' => 'POST',
            'callback' => [$this, 'save_settings_products_added'],
            'permission_callback' => [$this, 'save_settings_permission']
        ]);

        register_rest_route('cpai/v1', '/settings/update_product_ids', [
            'methods' => 'POST',
            'callback' => [$this, 'save_settings_product_updated'],
            'permission_callback' => [$this, 'save_settings_permission']
        ]);

        register_rest_route('cpai/v1', '/settings/delete-chatbot', [
            'methods' => 'POST',
            'callback' => [$this, 'delete_chatbot'],
            'permission_callback' => [$this, 'save_settings_permission']
        ]);
    }

    public function retrieve_settings()
    {
        $siteurl = get_option('siteurl');
        $cpaiwp_api_key = get_option('cpaiwp_api_key');
        $cpaiwp_chatbot_id = get_option('cpaiwp_chatbot_id');
        $cpaiwp_pages_added = get_option('cpaiwp_pages_added');
        $cpaiwp_products_added = get_option('cpaiwp_products_added');

        $response_data = [
            'siteurl' => $siteurl,
            'cpaiwp_api_key' => $cpaiwp_api_key,
            'cpaiwp_chatbot_id' => $cpaiwp_chatbot_id,
            'cpaiwp_pages_added' => $cpaiwp_pages_added,
            'cpaiwp_products_added' => $cpaiwp_products_added
        ];

        $response = rest_ensure_response($response_data);
        $response->set_status(200);

        $no_cache_headers = wp_get_nocache_headers();
        $response->set_headers($no_cache_headers);

        return $response;
    }

    public function retrieve_settings_api_key()
    {

        $cpaiwp_api_key = get_option('cpaiwp_api_key');

        $response_data = [
            'cpaiwp_api_key' => $cpaiwp_api_key
        ];
        $response = rest_ensure_response($response_data);
        $response->set_status(200);
        // $response->set_headers([ 'Cache-Control' => 'no-cache, no-store' ]);
        $no_cache_headers = wp_get_nocache_headers();
        $response->set_headers($no_cache_headers);

        return $response;
    }

    public function retrieve_settings_permission()
    {
        return current_user_can('publish_posts');
    }

    public function save_settings_api_key($req)
    {
        $cpaiwp_api_key = sanitize_text_field($req['cpaiwp_api_key']);

        update_option('cpaiwp_api_key', $cpaiwp_api_key);

        $response = rest_ensure_response('success');
        $response->set_status(200);
        $response->set_headers(['Cache-Control' => 'no-cache, no-store']);

        return $response;
    }

    public function save_settings_chatbot_created($req)
    {
        $cpaiwp_chatbot_id = sanitize_text_field($req['cpaiwp_chatbot_id']);

        update_option('cpaiwp_chatbot_id', $cpaiwp_chatbot_id);


        $response = rest_ensure_response('success');
        $response->set_status(200);
        $response->set_headers(['Cache-Control' => 'no-cache, no-store']);

        return $response;
    }

    public function save_settings_pages_updated($req)
    {

        $cpaiwp_page_ids = sanitize_text_field($req['ids']);

        update_option('cpaiwp_page_ids', json_decode($cpaiwp_page_ids));

        return rest_ensure_response('success');
    }

    public function save_settings_posts_updated($req)
    {

        $cpaiwp_post_ids = sanitize_text_field($req['ids']);

        update_option('cpaiwp_post_ids', json_decode($cpaiwp_post_ids));

        return rest_ensure_response('success');
    }

    public function save_settings_custom_posts_updated($req)
    {

        $cpaiwp_custom_post_ids = sanitize_text_field($req['ids']);

        update_option('cpaiwp_custom_post_ids', json_decode($cpaiwp_custom_post_ids));

        return rest_ensure_response('success');
    }

    public function save_settings_pages_added($req)
    {
        $cpaiwp_page_ids = sanitize_text_field($req['pageIds']);
        $cpaiwp_post_ids = sanitize_text_field($req['postIds']);
        $cpaiwp_custom_post_ids = sanitize_text_field($req['customPostIds']);


        update_option('cpaiwp_pages_added', true);
        update_option('cpaiwp_page_ids', json_decode($cpaiwp_page_ids));
        update_option('cpaiwp_post_ids', json_decode($cpaiwp_post_ids));
        update_option('cpaiwp_custom_post_ids', json_decode($cpaiwp_custom_post_ids));

        return rest_ensure_response('success');
    }

    public function save_settings_permission()
    {
        return current_user_can('publish_posts');
    }

    public function reset_settings($req)
    {

        $settingOptions = array('cpaiwp_api_key', 'cpaiwp_chatbot_id', 'cpaiwp_pages_added', 'cpaiwp_page_ids', 'cpaiwp_post_ids', 'cpaiwp_products_added', 'cpaiwp_product_ids', 'cpaiwp_custom_post_ids');

        foreach ($settingOptions as $settingName) {
            delete_option($settingName);
        }

        return rest_ensure_response('reset');
    }



    public function reset_settings_permission()
    {
        return current_user_can('publish_posts');
    }

    public function restore_settings($req)
    {

        $cpaiwp_chatbot_id = sanitize_text_field($req['chatbotId']);
        $cpaiwp_page_ids = sanitize_text_field($req['pageIds']);
        $cpaiwp_post_ids = sanitize_text_field($req['postIds']);
        $cpaiwp_custom_post_ids = sanitize_text_field($req['customPostIds']);
        $cpaiwp_product_ids = sanitize_text_field($req['productIds']);
        $cpaiwp_products_added = sanitize_text_field($req['cpaiwp_products_added']);


        update_option('cpaiwp_chatbot_id', $cpaiwp_chatbot_id);
        update_option('cpaiwp_pages_added', true);
        update_option('cpaiwp_page_ids', json_decode($cpaiwp_page_ids));
        update_option('cpaiwp_post_ids', json_decode($cpaiwp_post_ids));
        update_option('cpaiwp_custom_post_ids', json_decode($cpaiwp_custom_post_ids));
        update_option('cpaiwp_products_added', $cpaiwp_products_added);
        update_option('cpaiwp_product_ids', json_decode($cpaiwp_product_ids));

        return rest_ensure_response('restored');
    }



    public function save_settings_products_added($req)
    {
        $cpaiwp_product_ids = sanitize_text_field($req['productIds']);

        update_option('cpaiwp_products_added', true);
        update_option('cpaiwp_product_ids', json_decode($cpaiwp_product_ids));

        return rest_ensure_response('success');
    }

    public function save_settings_product_updated($req)
    {

        $cpaiwp_product_ids = sanitize_text_field($req['productIds']);

        update_option('cpaiwp_product_ids', json_decode($cpaiwp_product_ids));

        return rest_ensure_response('success');
    }

    public function restore_settings_permission()
    {
        return current_user_can('publish_posts');
    }

    public function delete_chatbot($req)
    {

        $settingOptions = array('cpaiwp_chatbot_id', 'cpaiwp_pages_added', 'cpaiwp_page_ids', 'cpaiwp_post_ids', 'cpaiwp_custom_post_ids', 'cpaiwp_products_added', 'cpaiwp_product_ids');

        foreach ($settingOptions as $settingName) {
            delete_option($settingName);
        }

        return rest_ensure_response('delete');
    }
}
new CPAIWP_React_Settings_Rest_Route();

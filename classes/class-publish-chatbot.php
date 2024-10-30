<?php
/**
 * This file will create Custom Rest API End Points.
 */
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
class CPAIWP_React_Publish_Rest_Route {

    public function __construct() {
        add_action( 'rest_api_init', [ $this, 'create_rest_routes' ] );
        add_action( 'wp_enqueue_scripts', [$this, 'initialize_embed_script'] );
    }

    public function create_rest_routes() {
        register_rest_route( 'cpai/v1', '/publish-chatbot', [
            'methods' => 'POST',
            'callback' => [ $this, 'publish_chatbot' ],
            'permission_callback' => [ $this, 'retrieve_publish_permission' ]
        ] );
        register_rest_route( 'cpai/v1', '/unpublish-chatbot', [
            'methods' => 'GET',
            'callback' => [ $this, 'unpublish_chatbot' ],
            'permission_callback' => [ $this, 'retrieve_publish_permission' ]
        ] );
    }

    public function publish_chatbot(){

        $response = [
            'ok' => 'Yes'
        ];

        return rest_ensure_response( $response );
    }

    public function initialize_embed_script() {
        $cpaiwp_chatbot_id = get_option( 'cpaiwp_chatbot_id' );
  $js_ver  = date("ymd-Gis");

        wp_enqueue_script('chatpress-embed', 'https://chatpress.ai/embed/' . $cpaiwp_chatbot_id . '.js', array() , $js_ver, true);
        // wp_enqueue_script('chatpress-embed', 'http://127.0.0.1:8788/embed/' . $cpaiwp_chatbot_id . '.js', [] , '1.0', true);
    }

    public function retrieve_publish_permission() {
        return current_user_can('publish_posts');
    }
}
new CPAIWP_React_Publish_Rest_Route();
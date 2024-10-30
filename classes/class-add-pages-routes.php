<?php

/**
 * This file will create Custom Rest API End Points.
 */
if (!defined('ABSPATH')) exit; // Exit if accessed directly
class CPAIWP_React_Pages_Rest_Route
{

    public function __construct()
    {
        add_action('rest_api_init', [$this, 'create_rest_routes']);
    }

    public function create_rest_routes()
    {
        register_rest_route('cpai/v1', '/get-page-ids', [
            'methods' => 'GET',
            'callback' => [$this, 'retrieve_page_ids'],
            'permission_callback' => [$this, 'retrieve_pages_permission']
        ]);
        register_rest_route('cpai/v1', '/get-post-ids', [
            'methods' => 'GET',
            'callback' => [$this, 'retrieve_post_ids'],
            'permission_callback' => [$this, 'retrieve_pages_permission']
        ]);
        register_rest_route('cpai/v1', '/get-custom-post-ids', [
            'methods' => 'GET',
            'callback' => [$this, 'retrieve_custom_post_ids'],
            'permission_callback' => [$this, 'retrieve_pages_permission']
        ]);
        register_rest_route('cpai/v1', '/get-page-post-ids', [
            'methods' => 'GET',
            'callback' => [$this, 'retrieve_post_page_ids'],
            'permission_callback' => [$this, 'retrieve_pages_permission']
        ]);
        register_rest_route('cpai/v1', '/initial-pages-posts', [
            'methods' => 'GET',
            'callback' => [$this, 'retrieve_pages_posts'],
            'permission_callback' => [$this, 'retrieve_pages_permission']
        ]);

        register_rest_route('cpai/v1', '/initial-custom-posts', [
            'methods' => 'GET',
            'callback' => [$this, 'retrieve_custom_posts'],
            'permission_callback' => [$this, 'retrieve_pages_permission']
        ]);

        register_rest_route('cpai/v1', '/pages', [
            'methods' => 'GET',
            'callback' => [$this, 'retrieve_paginated_pages'],
            'permission_callback' => [$this, 'retrieve_pages_permission']
        ]);

        register_rest_route('cpai/v1', '/posts', [
            'methods' => 'GET',
            'callback' => [$this, 'retrieve_paginated_posts'],
            'permission_callback' => [$this, 'retrieve_pages_permission']
        ]);

        register_rest_route('cpai/v1', '/custom-posts', [
            'methods' => 'GET',
            'callback' => [$this, 'retrieve_paginated_custom_posts'],
            'permission_callback' => [$this, 'retrieve_pages_permission']
        ]);

        register_rest_route('cpai/v1', '/post-types', [
            'methods' => 'GET',
            'callback' => [$this, 'retrieve_post_types'],
            'permission_callback' => [$this, 'retrieve_pages_permission']
        ]);

        //for woocommerce integration
        register_rest_route('cpai/v1', '/get-product-ids', [
            'methods' => 'GET',
            'callback' => [$this, 'retrieve_product_ids'],
            'permission_callback' => [$this, 'retrieve_products_permission']
        ]);
        register_rest_route('cpai/v1', '/products', [
            'methods' => 'GET',
            'callback' => [$this, 'retrieve_paginated_products'],
            'permission_callback' => [$this, 'retrieve_products_permission']
        ]);
        register_rest_route('cpai/v1', '/store', [
            'methods' => 'GET',
            'callback' => [$this, 'retrieve_store_meta'],
            'permission_callback' => [$this, 'retrieve_products_permission']
        ]);
        register_rest_route('cpai/v1', '/added-products', [
            'methods' => 'GET',
            'callback' => [$this, 'retrieve_added_products'],
            'permission_callback' => [$this, 'retrieve_products_permission']
        ]);

        //This is a route to get pages content which is already avaialble through WordPress API

        //check whether this api endpoint exists
        register_rest_route('cpai/v1', '/pages-access-enabled', array(
            'methods'  => 'GET',
            'callback' => [$this, 'page_access_enabled']
        ));

        register_rest_route('cpai/v1', '/get-page-content/(?P<page_id>\d+)', array(
            'methods'  => 'GET',
            'callback' => array($this, 'get_page_content'),
            'args'     => array(
                'page_id' => array(
                    'validate_callback' => function ($param, $request, $key) {
                        return is_numeric($param);
                    },
                ),
            ),
        ));
    }

    public function page_access_enabled()
    {
        return rest_ensure_response(['ok' => true]);
    }

    public function get_page_content($data)
    {
        // Ensure $page_id is valid and is an integer
        $page_id = $data['page_id'];

        if (empty($page_id)) {
            return rest_ensure_response(['error' => 'Invalid page ID']);
        }

        $page = get_post($page_id);

        if (!$page || is_wp_error($page)) {
            return rest_ensure_response(['error' => 'Page not found']);
        }

        $custom_page = [
            'id'       => $page->ID,
            'title'    => $page->post_title,
            'type'     => $page->post_type,
            'link'     => get_permalink($page->ID),
            'content'  => do_shortcode($page->post_content)
            // Add other properties as needed
        ];

        return rest_ensure_response($custom_page);
    }

    public function retrieve_page_ids()
    {
        $existing_page_ids = get_option('cpaiwp_page_ids');
        $response = [
            'cpaiwp_page_ids' => $existing_page_ids
        ];

        return rest_ensure_response($response);
    }

    public function retrieve_post_types()
    {
        $post_types = get_post_types(['publicly_queryable' => 1, '_builtin' => false], 'object');
        $post_types_names = array();

        foreach ($post_types as $post_type) {
            $post_type_data = array(
                'name' => $post_type->name,
                'label' => $post_type->label
            );
            $post_types_names[] = $post_type_data;
        }

        return rest_ensure_response($post_types_names);
    }

    public function retrieve_post_ids()
    {
        $existing_post_ids = get_option('cpaiwp_post_ids');
        $response = [
            'cpaiwp_post_ids' => $existing_post_ids
        ];

        return rest_ensure_response($response);
    }

    public function retrieve_custom_post_ids()
    {
        $existing_custom_post_ids = get_option('cpaiwp_custom_post_ids');
        $response = [
            'cpaiwp_custom_post_ids' => $existing_custom_post_ids
        ];

        return rest_ensure_response($response);
    }
    public function retrieve_post_page_ids()
    {
        $existing_post_ids = get_option('cpaiwp_post_ids');
        $existing_page_ids = get_option('cpaiwp_page_ids');
        $existing_custom_post_ids = get_option('cpaiwp_custom_post_ids');

        $response = [
            'cpaiwp_post_ids' => $existing_post_ids,
            'cpaiwp_page_ids' => $existing_page_ids,
            'cpaiwp_custom_post_ids' => $existing_custom_post_ids
        ];

        return rest_ensure_response($response);
    }

    public function retrieve_pages_posts($req)
    {
        $include_existing = $req->get_param('include_existing');

        $total_posts = wp_count_posts($post_type = 'post');
        $total_pages = wp_count_posts($post_type = 'page');

        $excluded_post_ids = get_option('cpaiwp_post_ids');
        $excluded_page_ids = get_option('cpaiwp_page_ids');

        if ($include_existing == 'y') {
            $posts = get_posts([
                'numberposts' => '10',
                'order' => 'DESC',
                'orderby' => 'modified',
                'include' => $excluded_post_ids
            ]);

            $pages = get_pages([
                'number' => '10',
                'sort_order' => 'DESC',
                'sort_column' => 'post_modified',
                'include' => $excluded_page_ids
            ]);

            $response = [
                'total_posts' => is_array($excluded_post_ids) ? count($excluded_post_ids) : 0,
                'total_pages' => is_array($excluded_page_ids) ? count($excluded_page_ids) : 0,
                'posts' => empty($excluded_post_ids) ? array() : $posts,
                'pages' => empty($excluded_page_ids) ? array() : $pages
            ];
        } else {
            $posts = get_posts([
                'numberposts' => '10',
                'order' => 'DESC',
                'orderby' => 'modified',
                'exclude' => $excluded_post_ids
            ]);

            $pages = get_pages([
                'number' => '10',
                'sort_order' => 'DESC',
                'sort_column' => 'post_modified',
                'exclude' => $excluded_page_ids,
            ]);

            $response = [
                'total_posts' => $total_posts,
                'total_pages' => $total_pages,
                'posts' => $posts,
                'pages' => $pages
            ];
        }


        return rest_ensure_response($response);
    }

    public function retrieve_custom_posts()
    {
        $total_posts = wp_count_posts($post_type = 'news');

        $excluded_post_ids = get_option('cpaiwp_custom_post_ids');

        $posts = get_posts([
            'numberposts' => '10',
            'order' => 'DESC',
            'orderby' => 'modified',
            'exclude' => $excluded_post_ids,
            'post_type' => 'news'
        ]);

        $response = [
            'total_posts' => $total_posts,
            'posts' => $posts,
        ];

        return rest_ensure_response($response);
    }

    public function retrieve_paginated_pages($req)
    {
        //TODO: check if the query has limit and offset parameters, if not set to 10 and 0

        $count_pages = wp_count_posts($post_type = 'page');
        $include_existing = $req->get_param('include_existing');

        $limit = $req->get_param('limit');
        $offset = $req->get_param('offset');
        $excluded_page_ids = get_option('cpaiwp_page_ids');

        if ($include_existing == 'y') {
            $pages = get_pages([
                'number' => $limit,
                'offset' => $offset,
                'sort_order' => 'DESC',
                'sort_column' => 'post_modified',
                'include' => $excluded_page_ids
            ]);

            $response = [
                'total' => is_array($excluded_page_ids) ? count($excluded_page_ids) : 0,
                'pages' => empty($excluded_page_ids) ? array() : $pages,
                'limit' => $limit,
                'offset' => $offset
            ];
        } else {
            $pages = get_pages([
                'number' => $limit,
                'offset' => $offset,
                'sort_order' => 'DESC',
                'sort_column' => 'post_modified',
                'exclude' => $excluded_page_ids
            ]);

            $response = [
                'total' => $count_pages,
                'pages' => $pages,
                'limit' => $limit,
                'offset' => $offset
            ];
        }

        return rest_ensure_response($response);
    }

    public function retrieve_paginated_posts($req)
    {
        $count_posts = wp_count_posts($post_type = 'post');
        $include_existing = $req->get_param('include_existing');

        $limit = $req->get_param('limit');
        $offset = $req->get_param('offset');
        $excluded_post_ids = get_option('cpaiwp_post_ids');


        if ($include_existing == 'y') {
            $posts = get_posts([
                'numberposts' => $limit,
                'offset' => $offset,
                'sort_order' => 'DESC',
                'sort_column' => 'post_modified',
                'include' => $excluded_post_ids
            ]);


            $response = [
                'total' => is_array($excluded_post_ids) ? count($excluded_post_ids) : 0,
                'posts' => empty($excluded_post_ids) ? array() : $posts,
                'limit' => $limit,
                'offset' => $offset
            ];
        } else {

            $posts = get_posts([
                'numberposts' => $limit,
                'offset' => $offset,
                'sort_order' => 'DESC',
                'sort_column' => 'post_modified',
                'exclude' => $excluded_post_ids
            ]);


            $response = [
                'total' => $count_posts,
                'posts' => $posts,
                'limit' => $limit,
                'offset' => $offset
            ];
        }

        return rest_ensure_response($response);
    }


    public function retrieve_paginated_custom_posts($req)
    {
        $post_type = $req->get_param('post_type');
        $include_existing = $req->get_param('include_existing');
        $limit = $req->get_param('limit');
        $offset = $req->get_param('offset');
        $excluded_post_ids = get_option('cpaiwp_custom_post_ids');

        // Filter excluded_post_ids to only include posts of the requested post_type
        $filtered_post_ids = array();
        if (!empty($excluded_post_ids)) {
            foreach ($excluded_post_ids as $post_id) {
                if (get_post_type($post_id) === $post_type) {
                    $filtered_post_ids[] = $post_id;
                }
            }
        }

        $query_args = [
            'numberposts' => $limit,
            'offset' => $offset,
            'orderby' => 'modified',
            'order' => 'DESC',
            'post_type' => $post_type
        ];

        if ($include_existing == 'y') {
            if (!empty($filtered_post_ids)) {
                $query_args['include'] = $filtered_post_ids;
                // Count only the included posts of this post type
                $total_posts = count($filtered_post_ids);
            } else {
                // If there are no posts of this type in excluded_post_ids
                $posts = array();
                $total_posts = 0;
            }

            if (!isset($posts)) {
                $posts = get_posts($query_args);
            }

            $response = [
                'post_type' => $post_type,
                'total' => max(0, $total_posts), // Ensure total is never negative
                'posts' => $posts,
                'limit' => $limit,
                'offset' => $offset,
                'filtered_count' => count($filtered_post_ids) // Optional: might be useful for debugging
            ];
        } else {
            $count_posts = wp_count_posts($post_type = $post_type);
            $posts = get_posts([
                'numberposts' => $limit,
                'offset' => $offset,
                'sort_order' => 'DESC',
                'sort_column' => 'post_modified',
                'exclude' => $excluded_post_ids,
                'post_type' => $post_type
            ]);

            $response = [
                'post_type' => $post_type,
                'total' => $count_posts,
                'posts' => $posts,
                'limit' => $limit,
                'offset' => $offset
            ];
        }


        return rest_ensure_response($response);
    }

    public function retrieve_pages_permission()
    {
        return current_user_can('publish_posts');
    }

    public function retrieve_paginated_products($req)
    {
        //TODO: check if the query has limit and offset parameters, if not set to 10 and 0
        $excluded_product_ids = get_option('cpaiwp_product_ids');

        $products_query = wc_get_products([
            'limit' => 50,
            'exclude' => $excluded_product_ids
        ]);
        foreach ($products_query as $product) {
            $products[] = $product->get_data();
        }

        $response = [
            'products' => $products
        ];

        return rest_ensure_response($response);
    }

    public function retrieve_product_ids()
    {
        $cpaiwp_product_ids = get_option('cpaiwp_product_ids');
        $response = [
            'cpaiwp_product_ids' => $cpaiwp_product_ids
        ];

        return rest_ensure_response($response);
    }

    public function retrieve_added_products($req)
    {
        //TODO: check if the query has limit and offset parameters, if not set to 10 and 0
        $included_product_ids = get_option('cpaiwp_product_ids');

        $products_query = wc_get_products([
            'limit' => 50,
            'include' => $included_product_ids
        ]);
        foreach ($products_query as $product) {
            $products[] = $product->get_data();
        }

        $response = [
            'products' => $products
        ];

        return rest_ensure_response($response);
    }

    public function retrieve_store_meta()
    {
        $siteurl = get_option('siteurl');
        $currency = get_option('woocommerce_currency');
        $woocommerce_permalinks = get_option('woocommerce_permalinks');

        $response_data = [
            'siteurl' => $siteurl,
            'currency' => $currency,
            'woocommerce_permalinks' => $woocommerce_permalinks,
        ];

        return $response = rest_ensure_response($response_data);
    }

    public function retrieve_products_permission()
    {
        return current_user_can('publish_posts');
    }
}
new CPAIWP_React_Pages_Rest_Route();

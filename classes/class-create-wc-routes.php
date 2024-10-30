<?php

/**
 * This file will create Custom Rest API End Points.
 */
if (!defined('ABSPATH')) exit; // Exit if accessed directly
class CPAIWP_React_Wp_Rest_Route
{
  public function __construct()
  {
    add_action('rest_api_init', [$this, 'create_rest_routes']);
  }

  public function create_rest_routes()
  {
    register_rest_route('cpai/v1', '/wc-access-enabled', array(
      'methods'  => 'GET',
      'callback' => [$this, 'wc_access_enabled']
    ));

    register_rest_route('cpai/v1', '/get-wc-products', array(
      'methods'  => 'GET',
      'callback' => array($this, 'get_wc_products'),
      'args'     => array(
        'product_ids' => array(
          'validate_callback' => function ($param, $request, $key) {
            // Check if the parameter is a comma-separated list of integers
            $product_ids = explode(',', $param);
            foreach ($product_ids as $id) {
              if (!is_numeric(trim($id))) {
                return false;
              }
            }
            return true;
          },
        ),
      ),
    ));
  }

  public function wc_access_enabled()
  {
    return rest_ensure_response(['ok' => true]);
  }


  public function get_wc_products($data)
  {
    // Get the comma-separated string of product IDs
    $product_id_string = $data['product_ids'];

    // Convert the string into an array
    $product_ids_array = explode(',', $product_id_string);

    // Trim each element of the array to remove any leading or trailing whitespace
    $product_ids_array = array_map('trim', $product_ids_array);

    $args = [
      'status' => 'publish',
      'include' => $product_ids_array
    ];

    $products_query = wc_get_products($args);


    foreach ($products_query as $product) {
      $attributes_data = array();

      // Extracting product attributes
      foreach ($product->get_attributes() as $attribute_name => $attribute) {
        $attribute_options = array();

        // Extract options
        foreach ($attribute->get_options() as $option_id) {
          $term = get_term($option_id); // Get term object
          $attribute_options[] = $term->name; // Add option name
        }

        // Create attribute object
        $attribute_data = array(
          'id'      => $attribute_name,
          'name'    => wc_attribute_label($attribute_name), // Get attribute label
          'options' => $attribute_options,
        );

        $attributes_data[] = $attribute_data;
      }

      // Extracting default attributes
      $default_attributes_data = array();
      $default_attributes = $product->get_default_attributes();
      foreach ($default_attributes as $attribute_name => $default_value) {
        $term = get_term($default_value); // Get term object
        $default_attribute_data = array(
          'id'      => $attribute_name,
          'name'    => wc_attribute_label($attribute_name), // Get attribute label
          'option' => $default_value, // Default value is considered as an option
        );
        $default_attributes_data[] = $default_attribute_data;
      }

      $product_data[] = array(
        'id'                => $product->get_id(),
        'parent_id'         => $product->get_parent_id(),
        'name'              => $product->get_name(),
        'permalink'         => $product->get_permalink(),
        'short_description' => $product->get_short_description(),
        'price'             => $product->get_price(),
        'attributes'        => $attributes_data, // Product attributes
        'default_attributes' => $default_attributes_data, // Default attributes
      );
    }

    if ($product_data === null) {
      $product_data = array();
    }

    $response = [
      'products' => $product_data
    ];

    return rest_ensure_response($response);
  }
}

new CPAIWP_React_Wp_Rest_Route();

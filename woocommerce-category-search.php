<?php
/*
Plugin Name: WooCommerce Category Search
Description: Adds a product search form with category filter and live AJAX suggestions.
Version: 1.0
Author: Mugniul Afif
*/

if (! defined('ABSPATH')) {
    exit;
}

// Include core logic
require_once plugin_dir_path(__FILE__) . 'search-system.php';

// Enqueue scripts and styles
function wcps_enqueue_assets()
{
    wp_enqueue_style('wcps-style', plugin_dir_url(__FILE__) . 'assets/search-style.css');
    wp_enqueue_script('wcps-script', plugin_dir_url(__FILE__) . 'assets/search-script.js', ['jquery', 'jquery-ui-autocomplete'], null, true);

    wp_localize_script('wcps-script', 'wcps_ajax_object', [
        'ajax_url' => admin_url('admin-ajax.php'),
    ]);
}
add_action('wp_enqueue_scripts', 'wcps_enqueue_assets');

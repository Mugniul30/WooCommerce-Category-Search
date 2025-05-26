<?php

    // === 1. Shortcode: Custom Search Form ===
    function wc_category_search_bar_shortcode()
    {
        ob_start();
    ?>
    <form role="search" method="get" class="woocommerce-product-search custom-search-form" action="<?php echo esc_url(home_url('/')); ?>">
        <div class="search-container">
            <input type="search" class="search-field" placeholder="Search for Product…" value="<?php echo get_search_query(); ?>" name="s" autocomplete="off" />
            <select name="product_cat" class="category-dropdown">
                <option value="">Categories</option>
                <?php
                    $terms = get_terms(['taxonomy' => 'product_cat', 'hide_empty' => true]);
                        foreach ($terms as $term) {
                            echo '<option value="' . esc_attr($term->slug) . '">' . esc_html($term->name) . '</option>';
                        }
                    ?>
            </select>
            <button type="submit" class="search-submit"><span class="dashicons dashicons-search"></span></button>
            <input type="hidden" name="post_type" value="product" />
        </div>
    </form>
    <?php
        return ob_get_clean();
        }
        add_shortcode('wc_category_search_bar', 'wc_category_search_bar_shortcode');

        // === 3. AJAX Product Suggestions ===
        add_action('wp_ajax_wcps_live_search', 'wcps_live_search_callback');
        add_action('wp_ajax_nopriv_wcps_live_search', 'wcps_live_search_callback');

        function wcps_live_search_callback()
        {
            $term     = sanitize_text_field($_GET['term']);
            $category = sanitize_text_field($_GET['category']);

            $args = [
                'post_type'      => 'product',
                'post_status'    => 'publish',
                's'              => $term,
                'posts_per_page' => 10,
            ];

            if (! empty($category)) {
                $args['tax_query'] = [
                    [
                        'taxonomy' => 'product_cat',
                        'field'    => 'slug',
                        'terms'    => $category,
                    ],
                ];
            }

            $query       = new WP_Query($args);
            $suggestions = [];

            foreach ($query->posts as $post) {
                $product = wc_get_product($post->ID);
                $image   = get_the_post_thumbnail_url($post->ID, 'thumbnail');
                $price   = $product->get_price_html();

                $suggestions[] = [
                    'label' => $post->post_title,
                    'value' => get_permalink($post->ID),
                    'image' => $image,
                    'price' => $price,
                ];
            }

            wp_send_json($suggestions);
        }

        // Enqueue jQuery UI and pass ajax_url
        function wcps_enqueue_scripts_elementor_safe()
        {
            if (! is_admin()) {
                wp_enqueue_script('jquery-ui-autocomplete');
                wp_localize_script('jquery-ui-autocomplete', 'wcps_ajax_object', [
                    'ajax_url' => admin_url('admin-ajax.php'),
                ]);
            }
        }
        add_action('wp_enqueue_scripts', 'wcps_enqueue_scripts_elementor_safe');

    ?>
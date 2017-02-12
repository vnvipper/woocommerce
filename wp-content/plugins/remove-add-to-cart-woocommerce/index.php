<?php
/*
  Plugin Name: woo-inquire-us-and-disable-add-to-cart-button
  Plugin URI: https://themelocation.com/
  Description: The plugin allows to woo-inquire-us-and-disable-add-to-cart-button
  Version: 1.1
  Author: ThemeL
  Author URI: https://www.themelocation.com
 */

add_action('product_cat_edit_form_fields', 'wpiudacb_edit_form_fields');
add_action('product_cat_edit_form', 'wpiudacb_edit_form');
add_action('product_cat_add_form_fields', 'wpiudacb_edit_form_fields');
add_action('product_cat_add_form', 'wpiudacb_edit_form');


if (!function_exists('wpiudacb_scripts')) {

    /**
     * Adding JS Script
     */
    function wpiudacb_scripts() {
        echo wp_enqueue_script('woo-inquire-us-and-disable-add-to-cart-button.min', plugin_dir_url(__FILE__) . '/js/woo-inquire-us-and-disable-add-to-cart-button.js');
    }

    add_action('admin_enqueue_scripts', 'wpiudacb_scripts');
}


/**
 * Category Form Edit Callback
 * @param type $tag
 */
if (!function_exists('wpiudacb_edit_form_fields')) {

    function wpiudacb_edit_form_fields($tag) {
        $wpiudacb_category_disable_add_to_cart = 'default';
        $wpiudacb_inqure_us_link = '';
        if (isset($tag->term_id)) {
            $termid = $tag->term_id;
            $wpiudacb_category_disable_add_to_cart = get_option("wpiudacb_category_disable_add_to_cart_$termid");
            $wpiudacb_inqure_us_link = get_option("wpiudacb_inqure_us_link_$termid");
        }
        ?>
        <tr class="form-field">
            <th valign="top" scope="row">
                <label for="catpic"><?php _e('Alter Add to Cart Button', ''); ?></label>
            </th>
            <td>
                <select id="wpiudacb_disable_add_to_cart" name="wpiudacb_category_disable_add_to_cart" class="select short" style="">
                    <option value="default" <?php selected($wpiudacb_category_disable_add_to_cart, 'default'); ?> >Default</option>
                    <option value="remove_button" <?php selected($wpiudacb_category_disable_add_to_cart, 'remove_button'); ?>>Remove Button</option>
                    <option value="inquire_us" <?php selected($wpiudacb_category_disable_add_to_cart, 'inquire_us'); ?>>Inquire Us</option>
                </select>
                <span class="description"></span>
            </td>
        </tr>
        <tr class="form-field wpiudacb_inqure_us_link_field">
            <th valign="top" scope="row">
                <label for="catpic"><?php _e('Inquire Us Link', ''); ?></label>
            </th>
            <td>
                <input type="text" class="short" style="" name="wpiudacb_inqure_us_link" id="wpiudacb_inqure_us_link" value="<?php echo esc_url($wpiudacb_inqure_us_link) ?>" placeholder="http://">
                <span class="description"></span>
            </td>
        </tr>
        <?php
    }

}




add_action('edited_product_cat', 'wpiudacb_save_extra_fileds');
add_action('created_product_cat', 'wpiudacb_save_extra_fileds');

/**
 * save extra category extra fields callback function
 * @param type $term_id
 */
if (!function_exists('wpiudacb_save_extra_fileds')) {

    function wpiudacb_save_extra_fileds($term_id) {
        $termid = $term_id;
        if (isset($_POST['wpiudacb_category_disable_add_to_cart'])) {
            $cat_meta = get_option("wpiudacb_category_disable_add_to_cart_$termid");
            if ($cat_meta !== false) {
                update_option("wpiudacb_category_disable_add_to_cart_$termid", $_POST['wpiudacb_category_disable_add_to_cart']);
            } else {
                add_option("wpiudacb_category_disable_add_to_cart_$termid", $_POST['wpiudacb_category_disable_add_to_cart'], '', 'yes');
            }
        }
        if (isset($_POST['wpiudacb_inqure_us_link'])) {
            $cat_meta = get_option("wpiudacb_inqure_us_link_$termid");
            if ($cat_meta !== false) {
                update_option("wpiudacb_inqure_us_link_$termid", $_POST['wpiudacb_inqure_us_link']);
            } else {
                add_option("wpiudacb_inqure_us_link_$termid", $_POST['wpiudacb_inqure_us_link'], '');
            }
        }
    }

}

// when a category is removed
add_filter('deleted_term_taxonomy', 'wpiudacb_remove_tax_Extras');

/**
 * when a category is removed
 * @param type $term_id
 */
if (!function_exists('wpiudacb_remove_tax_Extras')) {

    function wpiudacb_remove_tax_Extras($term_id) {
        $termid = $term_id;
        if ($_POST['taxonomy'] == 'product_cat'):
            if (get_option("wpiudacb_category_disable_add_to_cart_$termid"))
                delete_option("wpiudacb_category_disable_add_to_cart_$termid");
        endif;
    }

}
add_filter('manage_edit-product_cat_columns', 'wpiudacb_taxonomy_columns_type');
add_filter('manage_product_cat_custom_column', 'wpiudacb_taxonomy_columns_type_manage', 10, 3);

/**
 * Taxonomy Columns Type
 * @param array $columns
 * @return type
 */
if (!function_exists('wpiudacb_taxonomy_columns_type')) {

    function wpiudacb_taxonomy_columns_type($columns) {
        $columns['keywords'] = __('Detailed Description', 'dd_tax');
        return $columns;
    }

}

/**
 * Columns Type Manage
 * @global type $wp_version
 * @param type $out
 * @param type $column_name
 * @param type $term
 * @return type
 */
if (!function_exists('wpiudacb_taxonomy_columns_type_manage')) {

    function wpiudacb_taxonomy_columns_type_manage($out, $column_name, $term) {
        global $wp_version;
        $out = get_option("wpiudacb_category_disable_add_to_cart_$termid");
        if (((float) $wp_version) < 3.1)
            return $out;
        else
            echo $out;
    }

}

add_action('woocommerce_before_shop_loop_item', 'wpiudacb_replace_add_to_cart');

/**
 * Replacing add to card button
 * @global type $product
 */
if (!function_exists('wpiudacb_replace_add_to_cart')) {

    function wpiudacb_replace_add_to_cart() {
        global $product;
        $link = $product->get_permalink();
        $text = get_post_custom_values('wpiudacb_disable_add_to_cart', $product->id);

        $terms = get_the_terms($product->id, 'product_cat');
        $cat_option = 'default';

        if (!empty($terms)) {

            foreach ($terms as $cat) {
                if (get_option("wpiudacb_category_disable_add_to_cart_$cat->term_id") != 'Default') {
                    $cat_option = get_option("wpiudacb_category_disable_add_to_cart_$cat->term_id");
                }
            }
        }


        if ((!is_null($text) && $text[0] != 'default' ) || $cat_option != 'default') {
            remove_action('woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart', 10);
        }
    }

}
add_action('woocommerce_after_shop_loop_item', 'wpiudacb_replace_add_to_cart_with_inqure_us_on_listing_page');

/**
 * Adding Inquire Us button to listing page
 * @global type $product
 */
if (!function_exists('wpiudacb_replace_add_to_cart_with_inqure_us_on_listing_page')) {

    function wpiudacb_replace_add_to_cart_with_inqure_us_on_listing_page() {
        global $product;
        $wpiudacb_inqure_us_link = get_post_meta($product->id, 'wpiudacb_inqure_us_link');

        $disable_cart_option = get_post_custom_values('wpiudacb_disable_add_to_cart', $product->id);

        $terms = get_the_terms($product->id, 'product_cat');
        $cat_option = 'default';
        $cat_inquire_us_link = '';
        if (!empty($terms)) {
            foreach ($terms as $cat) {
                if (get_option("wpiudacb_category_disable_add_to_cart_$cat->term_id") != 'Default') {
                    $cat_option = get_option("wpiudacb_category_disable_add_to_cart_$cat->term_id");
                    $cat_inquire_us_link = get_option("wpiudacb_inqure_us_link_$cat->term_id");
                }
            }
        }
        $inquire_us_added = FALSE;
        if ($cat_option != 'default') {
            remove_action('woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart', 10);
            if ($cat_option == 'inquire_us') {
                remove_action('woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart', 10);
                echo do_shortcode('<a href="' . esc_url($cat_inquire_us_link) . '" target="_blank" class="button ">Inquire Us</a>');
                $inquire_us_added = true;
            }
        }
        if (!is_null($disable_cart_option) && $disable_cart_option[0] == 'inquire_us' && !$inquire_us_added) {
            remove_action('woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart', 10);
            echo do_shortcode('<a href="' . esc_url($wpiudacb_inqure_us_link[0]) . '" target="_blank" class="button ">Inquire Us</a>');
        }
    }

}

/* remove add-to-cart from single product  page for product author  */
add_action('woocommerce_before_single_product_summary', 'wpiudacb_user_filter_addtocart_for_single_product_page');

/**
 * Appling Filter on single product page
 * @global type $product
 */
if (!function_exists('wpiudacb_user_filter_addtocart_for_single_product_page')) {

    function wpiudacb_user_filter_addtocart_for_single_product_page() {
        global $product;
        global $post;
        $terms = get_the_terms($product->id, 'product_cat');
        $cat_option = 'default';
        $cat_inquire_us_link = '';
        if (!empty($terms)) {
            foreach ($terms as $cat) {
                if (get_option("wpiudacb_category_disable_add_to_cart_$cat->term_id") != 'Default') {
                    $cat_option = get_option("wpiudacb_category_disable_add_to_cart_$cat->term_id");
                    $cat_inquire_us_link = get_option("wpiudacb_inqure_us_link_$cat->term_id");
                }
            }
        }
        if ($cat_option != 'default') {
            remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_add_to_cart', 30);
            if ($cat_option == 'inquire_us') {
                $product->inqure_us_url = $cat_inquire_us_link;
                add_action('woocommerce_single_product_summary', 'wpiudacb_add_inqure_us_button');
            }
        }
        $text = get_post_custom_values('wpiudacb_disable_add_to_cart', $product->id);
        if (!is_null($text) && $text[0] != 'default') {
            remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_add_to_cart', 30);
        }
        if (!is_null($text) && $text[0] == 'inquire_us') {
            $wpiudacb_inqure_us_link = get_post_meta($post->ID, 'wpiudacb_inqure_us_link');
            $product->inqure_us_url = $wpiudacb_inqure_us_link[0];
            add_action('woocommerce_single_product_summary', 'wpiudacb_add_inqure_us_button');
        }
    }

}

/**
 * Add Inqure Us Button
 * @global type $post
 */
if (!function_exists('wpiudacb_add_inqure_us_button')) {

    function wpiudacb_add_inqure_us_button($as) {
        global $post;
        global $product;
        echo '<a href="' . esc_url($product->inqure_us_url) . '" target="_blank"> <button type="button" class="button alt">Inquire Us</button></a>';
    }

}
add_action('woocommerce_product_options_general_product_data', 'woocommerce_general_product_data_custom_field');

/**
 * Product Data Custom Field
 * @global type $woocommerce
 * @global type $post
 */
if (!function_exists('woocommerce_general_product_data_custom_field')) {


    function woocommerce_general_product_data_custom_field() {
        global $woocommerce, $post;
        echo '<div class="options_group">';
        woocommerce_wp_select(
                array(
                    'id' => 'wpiudacb_disable_add_to_cart',
                    'label' => __('Alter Add to Cart Button', 'woocommerce'),
                    'options' => array(
                        'default' => __('Default', 'woocommerce'),
                        'remove_button' => __('Remove Button', 'woocommerce'),
                        'inquire_us' => __('Inquire Us', 'woocommerce')
                    )
                )
        );
        woocommerce_wp_text_input(
                array(
                    'id' => 'wpiudacb_inqure_us_link',
                    'label' => __('Inquire Us Link', 'woocommerce'),
                    'placeholder' => 'http://',
                    'desc_tip' => 'true',
                    'description' => __('Enter the URL to Inquire Us button.', 'woocommerce'),
                    'value' => get_post_meta($post->ID, 'wpiudacb_inqure_us_link', true)
                )
        );

        echo '</div>';
    }

}

// Save Fields using WooCommerce Action Hook
add_action('woocommerce_process_product_meta', 'woocommerce_process_product_meta_fields_save');

/**
 * Product Meta Fields Save
 * @param type $post_id
 */
if (!function_exists('woocommerce_process_product_meta_fields_save')) {

    function woocommerce_process_product_meta_fields_save($post_id) {
        $wpiudacb_disable_add_to_cart = isset($_POST['wpiudacb_disable_add_to_cart']) ? $_POST['wpiudacb_disable_add_to_cart'] : 'Default';
        update_post_meta($post_id, 'wpiudacb_disable_add_to_cart', $wpiudacb_disable_add_to_cart);

        $wpiudacb_inqure_us_link = isset($_POST['wpiudacb_inqure_us_link']) ? $_POST['wpiudacb_inqure_us_link'] : '';
        update_post_meta($post_id, 'wpiudacb_inqure_us_link', $wpiudacb_inqure_us_link);
    }

}

/**
 * Edit Callback
 */
if (!function_exists('wpiudacb_edit_form')) {

    function wpiudacb_edit_form() {
        
    }

}


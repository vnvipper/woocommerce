<?php

/**
 * Đặt các đoạn code cần tùy biến của bạn vào bên dưới
 */

/**
 * Ẩn mã bưu chính
 * Ẩn địa chỉ thứ hai
 * Đổi tên Bang / Hạt thành Tỉnh / Thành
 * Đổi tên Tỉnh / Thành phố thành Quận / Huyện
 * 
 * 
 * @hook woocommerce_checkout_fields
 * @param $fields
 * @return mixed
 */
function tp_custom_checkout_fields( $fields ) {
 // Ẩn mã bưu chính
 unset( $fields['postcode'] );
 
 // Ẩn địa chỉ thứ hai
 unset( $fields['address_2'] );
 
 // Đổi tên Bang / Hạt thành Tỉnh / Thành
 $fields['state']['label'] = 'Tỉnh / Thành';
 
 // Đổi tên Tỉnh / Thành phố thành Quận / Huyện
 $fields['city']['label'] = 'Quận / Huyện';
 
 return $fields;
}
add_filter( 'woocommerce_default_address_fields', 'tp_custom_checkout_fields' );

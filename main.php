<?php

/**
 * Plugin Name: Cooach - invoice download for clients
 * Description: makes invoices generated in the Fortnox integration plugin downloadable in the frontend
 * Version: 1.0
 * Author: Alexander Städtler
 * License: GPL2
 */
 
// Add "Download Invoice" button to Woocommerce my-account/orders page
add_filter( 'woocommerce_my_account_my_orders_actions', 'wpsh_order_again_button', 9999, 2 );

function wpsh_order_again_button( $actions, $order ) {
    $meta_data_arr = $order->get_meta_data();

    $filteredArray = array_filter(
        $meta_data_arr,
         function($obj){ 
            return $obj->key === '_obj_fortnox_order_invoice';
         });

    foreach ($filteredArray as $obj) {
        if ($obj->key === "_obj_fortnox_order_invoice") {
          $invoice_id = $obj->value;
        }
      };

    // Set HERE the order statuses where you want the cancel button to appear
    $current_status = $order->get_status();
    $acceptable_statuses    = array( 'pending', 'processing', 'on-hold', 'failed', 'completed' );

    if ( in_array( $current_status , $acceptable_statuses) ) {
        $actions['download-invoice'] = array(
            "url" => wp_nonce_url( admin_url( 'admin-ajax.php?action=fortnox_order_list_download_invoice&invoice_id=' . $invoice_id ), 'wc-fortnox-download-invoice' ),
            "name" => "Download Invoice"
        );
    }
    return $actions;
}
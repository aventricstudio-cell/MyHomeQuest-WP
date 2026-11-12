<?php

function wpestate_webhook_send( $data, $formType = 'contact' ) {
    
    $url = wpresidence_get_option( 'wp_estate_webhook_url' );

    if ( ! empty( $url ) ) {

        $sendOption = wpresidence_get_option( 'wp_estate_' . $formType . '_form' );

        if ( $sendOption !== 'yes' ) {
            return; // Skip sending if the option is not enabled
        }

        foreach ( $data as $key => $value ) {
            if ( empty( $value ) ) {
                unset($data[ $key ]); // Remove empty values
            }
            // Sanitize data to prevent XSS or other issues
            $data[ $key ] = sanitize_text_field( $value );
        }

        $response = wp_remote_post( $url, array(
            'body' => wp_json_encode( $data ),
            'headers' => array(
                'Content-Type' => 'application/json',
            ),
        ) );

        if ( is_wp_error( $response ) ) {
        //    error_log( 'Webhook error: ' . $response->get_error_message() );
        }
    }
}

<?php
/**
 * Plugin Name: TechiePress Callaback URL Receive
 * Plugin URI: https://omukiguy.com
 * Author: TechiePress
 * Author URI: https://omukiguy.com
 * Description: Build a POST REST API endpoint to receive data from another API.
 * Version: 0.1.0
 * License: GPL2 or later
 * License URL: http://www.gnu.org/licenses/gpl-2.0.txt
 * text-domain: prefix-plugin-name
*/
 
add_action( 'rest_api_init', 'techiepress_add_callback_url_endpoint' );

function techiepress_add_callback_url_endpoint(){
    register_rest_route(
        'techiepress/v1/', // Namespace
        'receive-callback', // Endpoint
        array(
            'methods'  => 'POST',
            'callback' => 'techiepress_receive_callback'
        )
    );
}


function techiepress_receive_callback( $request_data ) {
    $data = array();
    
    $parameters = $request_data->get_params();
    
    $name     = $parameters['name'];
    $password = $parameters['password'];
    
    if ( isset($name) && isset($password) ) {
        
        $data['status'] = 'OK';
    
        $data['received_data'] = array(
            'name'     => $name,
            'password' => $password,
        );
        
        $data['message'] = 'You have reached the server';
        
    } else {
        
        $data['status'] = 'Failed';
        $data['message'] = 'Parameters Missing!';
        
    }
    
    return $data;
}

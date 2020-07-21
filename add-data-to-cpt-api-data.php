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
 * 
 * Credits:
 * This Repository shows the code for the Youtube Video series https://www.youtube.com/playlist?list=PLNqG1qGUllk0npc2ZGPU358Q6S1itFCYR on the use of the WordPress REST API to handle Callback URLs from another server.
 * Have you have ever wanted to use your WordPress or its plugins to receive data from a callback URL or Webhooks?
 * In this video series, we explore how to use the WordPress REST API to receive the sent data from an External API, webhook URL or a callback URL. Then use the code to make it do a bunch of things for you.
 * In the following series, you will learn how to make an API or an API.
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
    $title    = $parameters['title'];
    $body     = $parameters['body'];
    
    if ( isset($name) && isset($password) ) {
        
        $userdata = get_user_by( 'login', $name );
        
        if ( $userdata ) {
            
            $wp_check_password_result = wp_check_password( $password, $userdata->user_pass, $userdata->ID );
            
            if ( $wp_check_password_result ) {
                $data['status'] = 'OK';
            
                $data['received_data'] = array(
                    'name'     => $name,
                    'password' => $password,
                    //'data'     => $userdata
                );
                
                $post_args = array(
                    'post_title'   => wp_strip_all_tags( $title ),
                    'post_content' => $body,
                    'post_status'  => 'publish',
                    'post_type'    => 'apidata'
                );
                
                $post_var = wp_insert_post( $post_args );
                
                if( $post_var ) {
                    $data['message'] = 'Post was successful';
                }
                
            } else {
                $data['status'] = 'OK';
                $data['message'] = 'You are not authenticated to login!';
            }
            
            
        } else {
            
            $data['status'] = 'OK';
            $data['message'] = 'The current user does not exist!';
        }
        
        
    } else {
        
        $data['status'] = 'Failed';
        $data['message'] = 'Parameters Missing!';
        
    }
    
    return $data;
}

function techiepress_set_up_post_type(){
    
    $args = array(
        'public'             => true,
        'publicly_queryable' => false,
        'label'              => __( 'API Data', 'prefix-plugin-name' ),
        'menu_icon'          => 'dashicons-analytics',
        'supports'           => array( 'title', 'editor' )
    );
    
    register_post_type( 'apidata', $args );
}

add_action( 'init', 'techiepress_set_up_post_type' );

<?php

if ( ! defined( 'ABSPATH' ) ) {
    die( 'No direct access!' );
}

if ( !class_exists('customApi') ):
    class customApi{
        /**
         * CustomApi constructor.
         */
        function single_user_api(){
            register_rest_route( 'pos/v1/', 'users/(?P<id>\d+)',array(
                'methods'               => WP_REST_Server::READABLE,
                'callback'              => array($this,'callback_user_api'),
                'permission_callback'   => array($this,'callback_user_api_permission')
            ));
        }

        function check_order_view(){
            register_rest_route( 'pos/v1/', 'order/view/(?P<id>\d+)',
                array(
                    array(
                        'methods'               => WP_REST_Server::READABLE,
                        'callback'              => array($this,'callback_order_view_api'),
                        'permission_callback'   => array($this,'callback_user_api_permission'),
                        'args' => [
                            'id'
                        ],
                    ),
                    array(
                        'methods'               => WP_REST_Server::EDITABLE,
                        'callback'              => array($this,'callback_post_order_view_api'),
                        'permission_callback'   => array($this,'callback_order_view_permission'),
                    ),
                )
            );
        }

        function count_notification(){
            register_rest_route( 'pos/v1/', 'notification/count',array(
                'methods'               => WP_REST_Server::READABLE,
                'callback'              => array($this,'callback_count_notification'),
                'permission_callback'   => array($this,'callback_user_api_permission')
            ));
        }

        /**
         * CustomApi callback_user_api_permission.
         */
        function callback_user_api_permission($request){
            return true;
        }

        /**
         * CustomApi Order View callback_user_api_permission.
         */
        function callback_order_view_permission($request){
            return true;
        }

        /**
         * CustomApi callback_user_api.
         */
        function callback_user_api($request){
            $get_id = $request['id'];
            $get_user = get_userdata($get_id);

            $user_arr = array();

            $user_arr['id']             = $get_user->ID;
            $user_arr['first_name']     = $get_user->first_name;
            $user_arr['last_name']      = $get_user->last_name;
            $user_arr['display_name']   = $get_user->display_name;
            $user_arr['user_nicename']  = $get_user->user_nicename;
            $user_arr['user_email']     = $get_user->user_email;
            $user_arr['role']           = $get_user->roles;
            $user_arr['shop_cur'] 		= get_woocommerce_currency_symbol( $currency );

            $response = new WP_REST_Response($user_arr);
            $response->set_status(200);

            return $response;
        }

        /**
         * CustomApi callback_order_view_api.
         */
        function callback_order_view_api($request){
            $get_id = $request['id'];
            $status = array();
            $status['view'] = get_post_meta( $get_id, '_view_status', true );
            $response = new WP_REST_Response($status);
            $response->set_status(200);

            return $response;
        }

        /**
         * CustomApi callback_post_order_view_api.
         */
        function callback_post_order_view_api($request){
            $parameters     = $request->get_params();
            $order_status   = $parameters['view'];
            $order_id       = $parameters['id'];
            $response       = array();
            $order_view     = get_post_meta( $order_id, '_view_status', true );
            if (get_post_status ( $order_id )):
                if(!empty($order_view)):
                    update_post_meta( $order_id, '_view_status', $order_status );
                    $response['view'] = get_post_meta( $order_id, '_view_status', true );
                    return new WP_REST_Response($response,200);
                else:
                    if ($order_view == 0):
                            update_post_meta( $order_id, '_view_status', $order_status );
                            $response['view'] = get_post_meta( $order_id, '_view_status', true );
                            return new WP_REST_Response($response,200);
                        else:
                            return new WP_Error( '404', __( 'Order View Status Not Created for the Order Id', 'cpos' ) );
                    endif;
                endif;
            else:
                return new WP_Error( '404', __( 'No Order Found with the Order Id', 'cpos' ) );
            endif;

        }

        function callback_count_notification($request){
            $query = new WC_Order_Query( array(
                'limit'        => -1,
                'meta_key'     => '_view_status',
                'meta_value'   => 0
            ) );
            $orders = $query->get_orders();

            if ($orders):
                $data           = array();
                $data['count']  = count($orders);
                $response = new WP_REST_Response($data);
                $response->set_status(200);
                return $response;
            else:
                return new WP_Error( '404', __( 'Woocommerce no activated or installed', 'cpos' ) );
            endif;
        }
    }
endif;
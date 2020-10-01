<?php

if ( ! defined( 'ABSPATH' ) ) {
    die( 'No direct access!' );
}

if ( !class_exists('CustomFunction') ):
    class CustomFunction{
        /**
         * CustomFunction Register All Hook.
         */
        function register_hook() {
            add_action( 'rest_api_init' ,               array( &$this, 'single_user_api_hook'));
            add_action( 'rest_api_init' ,               array( &$this, 'order_view_api_hook'));
            add_action( 'rest_api_init' ,               array( &$this, 'order_view_notification_api_hook'));
            add_action( 'rest_api_init' ,               array( &$this, 'order_login_api_hook'));
            add_action( 'wp_enqueue_scripts' ,          array( &$this, 'custom_style_hook' ));
            add_action( 'admin_enqueue_scripts' ,       array( &$this, 'admin_style_hook' ));
            add_action( 'woocommerce_thankyou' ,        array( &$this, 'socket_notify'), 10, 1);
            add_action( 'admin_menu' ,                  array( &$this, 'admin_menu_hook'));
            add_action( 'admin_footer' ,                array( &$this, 'add_nonce'));
            add_action( 'wp_footer' ,                   array( &$this, 'add_option_to_footer'));
            add_action( 'wp_ajax_save_option',          array( &$this, 'admin_save_option'));
            add_action( 'wp_ajax_nopriv_save_option' ,  array( &$this, 'admin_save_option'));
            add_filter( 'jwt_auth_whitelist' ,          array( &$this, 'jwt_whitelist'));
            add_filter( 'woocommerce_rest_prepare_shop_order_object' , array( &$this, 'prefix_wc_rest_prepare_order_object'),10,3);
            add_filter( 'woocommerce_api_order_response' , array( &$this, 'prefix_wc_api_order_response'),10,1);
        }

        /**
         * Hook Custom Style.
         */
        function custom_style_hook(){
            wp_enqueue_script('sweetalert2', 'https://cdn.jsdelivr.net/npm/sweetalert2@10.0.2/dist/sweetalert2.all.min.js',array('jquery'),'2.0.0',true );
            wp_enqueue_script('socket', plugins_url( 'assets/js/socket.io.js', __DIR__ ),array('jquery'),'2.3.0',true );
            wp_enqueue_script('_s_app', plugins_url( 'assets/js/app.js', __DIR__ ),array('jquery'),'1.0.0',true );
            wp_register_style( 'sweetalert2-style', 'https://cdn.jsdelivr.net/npm/sweetalert2@10.0.2/dist/sweetalert2.min.css', false, '1.0.0' );
            wp_enqueue_style( 'admin-style' );
        }

        /**
         * Hook Admin Style.
         */
        function admin_style_hook(){
            wp_enqueue_script('sweetalert2', 'https://cdn.jsdelivr.net/npm/sweetalert2@10.0.2/dist/sweetalert2.all.min.js',array('jquery'),'2.0.0',true );
            wp_enqueue_script('admin-script', plugins_url( 'assets/js/admin-script.js', __DIR__ ),array('jquery'),'1.0.0',true );
            wp_register_style( 'sweetalert2-style', 'https://cdn.jsdelivr.net/npm/sweetalert2@10.0.2/dist/sweetalert2.min.css', false, '1.0.0' );
            wp_register_style( 'admin-style', plugins_url( 'assets/css/admin-style.css', __DIR__ ), false, '1.0.0' );
            wp_enqueue_style( 'sweetalert2-style' );
            wp_enqueue_style( 'admin-style' );
        }

        /**
         * Add Options To The Footer.
         */
        function add_option_to_footer(){
            if (get_option('pos_option')):
                $get_option = get_option('pos_option');
                ?>
                <script type="text/javascript">
                    /* <![CDATA[ */
                        let rdata = {
                            'id':"<?=$get_option['pos_r_id']?>",
                            'status':"<?=$get_option['pos_r_status']?>"
                        };
                    /* ]]> */
                </script>
                <?php
            endif;
        }


        /**
         * Add Admin Ajax Nonce.
         */
        function add_nonce(){
            ?>
                <script type="text/javascript">
                    /* <![CDATA[ */
                    let cPosSecurity = "<?=wp_create_nonce( "cPos-nonce" )?>";
                    let ajaxUrl = "<?=admin_url('admin-ajax.php')?>";
                    /* ]]> */
                </script>
            <?php
        }

        /**
         * Hook Single User Api.
         */
        function single_user_api_hook(){
            $api = new customApi();
            $api->single_user_api();
        }

        /**
         * Hook Order View Api.
         */
        function order_view_api_hook(){
            $api = new customApi();
            $api->check_order_view();
        }

        /**
         * Hook Order Login Api.
         */
        function order_login_api_hook(){
            $api = new customApi();
            $api->register_login_api_hooks();
        }

        /**
         * Hook Order View Notification Api.
         */
        function order_view_notification_api_hook(){
            $api = new customApi();
            $api->count_notification();
        }

        /**
         * Hook Single User Api.
         */
        function socket_notify($order_id){
            if( empty( get_post_meta( $order_id, '_view_status', true ) ) ):
                add_post_meta( $order_id, '_view_status', 0, true );
                $socket = new socketNotification();
                $socket->fire_socket($order_id);
            endif;
        }

        /**
         * Hook Admin Menu Page.
         */
        function admin_menu_hook(){
            $_admin = new AdminMenu();
            $_admin->setup_menu();
        }

        /**
         * Hook Ajax Store Pos Option Data.
         */
        function admin_save_option(){
            //$_admin_option = new StorePosOptions();
            //$_admin_option->save_lc_id();

            check_ajax_referer( 'cPos-nonce', 'cPosSecurity' );

            $args = array(
                'po_r_lc'       => $_POST['rLc'],
                'pos_r_id'      => $_POST['rId'],
                'pos_r_status'  => $_POST['rStatus'],
            );

            if( !get_option('pos_option') ){
                $opt = add_option( 'pos_option', $args);
            }else{
                $opt = update_option( 'pos_option', $args);
            }

            if ($opt){
                echo wp_json_encode(array(
                    'success'=>'Save successfully !',
                    'data' => $args
                ));
            }else{
                echo wp_json_encode(array(
                    'statusText'=>'Oops, Something went wrong !',
                    'data' => $args
                ));
            }
            wp_die();
        }

        function jwt_whitelist($endpoints){
            return array(
                '/wp-json/pos/v1/order/view/*',
                '/wp-json/pos/v1/notification/count/',
                '/wp-json/pos/v1/user/login/',
            );
        }

        function prefix_wc_rest_prepare_order_object( $response, $object, $request ) {
            if (empty(get_post_meta($object->get_id(), '_view_status', true))){
                $view_meta_field = "";
            }else{
                $view_meta_field = get_post_meta($object->get_id(), '_view_status', true);
            }
            $response->data['order_view'] = $view_meta_field;
            return $response;
        }

        function prefix_wc_api_order_response( $order_data ) {
            if (empty(get_post_meta($order_data['id'], '_view_status', true))){
                $view_meta_field = "";
            }else{
                $view_meta_field = get_post_meta($order_data['id'], '_view_status', true);
            }
            $order_data['order_view'] = $view_meta_field;
            return $order_data;
        }

    }

endif;
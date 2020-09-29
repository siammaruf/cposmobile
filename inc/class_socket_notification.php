<?php

if ( ! defined( 'ABSPATH' ) ) {
    die( 'No direct access!' );
}

if ( !class_exists('socketNotification') ):
    class socketNotification{

        function fire_socket($order_id){
            // Get an instance of the WC_Order object
            $query = new WC_Order_Query( array(
                'limit'        => -1,
                'meta_key'     => '_view_status',
                'meta_value'   => 0
            ) );
            $orders = $query->get_orders();

            $order = wc_get_order( $order_id );
            $data = $order->get_data();
            $order_total = $order->get_total();
            $get_o = get_option('pos_option');
            if ($get_o):
                if ($get_o['pos_r_status'] == 'true'):
                echo '<script type="text/javascript">
                        /* <![CDATA[ */
                        let _p = {
                            "prId": "'.$get_o['pos_r_id'].'",
                            "oId": "'.$order_id.'",
                            "currency": "'.$data['currency'].'",
                            "orderAmount": "'.$order_total.'",
                            "orderDate": "'.$data['date_created']->date('Y-m-d H:i:s').'",
                            "notify": "'.count($orders).'",
                        }
                        /* ]]> */
                    </script>';
                endif;
            endif;
        }

        function socket_update(){
            $get_o = get_option('pos_option');
            $query = new WC_Order_Query( array(
                'limit'        => -1,
                'meta_key'     => '_view_status',
                'meta_value'   => 0
            ) );
            $orders = $query->get_orders();
            if ($get_o):
                if ($get_o['pos_r_status'] == 'true'):
                    echo '<script type="text/javascript">
                        /* <![CDATA[ */
                        let socketIo = io("https://cposnotify.herokuapp.com");
                        let _nu = {
                            "prId": "'.$get_o['pos_r_id'].'",
                            "notify": "'.count($orders).'",
                        }
                        socketIo.emit("_nUpdate", _nu);
                        /* ]]> */
                    </script>';
                endif;
            endif;
        }
        
    }
endif;
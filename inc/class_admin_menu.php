<?php

if ( ! defined( 'ABSPATH' ) ) {
    die( 'No direct access!' );
}

if ( !class_exists('AdminMenu') ):
    class AdminMenu{
        function setup_menu(){
            add_menu_page(
                __( 'Combosoft Pos', 'combopos' ),
                'Combosoft Pos',
                'manage_options',
                'combo_pos',
                array(&$this,'admin_init'),
                'dashicons-buddicons-topics',
                10
            );
        }

        function admin_init(){
            ?>
            <div class="wrap cpos-plugin-wrap">
                <div class="title">
                    <h1>Como-Soft POS Mobile Application Integration and Licence</h1>
                </div>
                <div class="container">
                    <!-- Tabs -->
<!--                    <div class="tab-header-wrap">-->
<!--                        <a href="#" class="cpos-tab active" data-toggle-target=".cpos-tab-content-1">License</a>-->
<!--                        <a href="#" class="cpos-tab" data-toggle-target=".cpos-tab-content-2">Settings</a>-->
<!--                    </div>-->
                    <!-- Content -->
                   <div class="tab-content-wrap">
                       <div class="cpos-tab-content cpos-tab-content-1 active">
                           <h1>Restaurant License</h1>
                           <form class="cpos-forms" id="check-lc">
                               <div class="field-group">
                                   <label for="cpos-lc">Please, Enter your license key to active your restaurant.</label>
                                   <?php if( !get_option('pos_option') ):?>
                                        <input type="text" name="cpos-lc" id="cpos-lc" placeholder="Your license key">
                                   <?php else:?>
                                        <?php $get_pos_apt = get_option('pos_option');?>
                                        <input type="text" name="cpos-lc" value="<?=$get_pos_apt['po_r_lc']?>" id="cpos-lc" placeholder="Your license key">
                                   <?php endif;?>
                               </div>
                               <div class="field-group">
                                   <button id="btn-action-lc" class="input-btn">Check and Save</button>
                               </div>
                           </form>
                       </div>
<!--                       <div class="cpos-tab-content cpos-tab-content-2">-->
<!--                           <h1>Restaurant General Settings</h1>-->
<!--                           <form class="cpos-forms" id="check-lc">-->
<!--                               <div class="field-group">-->
<!--                                   <label for="cpos-lc">Please, Enter your restaurant ID.</label>-->
<!--                                   <input type="text" name="cpos-lc" id="cpos-lc" placeholder="Your Restaurant ID">-->
<!--                               </div>-->
<!--                               <div class="field-group">-->
<!--                                   <button id="btn-action-lc" class="input-btn">Save</button>-->
<!--                               </div>-->
<!--                           </form>-->
<!--                       </div>-->
                   </div>
                </div>
            </div>
            <?php
        }
    }
endif;
<?php
/*
Plugin Name: Bubuti Donation
Plugin URI: https://www.bubuti.com/
Description: Allows you to share Acts from Bubuti on your website via shortcode
Version: 1.0
Author: Bubuti
Author URI: https://www.bubuti.com/
*/

/**
 * Copyright (c) 2014 Bubuti
 * This file is part of Bubuti Donation.
 *
 * Bubuti Donation is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 2 of the License, or
 * (at your option) any later version.
 *
 * Bubuti Donation is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Bubuti Donation.  If not, see <http://www.gnu.org/licenses/>.
 *
 */

if (!class_exists('sw_Bubuti_Share')) {
    class sw_Bubuti_Share {

        public function __construct() {

            // Set location values
            $this->path = untrailingslashit( plugin_dir_path( __FILE__ ) );
            $this->url  = untrailingslashit( plugin_dir_url( __FILE__ ) );

            // Set default values
            $this->settings = array();
            $this->get_settings();

            // Hook in where necessary
            add_action( 'admin_enqueue_scripts', array( &$this, 'register_admin_scripts_styles' ) );
            add_action( 'wp_enqueue_scripts', array( &$this, 'register_public_scripts_styles' ) );
            add_action( 'admin_menu', array( &$this, 'add_settings_page' ) );
            add_action( 'add_meta_boxes', array( &$this, 'add_meta_boxes' ) );
            add_action( 'save_post', array( &$this, 'save_meta_boxes' ) );
            add_filter( 'the_content', array( &$this, 'add_button_to_post' ) );
            add_filter( 'plugin_action_links_' . plugin_basename(__FILE__), array( &$this, 'add_settings_link' ) );

        }

        /**
         * Retrieves settings from _options table
         * @return void
         */
        private function get_settings() {

            $this->settings = unserialize( get_site_option( 'bubuti-act-share-settings' ) );
            if ( empty($this->settings) ) {
                $this->settings['default_act_id'] = '';
                $this->settings['button_color']   = '';
                $this->settings['post_types']     = array();
                $this->settings['placement']      = array( 'above' => '', 'below' => '', 'left' => '', 'right' => '' );
            }

            return;

        }

        /**
         * Adds settings page to WordPress administration area
         * @return void
         */
        public function add_settings_page() {

            add_submenu_page('options-general.php', 'Bubuti Donation', 'Bubuti Donation', 'manage_options', 'sw-bshare-settings', array( &$this, 'settings_page' ) );

            return;

        }

        /**
         * Adds `Settings` link on the plugin item in the plugins list
         * @param array  $links Collection of links
         * @return array $links Collection of links
         */
        public function add_settings_link( $links ) {
            $settings_link = '<a href="options-general.php?page=sw-bshare-settings">Settings</a>';
            array_unshift($links, $settings_link);

            return $links;

        }

        /**
         * Renders settings page
         * @return void
         */
        public function settings_page() {

            if ( isset($_POST['save-bubuti-donation-settings']) ) {
                check_admin_referer( 'save-bubuti-donation-settings' );

                $this->settings['default_act_id'] = ( isset( $_POST['bubuti-share-act-id'] ) && $_POST['bubuti-share-act-id'] != '' ) ? $_POST['bubuti-share-act-id'] : '';
                $this->settings['button_color'] = ( isset( $_POST['bubuti-act-btn-color'] ) && $_POST['bubuti-act-btn-color'] != '' ) ? $_POST['bubuti-act-btn-color'] : '';
                $this->settings['post_types'] = ( isset( $_POST['act-post-type'] ) && $_POST['act-post-type'] != '' ) ? $_POST['act-post-type'] : array();

                $this->settings['placement']['topLeft'] = ( isset($_POST['act-placement-top-left'] ) && $_POST['act-placement-top-left'] ) ? true : false;
                $this->settings['placement']['topCenter'] = ( isset($_POST['act-placement-top-center'] ) && $_POST['act-placement-top-center'] ) ? true : false;
                $this->settings['placement']['bottomLeft'] = ( isset($_POST['act-placement-bottom-left'] ) && $_POST['act-placement-bottom-left'] ) ? true : false;
                $this->settings['placement']['bottomCenter'] = ( isset($_POST['act-placement-bottom-center'] ) && $_POST['act-placement-bottom-center'] ) ? true : false;

                $this->save_settings();
            }

            $registered_post_types = $this->get_public_post_types();
            require_once( 'form-settings.php' );

            return;

        }

        /**
         * Save settings to _options table
         * @return void
         */
        private function save_settings() {

            update_site_option( 'bubuti-act-share-settings', serialize( $this->settings ) );

            return;

        }

        /**
         * Adds meta boxes to add/edit post screen for desired post types
         * @return void
         */
        public function add_meta_boxes() {

           if ( ! empty( $this->settings['post_types'] ) ) {
                foreach( $this->settings['post_types'] as $post_type ) {
                    add_meta_box( 'bubuti-share-act-post-settings', 'Bubuti Donation', array( &$this, 'post_meta_boxes' ), $post_type, 'normal' );
                }
            }

            return;

        }

        /**
         * Renders meta box content
         * @return void
         */
        public function post_meta_boxes() {

            global $post;

            wp_nonce_field( 'bubuti_share_act_meta_box', 'bubuti_share_act_meta_box_nonce' );
            require_once('form-meta-boxes.php');

            return;

        }

        /**
         * Saves content entered in meta boxes when post is saved
         * @param int   $post_id    ID of the post being saved
         * @return void
         */
        public function save_meta_boxes( $post_id ) {

            // Check if our nonce is set.
            if ( ! isset( $_POST['bubuti_share_act_meta_box_nonce'] ) ) {
                return;
            }

            // Verify that the nonce is valid.
            if ( ! wp_verify_nonce( $_POST['bubuti_share_act_meta_box_nonce'], 'bubuti_share_act_meta_box' ) ) {
                return;
            }

            // If this is an autosave, our form has not been submitted, so we don't want to do anything.
            if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
                return;
            }

            // Check the user's permissions.
            if ( isset( $_POST['post_type'] ) && in_array( $_POST['post_type'], $this->settings['post_types'] ) ) {
                foreach( $this->settings['post_types'] as $post_type ) {
                    if ( ! current_user_can( 'edit_' . $post_type, $post_id ) ) {
                        return;
                    }
                }
            }

            // Make sure fields have values
            if ( ! isset( $_POST['act-enabled-yes'] ) && ! isset ( $_POST['act-enabled-no'] ) ) {
                return;
            } else {
                $act_sharing_status = ( sanitize_text_field( $_POST['act-enabled-yes'] ) == '' ) ? 'disabled' : 'enabled';
            }

            update_post_meta( $post_id, '_bubuti_act_sharing_id', sanitize_text_field( $_POST['bubuti-share-act-id'] ) );
            update_post_meta( $post_id, '_bubuti_act_sharing_status', $act_sharing_status );

            return;

        }

        /**
         * Adds CSS and JS as necessary to the WordPress administration area
         * @return void
         */
        public function register_admin_scripts_styles() {

            wp_register_script( 'sw-bshare-admin-scripts', $this->url . '/js/admin-scripts.js', array( 'jquery' ), '20140811' );
            wp_enqueue_script( 'sw-bshare-admin-scripts' );

            wp_register_style( 'sw-bshare-admin-styles', $this->url . '/css/admin-styles.css', null, '20140811' );
            wp_enqueue_style( 'sw-bshare-admin-styles' );

            return;

        }

        /**
         * Adds CSS and JS as necessary to public facing pages
         * @return void
         */
        public function register_public_scripts_styles() {

            wp_register_style( 'sw-bshare-public-styles', $this->url . '/css/styles.css', null, '20140625' );
            wp_enqueue_style( 'sw-bshare-public-styles' );

            return;

        }

        /**
         * Adds donation button to appropriate location in post content
         * @param string $content Content supplied by WordPress to be filtered
         * @return string Modified content with button placed in it
         */
        public function add_button_to_post( $content ) {
            global $post;

            if ( trim( $content ) == '' ) { return $content; }

            // Find out if donation button is enabled for this post or not, if not then return unmodified content ASAP
            $donation_enabled = get_post_meta( $post->ID, '_bubuti_act_sharing_status', true );
            if ( $donation_enabled != 'enabled' ) { return $content; }

            $act_id = get_post_meta( $post->ID, '_bubuti_act_sharing_id', true );       // Try to get Act ID specified by the individual post
            if ( $act_id == '' ) { $act_id = $this->settings['default_act_id']; }       // If no Act ID was found on the post, try using the default Act ID
            if ( $act_id == '' ) { return $content; }                                   // If still no Act ID was found, return the unmodified content

            $placement = '';
            $placement = ( $this->settings['placement']['topLeft'] ) ? 'topLeft' : $placement;
            $placement = ( $this->settings['placement']['topCenter'] ) ? 'topCenter' : $placement;
            $placement = ( $this->settings['placement']['bottomLeft'] ) ? 'bottomLeft' : $placement;
            $placement = ( $this->settings['placement']['bottomCenter'] ) ? 'bottomCenter' : $placement;

            if ( $placement == '' ) { return $content; }                        // If no placement can be figured out then return the unmodified content
            if ( $this->settings['button_color'] == '' ) { return $content; }   // If no button color is set then return the unmodified content

            // Compose the HTML for the button
            $button_html = '<a target="_blank" class="btn-bubuti-donate btn-bubuti-donate-color-' . $this->settings['button_color'] . ' btn-bubuti-donate-placement-' . $placement . '" href="https://www.bubuti.com/acts/' . $act_id . '">Click to Donate</a>';

            // Place button before or after content as appropriate based on settings
            switch ( $placement ) {
                case 'topLeft':
                case 'topCenter':
                    $content = $button_html . $content;
                    break;
                case 'bottomLeft':
                case 'bottomCenter':
                    $content = $content . $button_html;
                    break;
                default:
                    break;
            }

            return $content;

        }

        /**
         * Retrieves currently defined post types from WordPress
         * @return array    $return_types   Registered post types
         */
        private function get_public_post_types() {

            $post_type_blacklist = array('attachment');

            $args = array(
                'public' => true
            );
            $registered_post_types = get_post_types( $args );

            foreach( $registered_post_types as $post_type ) {
                if ( ! in_array( $post_type, $post_type_blacklist ) ) {
                    $return_types[] = $post_type;
                }
            }

            return $return_types;

        }

    }
}

if ( ! @$swBubutiShare && function_exists( 'add_action' )) { $swBubutiShare = new sw_Bubuti_Share(); } // Create object if needed

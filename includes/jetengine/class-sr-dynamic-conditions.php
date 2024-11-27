<?php

add_action( 'jet-engine/modules/dynamic-visibility/conditions/register', function( $conditions_manager ) {

    class Referral_User_Active_Condition extends \Jet_Engine\Modules\Dynamic_Visibility\Conditions\Base {

        /**
         * Returns condition ID
         *
         * @return string
         */
        public function get_id() {
            return 'referral-user-active';
        }

        /**
         * Returns condition name
         *
         * @return string
         */
        public function get_name() {
            return __( 'Referral User Active', 'smart-referrals' );
        }

        /**
         * Returns group for current operator
         *
         * @return string
         */
        public function get_group() {
            return 'user';
        }

        /**
         * Check condition by passed arguments
         *
         * @param  array $args
         * @return bool
         */
        public function check( $args = array() ) {

            if ( ! is_user_logged_in() ) {
                return false;
            }

            $user_id = get_current_user_id();
            $active_status = get_user_meta( $user_id, 'active_status', true );

            return $active_status === 'active';
        }

        /**
         * Check if is condition available for meta fields control
         *
         * @return boolean
         */
        public function is_for_fields() {
            return false;
        }
    }

    class Referral_User_Inactive_Condition extends \Jet_Engine\Modules\Dynamic_Visibility\Conditions\Base {

        /**
         * Returns condition ID
         *
         * @return string
         */
        public function get_id() {
            return 'referral-user-inactive';
        }

        /**
         * Returns condition name
         *
         * @return string
         */
        public function get_name() {
            return __( 'Referral User Inactive', 'smart-referrals' );
        }

        /**
         * Returns group for current operator
         *
         * @return string
         */
        public function get_group() {
            return 'user';
        }

        /**
         * Check condition by passed arguments
         *
         * @param  array $args
         * @return bool
         */
        public function check( $args = array() ) {

            if ( ! is_user_logged_in() ) {
                return false;
            }

            $user_id = get_current_user_id();
            $active_status = get_user_meta( $user_id, 'active_status', true );

            return $active_status === 'inactive';
        }

        /**
         * Check if is condition available for meta fields control
         *
         * @return boolean
         */
        public function is_for_fields() {
            return false;
        }
    }

    // Register conditions
    $conditions_manager->register_condition( new Referral_User_Active_Condition() );
    $conditions_manager->register_condition( new Referral_User_Inactive_Condition() );

} );

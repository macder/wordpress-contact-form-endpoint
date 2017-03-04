<?php defined( 'ABSPATH' ) or die();
/*
Plugin Name: Contact Form Endpoint
Plugin URI:  https://github.com/macder/wp-contact-form-endpoint
Description: Simple api endpoint to post data from a contact form. Intended for developers
Version:     0.0.1
Author:      Maciej Derulski
Author URI:  https://derulski.com
License:     GPL3
License URI: https://www.gnu.org/licenses/gpl-3.0.html
*/

class Contact_Form_Endpoint {

  function __construct() {
    $this->add_actions();
  }

  /**
   * creates the action hooks for contact form post
   *
   * @since 0.0.1
   * @access private
   */
  private function add_actions() {
    add_action( 'admin_post_nopriv_contact_form', array( $this, 'post_entry' ) );
    add_action( 'admin_post_contact_form', array( $this, 'post_entry' ) );
  }

  /**
   * callback for contact_form post action
   *
   * prepares $_POST data for sanitation and validation
   *
   * @since 0.0.1
   * @access private
   */
  private function post_entry() {

  }

}
 
$Contact_Form_Endpoint = new Contact_Form_Endpoint();

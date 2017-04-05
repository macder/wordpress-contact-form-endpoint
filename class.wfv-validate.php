<?php defined( 'ABSPATH' ) or die();

/**
 * Performs the input validation
 *
 * Uses Valitron to validate form
 * If form validates, an action is triggered
 *
 * @since 0.2.0
 * @since 0.6.0 renamed from Form_Validate_Post
 */
class WFV_Validate {

  /**
   * Form identifier
   *
   * @since 0.1.0
   * @access public
   * @var string $action
   */
  public $action;

  /**
   * Validation rules
   *
   * @since 0.1.0
   * @access public
   * @var array $rules Form validation rules.
   */
  public $rules = array();

  /**
   * Error message overrides
   *
   * @since 0.4.0
   * @access public
   * @var array $messages The field/rule paired messages.
   */
  public $messages = array();

  /**
   * User input from failed validation
   *
   * @since 0.2.1
   * @access public
   * @var array $input Form validation rules.
   */
  public $input = array();

  /**
   * Result from wp_nonce_field()
   *
   * @since 0.3.0
   * @access public
   * @var string $nonce_field WP rendered nonce field.
   */
  public $nonce_field;



  /**
   * __construct
   *
   * @since 0.2.0
   * @param Object $form The validation properties
   *
   */
  function __construct() {

  }

  /**
   * Do the validation
   *
   * @since 0.2.0
   * @since 0.6.0 Public access
   */
  public function validate() {
    $this->validate_nonce();
    $v = $this->create_valitron();

    if ( $v->validate() ) {
      do_action( $this->action, $this->input );
    } else {
      $this->errors = $v->errors();
    }
  }

  /**
   * Verify the nonce
   * Prevents CSFR exploits
   *
   * @since 0.2.2
   * @param string $action
   * @access private
   */
  protected function validate_nonce() {
    $nonce = $_REQUEST[ $this->action.'_token' ];
    if ( ! wp_verify_nonce( $nonce, $this->action ) ) {
      die( 'invalid token' );
    }
  }

  /**
   * Sanitize input and keys in $_POST
   * Assign the sanitized data to $sane_post property
   *
   * @since 0.2.0
   * @since 0.6.0 Public access
   */
  public function sanitize_post() {
    foreach ( $_POST as $key => $value ) {
      $this->input[ sanitize_key( $key ) ] = sanitize_text_field( $value );
    }
  }


  /**
   * Create an instance of Valitron\Validator with our rules / messages
   * Assign to $valitron property
   *
   * @since 0.2.0
   * @param array $form Form configuration array
   */
  public function create_valitron() {
    $valitron = new Valitron\Validator( $this->input );
    $rules_to_map = $this->custom_message_rules($valitron);
    $valitron->mapFieldsRules( $rules_to_map );
    return $valitron;
  }

  /**
   * Check for custom error messages in $form config
   * Add rules with custom messages individually to Valitron
   *
   * @since 0.5.0
   *
   * @param array $form Form configuration array
   * @return array Form rules that didn't have custom messages
   */
   private function custom_message_rules($valitron) {
    $rule_set = $this->rules;
    $msg_set = $this->messages;

    foreach( $rule_set as $field => $rules ) {
      // check if this field has any custom error msgs
      if ( array_key_exists( $field, $msg_set ) ) {
        foreach ( $msg_set[ $field ] as $rule => $message ) {
          // add the rule with custom error message
          $valitron->rule( $rule, $field )->message( $message );
          // remove the rule from $lorem config or it will get mapped again later
          $key = array_search( $rule, $rules );
          unset( $rule_set[ $field ][ $key ] );
        }
      }
    }
    return $rule_set;
  }
}
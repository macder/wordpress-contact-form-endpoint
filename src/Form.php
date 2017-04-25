<?php
namespace WFV;
defined( 'ABSPATH' ) or die();

/**
 *
 *
 * @since 0.8.0
 */
class Form implements ValidationInterface {
  /**
   * Form identifier
   *
   * @since 0.1.0
   * @access protected
   * @var string $action
   */
  protected $action;

  /**
   * Error message bag
   *
   * @since 0.6.1
   * @since 0.7.3 WFV\Errors instance
   * @access protected
   * @var class $errors Instance of WFV_Errors.
   */
  protected $errors;

  /**
   * User input
   *
   * @since 0.2.1
   * @since 0.7.2 WFV_Input instance
   * @access protected
   * @var class $input Instance of WFV\Input.
   */
  protected $input;

  /**
   * Error message overrides
   *
   * @since 0.4.0
   * @since 0.7.0 WFV\Messages instance
   * @access protected
   * @var class $messages Instance of WFV_Messages.
   */
  protected $messages;

  /**
   * Validation rules
   *
   * @since 0.1.0
   * @since 0.7.0 WFV\Rules instance
   * @access protected
   * @var class $rules Instance of WFV_Rules.
   */
  protected $rules;

  /**
   * CSFR token
   * Token generated by wp_nonce()
   *
   * @since 0.8.0
   * @access protected
   * @var string $token Token value from wp_nonce()
   */
  protected $token;

  /**
   *
   *
   * @since 0.9.1
   * @access private
   * @var
   */
  // private $validator;


  use AccessorTrait;
  use MutatorTrait;

  /**
   * __construct
   *
   * @since 0.9.1 Moved from WFV\Validator
   *
   * @param string
   * @param WFV\Rules $rules
   * @param WFV\Input $input
   * @param WFV\Messages $messages
   * @param WFV\Errors $errors
   *
   */
  function __construct( $action, Input $input, Rules $rules, Messages $messages, Errors $errors ) {
    $properties = array(
      'action' => $action,
      'rules' => $rules,
      'messages' => $messages,
      'input' => $input,
      'errors' => $errors,
      'token' => wp_create_nonce( $action ),
    );
    $this->set( $properties );
  }

  /**
   * Convenience method to repopulate checkbox or radio.
   * Returns 'checked' string if field has value in POST.
   *
   * @since 0.8.5
   *
   * @param string $field Field name.
   * @param string $value Value to compare against.
   * @return string|null
   */
  public function checked_if( $field, $value ) {
    return ( $this->input->contains( $field, $value ) ) ? 'checked' : null;
  }

  /**
   * TEMP - upcoming re-work
   *
   * Returns markup for required hidden fields
   * Makes theme file cleaner
   *
   * @since 0.8.0
   */
  public function get_token_fields() {
    // TODO - Move markup into a view
    $token_name = $this->action . '_token';

    echo $nonce_field = wp_nonce_field( $this->action, $token_name, false, false );
    echo $action_field = '<input type="hidden" name="action" value="'. $this->action .'">';
  }

  /**
   * Check if input action is for this instance.
   *
   * @since 0.8.0
   *
   * @return bool True if $this->action is $input->action and nonce is valid.
   */
  public function must_validate() {
    if( $this->has_request_action() ) {
      return ( $this->is_legal( $this->input->action ) ) ? true : false;
    }
    return false;
  }

  /**
   * Convenience method to repopulate select dropdown.
   * Returns 'selected' string if field has value in POST.
   *
   * @since 0.8.6
   *
   * @param string $field Field name.
   * @param string $value Value to compare against.
   * @return string|null
   */
  public function selected_if( $field, $value ) {
    return ( $this->input->contains( $field, $value ) ) ? 'selected' : null;
  }

  /**
   * Check if $this->input has action property
   *
   * @since 0.8.0
   * @since 0.9.1 Moved from WFV/Validator
   * @access protected
   *
   * @return bool
   */
  protected function has_request_action() {
    return ( $this->input->has('action') ) ? true : false;
  }

  /**
   * Safety method.
   * Verifies if the input action matches action on this instance.
   * Very unlikely to get false, unless sneaky things happening...
   *
   * @since 0.8.0
   * @since 0.9.1 Moved from WFV/Validator
   * @access protected
   *
   * @param string $action String to compare against $this->action.
   * @return bool
   */
  protected function is_legal( $action ) {
    return ( $action === $this->action ) ? true : false;
  }
}

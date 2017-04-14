<?php
namespace WFV;
defined( 'ABSPATH' ) or die();

/**
 *
 *
 * @since 0.8.0
 */
class Messages implements ValidationInterface {

  use AccessorTrait;
  use MutatorTrait;

  /**
   * __construct
   *
   * @since 0.8.0
   *
   */
  function __construct( $messages ) {
    if( $messages ) {
      $this->set( $messages );
    }
  }

  /**
   * Check if a fields rule has custom message
   *
   * @since 0.7.0
   *
   * @param string $field
   * @param string $rule
   *
   * @return bool
   */
  public function exist( $field, $rule ) {
    if( property_exists( $this, $field ) && array_key_exists( $rule, $this->$field ) ) {
      $exists = true;
    }
    return ( $exists ) ? true : false;
  }
}

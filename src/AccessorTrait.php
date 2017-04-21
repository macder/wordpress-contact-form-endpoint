<?php
namespace WFV;
defined( 'ABSPATH' ) or die();

/**
 * Generic property getters
 *
 * @since 0.8.0
 */
trait AccessorTrait {

  /**
   * __get magic method
   *
   * @since 0.8.0
   *
   * @param string $property
   * @return mixed
   */
  public function __get( $property ) {
    return ( true === $this->has( $property ) ) ? $this->$property : null;

    // stripcslashes(); is_string
    /*if( $this->has( $property ) ) {
      return ( is_string( $this->$property ) ) ? stripslashes( $this->$property ) : $this->$property;
    }
    return null;*/
  }

  /**
   *
   *
   * @since 0.8.6
   *
   * @return bool
   */
  public function contains( $property, $value = null ) {
    // WIP
    if ( is_array( $this->$property ) ) {
      return ( in_array( $value, $this->$property ) ) ? true : false;
    }
    return ( $this->$property === $value ) ? true : false;
  }

  /**
   * Get array of instance properties
   *
   * @since 0.7.2
   *
   * @return array Associative array, key/value pairs of instance props.
   */
  public function get_array() {
    return get_object_vars( $this );
  }

  /**
   * Check if property exists
   *
   * @since 0.8.0
   *
   * @param string $property
   * @return bool
   */
  public function has( $property ) {
    return ( true === property_exists( $this, $property ) ) ? true : false;
  }

}

<?php

namespace WFV;

use WFV\Rules;

class RulesTest extends \PHPUnit_Framework_TestCase {

  /**
   *
   *
   * @access protected
   * @var
   */
  protected static $rules;

  /**
   *
   *
   */
  public static function setUpBeforeClass() {
    self::$rules = array(
      'first_name' => ['required', 'alpha'],
      'last_name' => ['required', 'alpha'],
      'phone'      => ['required', 'custom:phone'],
      'username'   => ['required'],
      'password'   => ['required'],
      'email'      => ['email','required'],
      'website'    => ['required', 'url'],
      'postal'     => ['custom:postal_code'],
    );
  }

  /**
   *
   *
   */
  public static function tearDownAfterClass() {

  }

  /**
   * Are the rules array keys set as property names?
   *
   */
  public function test_rules_get_set_as_properties() {
    $rules = new Rules( self::$rules );

    foreach( $rules as $field => $ruleset ) {
      $this->assertTrue( property_exists( $rules, $field ) );
    }
  }

  /**
   * Is the ruleset for each field assigned from the rules array
   *  to the field property?
   *
   */
  public function test_rules_if_field_has_rules() {
    $rules = new Rules( self::$rules );

    foreach( $rules as $field => $ruleset ) {
      foreach( $ruleset as $index => $rule ) {
        $has_rule = ( $rule === self::$rules[$field][$index] ) ? true : false;
        $this->assertTrue( $has_rule );
      }
    }
  }

  /**
   * Does is_custom return true if rule is custom?
   *
   */
  public function test_rule_is_custom_returns_true() {
    $rules = new Rules( self::$rules );
    $this->assertTrue( $rules->is_custom('custom:phone') );
  }

  /**
   * Does is_custom return false if rule is NOT custom?
   *
   */
  public function test_rule_is_custom_returns_false() {
    $rules = new Rules( self::$rules );
    $this->assertFalse( $rules->is_custom('required') );
  }
}

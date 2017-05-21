<?php
namespace WFV\Collection;
defined( 'ABSPATH' ) || die();

use WFV\Abstraction\Collectable;

/**
 *
 *
 * @since 0.10.0
 */
class RuleCollection extends Collectable {

	/**
	 *
	 *
	 * @since 0.10.0
	 *
	 * @param array $rules
	 */
	public function __construct( array $rules ) {
		$this->data = $this->parse_rules( $rules );
	}


	/**
	 * Get rules array
	 *
	 * @since 0.10.0
	 *
	 * @return array
	 */
	public function get_array() {
		return $this->data;
	}

	/**
	 * Get array of unique rule types
	 *
	 * @since 0.11.0
	 *
	 * @return array
	 */
	public function unique() {
		$flat = $this->flatten( $this->remove_params() );
		return array_values( array_filter( array_unique( $flat ), function( $item ) {
			return $item !== 'optional';
		}));
	}

	/**
	 * Extract rule name from a rule string
	 *
	 * @since 0.11.0
	 * @access protected
	 *
	 * @param string $rule
	 * @return string
	 */
	protected function extract_name( $rule ) {
		return strstr( $rule, ':', true );
	}

	/**
	 * Extract rule parameters from a rule string
	 *
	 * @since 0.11.0
	 * @access protected
	 *
	 * @param string $rule
	 * @return string
	 */
	protected function extract_params( $rule ) {
		return ltrim( strstr($rule, ':'), ':');
	}

	/**
	 *
	 *
	 * @since 0.11.0
	 * @access protected
	 *
	 * @param array $array
	 * @return array
	 */
	protected function flatten( array $array ) {
		$flat = array();
		foreach( $array as $rule ) {
			if( is_array( $rule ) ){
				$flat = array_merge( $flat, $this->flatten( $rule ) );
			} else {
				$flat[] = $rule;
			}
		}
		return $flat;
	}

	/**
	 * Checks if a rule string has parameters
	 *
	 * @since 0.11.0
	 * @access protected
	 *
	 * @param string $rule
	 * @return bool
	 */
	protected function has_parameters( $rule ) {
		return strpos( $rule, ':' );
	}

	/**
	 * Split each string ruleset from config array
	 *  into a machine friendly multi-dimensional array
	 *
	 * @since 0.11.0
	 * @access protected
	 *
	 * @param array $rules
	 * @return array
	 */
	protected function parse_rules( array $rules ) {
		// WIP - works, but confusing - simplify or breakdown into small methods
		$parsed = array();
		$this->split_rules( $rules );
		foreach( $rules as $field => $ruleset ) {
			$parsed[ $field ] = array_map( function( $rule ) {
				if ( $this->has_parameters( $rule ) ) {
					return array(
						'rule' => $this->extract_name( $rule ),
						'params' => explode( ',', $this->extract_params( $rule ) )
					);
				}
				return $rule;
			}, $ruleset );
		}
		return $parsed;
	}

	/**
	 * Flatens rules with parameters in the collection
	 *  and returns the new array.
	 *
	 * @since 0.11.0
	 * @access protected
	 *
	 * @return array
	 */
	protected function remove_params() {
		return array_map( function( $item ) {
			foreach( $item as $rule ) {
				$rules[] = ( is_string( $rule ) ) ? $rule : $rule['rule'];
			}
			return $rules;
		}, $this->data );
	}

	/**
	 * Converts string ruleset to index array
	 *
	 * @since 0.11.0
	 * @access protected
	 *
	 * @param array $rules
	 */
	protected function split_rules( array &$rules ) {
		// perhaps the $rules array structure should be validated here?...
		$rules = array_map( function( $item ) {
			return explode( '|', $item );
		}, $rules );
	}
}

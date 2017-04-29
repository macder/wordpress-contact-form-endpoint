<?php
namespace WFV\Component;
defined( 'ABSPATH' ) or die();

use WFV\Abstraction\Collection;

/**
 *
 *
 * @since 0.8.0
 */
class Errors extends Collection {

	/**
	 * Convienience method to get first error on field.
	 *
	 * @since 0.9.1
	 *
	 * @param string $field Name of field
	 * @return string First error message.
	 */
	public function first( $field ) {
		$errors = $this->$field;
		return $errors[0];
	}
}

<?php
namespace WFV\Validators;
defined( 'ABSPATH' ) or die();

use WFV\Validators\AbstractValidator;

/**
 *
 *
 * @since 0.11.0
 */
class AlphaDash extends AbstractValidator {

	/**
	 *
	 *
	 * @since 0.11.0
	 * @access protected
	 * @var
	 */
	protected $template = [
		'message' => '{label} can only contain alphabetic characters, dashes, and underscores',
		'name' => 'alpha',
	];

	/**
	 * Set the validation constraints that make this rule
	 *
	 * @since 0.11.0
	 * @access protected
	 *
	 */
	protected function set_policy() {
		$v = $this->validator;
		$v = ( $this->optional )
			? $v->optional( $v->create()->alpha('-_') )
			: $v->alpha('-_');
	}
}
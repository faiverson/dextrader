<?php namespace App\Services;

use Illuminate\Validation\Factory;
use App\Services\ValidableInterface;

abstract class AbstractValidator implements ValidableInterface {
	/**
	 * Validator
	 *
	 * @var object
	 */
	protected $validator;

	/**
	 * Data to be validated
	 *
	 * @var array
	 */
	protected $data = array();

	/**
	 * Validation Rules
	 *
	 * @var array
	 */
	protected $rules = array();

	/**
	 * Messages
	 *
	 * @var array
	 */
	protected $messages = array();

	/**
	 * Validation errors
	 *
	 * @var array
	 */
	protected $errors = array();

	public function __construct(Factory $validator)
	{
		$this->validator = $validator;
	}

	/**
	 * Set data to validate
	 *
	 * @param array $data
	 * @return self
	 */
	public function with(array $data)
	{
		$this->data = $data;

		return $this;
	}

	/**
	 * Return errors
	 *
	 * @return array
	 */
	public function errors()
	{
		return $this->errors;
	}

	/**
	 * Pass the data and the rules to the validator
	 *
	 * @return boolean
	 */
	public function passes()
	{
		$validator = $this->validator->make($this->data, $this->rules, $this->messages);

		if( $validator->fails() )
		{
			$this->errors = $validator->messages();
			return false;
		}

		return true;
	}

	public function custom() {}
}
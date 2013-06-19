<?php

abstract class Base_Model extends Eloquent
{
	protected $rules = [];
	protected $messages = [];
	protected $errors;

	public function validate()
	{
		$validator = Validator::make($this->attributes, $this->rules, $this->messages);

		if ($validator->fails()) {
			$this->errors = $validator->errors;
			return false;
		}

		return true;
	}

	public function errors()
	{
		return $this->errors;
	}
}
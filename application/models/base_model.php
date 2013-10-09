<?php

abstract class Base_Model extends Eloquent
{
	protected $rules = array();
	protected $messages = array();
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
	
	public function get_attribute($attribute)
	{
		if ( ! isset($this->attributes[$attribute]) && ! @is_null($this->attributes[$attribute]) )
		{
			if ( isset($this->attributes['id']) )
			{
				$field = static::select($attribute)->where_id($this->attributes['id'])->first();
				
				if ( isset($field->attributes[$attribute]) )
				{
					$this->set_attribute($attribute, $field->attributes[$attribute]);
				}
			}
		}
		
		return parent::get_attribute($attribute);
	}
}
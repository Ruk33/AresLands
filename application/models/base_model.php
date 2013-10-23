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
		// Comprobamos que el registro exista
		if ( $this->exists )
		{
			// Comprobamos que la columna $attribute no esté y que la columna id si lo esté
			// Comprobamos la columna id, porque si no la tenemos, no hay forma de traer
			// la columna $attribute
			if ( ! isset($this->attributes[$attribute]) && isset($this->attributes['id']) )
			{
				// Verificamos que la columna $attribute exista
				$field = DB::query("SHOW FIELDS FROM " . static::$table . " where Field = '$attribute'");
				
				// $field es un array, estará vacío si la
				// columna no existe
				if ( isset($field[0]) )
				{
					// Verificamos si la columna puede ser null
					$canBeNull = $field[0]->null != 'NO';
				
					if ( ! $canBeNull )
					{
						// No puede ser null, entonces quiere decir que
						// la columna no está, así que vamos a traerla
						$field = static::select(array($attribute))->where_id($this->attributes['id'])->first();
						$this->set_attribute($attribute, $field->attributes[$attribute]);
					}
				}
			}
		}
		
		return parent::get_attribute($attribute);
	}
}
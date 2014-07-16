<?php

abstract class Base_Model extends Eloquent
{
	protected $rules = array();
	protected $messages = array();
	protected $errors;
	
	public function fire_global_event($event, $args)
	{
		Event::fire($event, $args);
	}
	
	/**
	 * 
	 * @param array $attributes
	 * @return Eloquent
	 */
	public static function create_instance(Array $attributes = array())
	{
		return new static($attributes);
	}
    
    protected function inject_query($query)
    {
        return $query;
    }
    
    protected function _query()
    {
        $query = parent::_query();
        
        return $this->inject_query($query);
    }

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
        // Si estamos en local, y la columna no ha sido traida...
        if ( Request::env() == 'local' && ! array_key_exists($attribute, $this->attributes) )
        {
            //echo "<div style='background-color: red; color: white; font-family: consolas;' class='text-center'>[Error] Posible columna sin traer -> {$attribute}</div>";
        }
		
		return parent::get_attribute($attribute);
	}

	public function to_json()
	{
		return json_encode($this);
	}
}
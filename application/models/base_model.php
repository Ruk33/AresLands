<?php

abstract class Base_Model extends Eloquent
{
	protected $rules = array();
	protected $messages = array();
	protected $errors;
	
	/**
	 * 
	 * @param array $attributes
	 * @return Eloquent
	 */
	public static function create_instance(Array $attributes = array())
	{
		return new static($attributes);
	}
	
	/**
	 * Primero o vacio (se evita el null) asi se puede usar la instancia
	 * exista o no el registro
	 * 
	 * @return Eloquent
	 */
	public function first_or_empty()
	{
		return ( is_null($model = $this->first) ) ? new static : $model ;
	}
	
	/**
	 * 
	 * @return Eloquent|void
	 */
	public function first_or_die()
	{
		if ( ! is_null($model = $this->first()) )
		{
			return $model;
		}
		
		$response = Response::error('404');
		$response->render();
		$response->send();
		$response->foundation->finish();

		exit(1);
	}
	
	/**
	 * 
	 * @param integer $id
	 * @param Array $select
	 * @return Eloquent|void
	 */
	public function find_or_die($id, Array $select = array('*'))
	{
		if ( ! is_null($model = $this->find($id, $select)) )
		{
			return $model;
		}
		
		$response = Response::error('404');
		$response->render();
		$response->send();
		$response->foundation->finish();

		exit(1);
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
            echo "<div style='background-color: red; color: white; font-family: consolas;' class='text-center'>[Error] Posible columna sin traer -> {$attribute}</div>";
        }
		
		return parent::get_attribute($attribute);
	}

	public function to_json()
	{
		return json_encode($this);
	}
}
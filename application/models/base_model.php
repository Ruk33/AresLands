<?php

use Laravel\Database\Eloquent\Query;
use Laravel\Database\Eloquent\Relationships\Belongs_To;
use Laravel\Database\Eloquent\Model;

abstract class Base_Model extends Model
{
    /**
     * 
     * @var Query 
     */
    protected $query;
    
    /**
     *
     * @var Belongs_To 
     */
    protected $belongsTo;
    
    /**
     *
     * @var array
     */
	protected $rules = array();
    
    /**
     *
     * @var array
     */
	protected $messages = array();
    
    /**
     *
     * @var array
     */
	protected $errors;

    /**
     * En lugar de ir escribiendo dependencia por dependencia
     * (ej.: setCharacterRepository, setQuestRepository, getX, etc.)
     * simplemente creamos un array donde las guardamos evitando
     * asi tener que re-escribir el mismo codigo :)
     *
     * @var array
     */
    protected $dependencies;

    /**
     * Asignamos el valor de una dependencia (util para test/mock).
     *
     * Establecemos este metodo como protected para que la clase
     * que lo vaya a utilizar determine los nombres de las dependencias
     * y asi evitar problemas a la hora de tener que cambiarlos.
     *
     * @param string $name
     * @param mixed $value
     */
    protected function setDependency($name, $value)
    {
        $this->dependencies[$name] = $value;
    }

    /**
     * Obtenemos una dependencia. En caso de que la misma
     * no haya sido asignada, se busca en el contenedor IoC
     *
     * Establecemos este metodo como protected para que la clase
     * que lo vaya a utilizar determine los nombres de las dependencias
     * y asi evitar problemas a la hora de tener que cambiarlos.
     *
     * @param string $name
     * @return mixed
     */
    protected function getDependency($name)
    {
        if (! isset($this->dependencies[$name])) {
            $this->setDependency($name, \Laravel\IoC::resolve($name));
        }

        return $this->dependencies[$name];
    }
	
    /**
     * 
     * @param Belongs_To $belongsTo
     */
    public function setBelongsTo(Belongs_To $belongsTo = null)
    {
        $this->belongsTo = $belongsTo;
    }
    
    public function belongs_to($model, $foreign = null)
    {
        return ($this->belongsTo) ? $this->belongsTo : parent::belongs_to($model, $foreign);
    }
    
	public function fire_global_event($event, $args)
	{
		Event::fire($event, $args);
	}
    
    protected function inject_query($query)
    {
        return $query;
    }
    
    /**
     * Its Mocking time!
     * 
     * Cuando no se quiera tocar la base de datos simplemente
     * reemplazar la dependencia (Query) con un Mock
     * 
     * @param Query $query
     */
    public function setQuery(Query $query = null)
    {
        $this->query = $query;
    }
    
    /**
     * 
     * @return Query
     */
    protected function getQuery()
    {
        return ($this->query) ? $this->query : parent::_query();
    }
    
    protected function _query()
    {
        return $this->inject_query($this->getQuery());
    }
    
    public function get_validator($attributes, $rules, $messages = array())
    {
        return Validator::make($attributes, $rules, $messages);
    }

	public function validate()
	{
		$validator = $this->get_validator($this->attributes, $this->rules, $this->messages);

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

	public function to_json()
	{
		return json_encode($this);
	}
}
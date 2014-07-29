<?php

abstract class VipObject
{
    /**
     *
     * @var Character
     */
    protected $buyer;
    
    /**
     * Array para guardar informacion (por ejemplo en cambio de nombre
     * requerimos el nuevo nombre) y luego poder validarla
     * 
     * @var array
     */
    protected $attributes;

    /**
     * 
     * @param Character $buyer
     */
    public function setBuyer(Character $buyer)
    {
        $this->buyer = $buyer;
    }
    
    /**
     * 
     * @param array $attributes
     */
    public function setAttributes(Array $attributes)
    {
        $this->attributes = $attributes;
    }
    
    /**
	 * Se ejecutan las acciones del objeto vip
	 * @return bool
	 */
    abstract public function execute();
    
    /**
     * @return Validator
     */
    abstract public function getValidator();
    
    /**
     * @return string
     */
    abstract public function getName();
    
    /**
     * @return string
     */
    abstract public function getDescription();
    
    /**
     * @return string
     */
    abstract public function getIcon();
    
    /**
     * @return float
     */
    abstract public function getPrice();
    
    /**
     * Obtenemos el html para el input
     * @return string
     */
    public function getInput()
    {
        return "";
    }
}
<?php namespace Models\Vip;

interface VipImplementation
{    
    /**
     * @return \Laravel\Validator
     */
    public function getValidator();
    
    /**
     * @return string
     */
    public function getInputs();
    
    /**
     * @return boolean
     */
    public function execute();
    
    /**
     * 
     * @param \Character $entity
     * @param array $attributes
     */
    public function __construct(\Character $entity, array $attributes);
}
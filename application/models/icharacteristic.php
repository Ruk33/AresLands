<?php

interface ICharacteristic
{
    /**
     * Obtenemos el "id" de la caracteristica
     * @return string
     */
    public static function get_id();
    
	/**
	 * Obtenemos el nombre de la caracteristica
	 * @return string
	 */
	public function get_name();
	
	/**
	 * Obtenemos la descripcion de la caracteristica
	 * @return string
	 */
	public function get_description();
	
	/**
	 * Obtenemos los bonuses (string para view) que da la caracteristica
	 * @return array
	 */
	public function get_bonusses();
	
	/**
	 * Obtenemos las skills de la caracteristica
	 * @return array
	 */
	public function get_skills();
}
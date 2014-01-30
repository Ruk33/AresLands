<?php

abstract class Characteristic
{
	const ENERGETIC = 'energico';
	const LAZY = 'perezoso';
	
	const RESERVED = 'reservado';
	const RELIANT = 'confiado';
	
	const BRAVE = 'valiente';
	const SHY = 'timido';
	
	const TALENTED = 'talentoso';
	const CLUMSY = 'torpe';
	
	const ADVENTUROUS = 'aventurero';
	const CAUTIOUS = 'cauto';
	
	/**
	 * Obtenemos una caracteristica (ver constantes para el parametro)
	 * @param string $characteristic
	 * @return ICharacteristic
	 * @throws Exception
	 */
	public static function get($characteristic)
	{
		switch (strtolower($characteristic) )
		{
			case self::ADVENTUROUS:
				return new CharacteristicAdventurous();
			
			case self::BRAVE:
				return new CharacteristicBrave();
				
			case self::CAUTIOUS:
				return new CharacteristicCautious();
				
			case self::CLUMSY:
				return new CharacteristicClumsy();
				
			case self::ENERGETIC:
				return new CharacteristicEnergetic();
				
			case self::RELIANT:
				return new CharacteristicReliant();
				
			case self::LAZY:
				return new CharacteristicLazy();
				
			case self::RESERVED:
				return new CharacteristicReserved();
				
			case self::SHY:
				return new CharacteristicShy();
				
			case self::TALENTED:
				return new CharacteristicTalented();
				
			default:
				throw new Exception("La caracteristica {$characteristic} no existe.");
		}
	}
	
	/**
	 * Obtenemos todas las caracteristicas
	 * @return array
	 */
	public static function get_all()
	{
		return array(
			array(
				new CharacteristicEnergetic(),
				new CharacteristicLazy()
			),
			
			array(
				new CharacteristicReserved(),
				new CharacteristicReliant()
			),
			
			array(
				new CharacteristicBrave(),
				new CharacteristicShy()
			),
			
			array(
				new CharacteristicTalented(),
				new CharacteristicClumsy()
			),
			
			array(
				new CharacteristicAdventurous(),
				new CharacteristicCautious
			)
		);
	}
}
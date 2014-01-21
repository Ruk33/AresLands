<?php

abstract class VipFactory
{
	const COIN_MULTIPLIER = 1;
	const XP_MULTIPLIER = 2;
	const REDUCTION_TIME = 3;
	const CHANGE_GENDER = 4;
	const CHANGE_NAME = 5;
	const CHANGE_RACE = 6;
	
	/**
	 * Obtenemos instancia del objeto vip
	 * @param integer $vipObject
	 * @return IVipObject o null
	 */
	public static function get($vipObject)
	{
		switch ( $vipObject )
		{
			case self::COIN_MULTIPLIER:
				return new VipCoinMultiplier;
				break;
			
			/*case self::XP_MULTIPLIER:
				return new VipXpMultiplier;
				break;
			
			case self::REDUCTION_TIME:
				return new VipReductionTime;
				break;
			
			case self::CHANGE_GENDER:
				return new VipChangeGender;
				break;
			
			case self::CHANGE_NAME:
				return new VipChangeName;
				break;
			
			case self::CHANGE_RACE:
				return new VipChangeRace;
				break;*/
		}
		
		return null;
	}
}
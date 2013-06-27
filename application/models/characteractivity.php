<?php

class CharacterActivity extends Base_Model
{
	public static $softDelete = false;
	public static $timestamps = false;
	public static $table = 'character_activities';

	public function get_data()
	{
		return unserialize($this->get_attribute('data'));
	}

	public function set_data($data)
	{
		$this->set_attribute('data', serialize($data));
	}

	public function update_time()
	{
		$character = Session::get('character');

		if ( $this->end_time <= time() )
		{
			switch ( $this->name ) {
				case 'travel':
					/*
					 *	Obtenemos la información
					 *	de la zona a donde viajó
					 */
					$zone = $this->data['zone'];

					/*
					 *	Cobramos el costo del viaje
					 */
					$characterCoins = $character->get_coins();
					$characterCoins->count -= Config::get('game.travel_cost');
					$characterCoins->save();

					/*
					 *	Actualizamos la zona en
					 *	donde el personaje se encuentra
					 */
					$character->zone_id = $zone->id;
					$character->save();

					break;
				
				case 'battlerest':
					

					break;
			}

			/*
			 *	Finalizó la actividad, por ende
			 *	la eliminamos de la db
			 */
			$this->delete();
		}
	}
}
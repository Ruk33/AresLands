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

	public function character()
	{
		return $this->belongs_to('Character', 'character_id');
	}

	public function update_time()
	{
		//$character = Session::get('character');
		$character = $this->character()->select(array('id', 'zone_id', 'is_traveling', 'is_exploring', 'xp', 'zone_id'))->first();

		if ( ! $character )
		{
			return;
		}

		if ( $this->end_time <= time() )
		{
			switch ( $this->name ) 
			{
				case 'travel':
					/*
					 *	Obtenemos la información
					 *	de la zona a donde viajó
					 */
					$zone = $this->data['zone'];

					/*
					 *	Actualizamos la zona en
					 *	donde el personaje se encuentra
					 */
					$character->zone_id = $zone->id;
					$character->is_traveling = false;
					$character->save();

					break;
				
				case 'battlerest':
					break;

				case 'explore':
					$data = $this->data;

					$character->is_exploring = false;
					$character->xp += $data['time'] / 60 * Config::get('game.xp_rate');
					$character->add_exploring_time($character->zone, $data['time']);
					$character->give_explore_reward($data['reward']);
					$character->save();

					/*
					 *	Nuevo mounstruo para pelear
					 */
					$monster = Npc::where('zone_id', '=', $character->zone_id)->where('type', '=', 'monster')->order_by(DB::raw('RAND()'))->first();

					if ( $monster )
					{
						Session::put('monster_id', $monster->id);
					}

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
<?php

class CharacterActivity extends Base_Model
{
	public static $softDelete = false;
	public static $timestamps = false;
	public static $table = 'character_activities';
	public static $key = 'id';

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
        $character = $this->character;
        
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

					$xpGained = $data['time'] / 60 / 25 * Config::get('game.xp_rate');

					$character->is_exploring = false;

					if ( $xpGained > 0 )
					{
						$character->xp += $xpGained;
						$character->points_to_change += $xpGained;
					}
					
					$character->add_exploring_time($character->zone()->select(array('id'))->first(), $data['time']);
					$character->give_explore_reward($data['reward']);

					$character->save();

					/*
					 *	Enviamos informe de que terminó de explorar
					 */
					Message::completed_exploration($character, $data['time'] / 60 / 25 * Config::get('game.xp_rate'), $data['reward']);

					break;
			}

			/*
			 *	Finalizó la actividad, por ende
			 *	la eliminamos de la db
			 */
			$this->delete();
		}
	}
	
	public function save()
	{
		$character = $this->character;
		
		// Verificamos si el personaje es afortunado y recarga uno de sus talentos
		if ( $character->has_skill(Config::get('game.ready_for_new_adventure_skill')) )
		{
			if ( mt_rand(0, 100) <= $character->luck )
			{
				$randomTalent = $character->get_random_talent()
										  ->where('usable_at', '>', time())
										  ->first();
				
				if ( $randomTalent )
				{
					$character->refresh_talent($randomTalent);
				}
			}
		}
		
		if ( $this->name != 'explore' )
		{
			if ( $character->has_skill(Config::get('game.overload_skill')) )
			{
				$this->end_time = 1;
				
				$overloadSkill = $character->skills()->where('skill_id', '=', Config::get('game.overload_skill'))->first();
				
				if ( $overloadSkill )
				{
					$character->remove_buff($overloadSkill);
				}
			}
			else
			{
				if ( $character->has_skill(Config::get('game.vip_reduction_time_skill')) )
				{
					// Se usa time() para los tiempos asi que no
					// deberia haber problemas				
					$this->end_time -= ($this->end_time - time()) * 0.20;
				}

				switch ( $this->name )
				{
					case 'travel':
						$this->end_time -= $character->travel_time + $character->travel_time_extra;
						break;

					case 'battlerest':
						$this->end_time -= $character->battle_rest_time + $character->battle_rest_time;
						break;
				}
			}
		}
		
		return parent::save();
	}
}
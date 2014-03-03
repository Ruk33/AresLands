<?php

class Npc extends Base_Model
{
	public static $softDelete = false;
	public static $timestamps = false;
	public static $table = 'npcs';
	public static $key = 'id';
	
	public function get_text_for_tooltip()
	{
		$message = "<div style='min-width: 250px; text-align: left;'>";

		$message .= "<img src='" . URL::base() . "/img/icons/npcs/$this->id.png' class='pull-left' width='32px' height='32px' style='margin-right: 10px;'>";

		$message .= "<strong style='color: orange; margin-top: 10px;'>$this->name</strong>";
		$message .= "<br>Nivel: $this->level";
		$message .= "<p><small><em>$this->dialog</em></small></p>";

		$message .= "<ul class='unstyled'>";
		
		if ( $this->stat_strength != 0 )
		{
			$message .= "<li>Fuerza física: $this->stat_strength</li>";
		}

		if ( $this->stat_dexterity != 0 )
		{
			$message .= "<li>Destreza física: $this->stat_dexterity</li>";
		}
		
		if ( $this->stat_resistance != 0 )
		{
			$message .= "<li>Resistencia: $this->stat_resistance</li>";
		}

		if ( $this->stat_magic != 0 )
		{
			$message .= "<li>Poder mágico: $this->stat_magic</li>";
		}

		if ( $this->stat_magic_skill != 0 )
		{
			$message .= "<li>Habilidad mágica: $this->stat_magic_skill</li>";
		}

		if ( $this->stat_magic_resistance != 0 )
		{
			$message .= "<li>Contraconjuro: $this->stat_magic_resistance</li>";
		}

		$message .= '</ul>';

		$message .= '</div>';

		return $message;
	}

	public function get_stats()
	{
		$stats = array();

		$stats['stat_strength'] = $this->stat_strength;
		$stats['stat_dexterity'] = $this->stat_dexterity;
		$stats['stat_resistance'] = $this->stat_resistance;
		$stats['stat_magic'] = $this->stat_magic;
		$stats['stat_magic_skill'] = $this->stat_magic_skill;
		$stats['stat_magic_resistance'] = $this->stat_magic_resistance;

		return $stats;
	}

	/**
	 *	Obtenemos los npcs (no mounstros) de
	 *	una zona
	 *
	 *	@param <Zone> $zone Zona de donde queremos los npcs
	 *	@return <array> Npcs (no mounstros) que se encuentran en $zone
	 */
	public static function get_npcs_from_zone(Zone $zone)
	{
		if ( ! $zone )
		{
			return array();
		}

		$character = Character::get_character_of_logged_user(array('id'));
		$exploringTime = $character->exploring_times()->select(array('character_id', 'time'))->where('zone_id', '=', $zone->id)->first();

		return Npc::select(array('id', 'name', 'dialog', 'tooltip_dialog'))
		->where('zone_id', '=', $zone->id)
		->where('type', '=', 'npc')
		->where('time_to_appear', '<=', ( isset($exploringTime->time) ) ? $exploringTime->time : 0 )
		->order_by('time_to_appear', 'asc')
		->get();
	}

	/**
	 *	Detectamos si un npc está bloqueado para un personaje
	 *
	 *	@param <Character> $character
	 *	@return <bool> true en caso de estar bloqueado, false de lo contrario
	 */
	public function is_blocked_to(Character $character)
	{
		if ( ! $character )
		{
			return true;
		}
		
		if ( ! $this->time_to_appear )
		{
			return false;
		}

		$exploringTime = $character->exploring_times()->select(array('character_id', 'time'))->where('zone_id', '=', $this->zone_id)->first();

		if ( ! $exploringTime )
		{
			return true;
		}

		return $this->time_to_appear > $exploringTime->time;
	}

	/**
	 *	Obtenemos las misiones de un npc que pueden ser
	 *	pedidas por un personaje 
	 *
	 *	@param Character $character
	 *	@return Object
	 */
	public function available_quests_of(Character $character)
	{
		$characterQuests = DB::raw("(
			SELECT 
				quest_id 
			FROM 
				character_quests 
			WHERE 
				character_id = $character->id
				
				AND
				
				(
					repeatable_at IS NULL
					OR
					repeatable_at > " . time() . "
				)
		)");
		
		return $this->
				quests()->
				where('quests.id', 'NOT IN', $characterQuests)->
				where('min_level', '<=', $character->level)->
				where('max_level', '>=', $character->level);
	}

	/**
	 *	Obtenemos las misiones que son repetibles
	 *	y que el personaje ya ha finalizado
	 *
	 *	@param Character $character
	 *	@return Object
	 */
	public function repeatable_quests_of(Character $character)
	{
		return $this
		->quests()
		->join('character_quests as character_quest', 'quests.id', '=', 'character_quest.quest_id')
		->where('repeatable', '=', 1)
		->where('character_quest.character_id', '=', $character->id)
		->where('character_quest.repeatable_at', '>', time())
		->where('character_quest.progress', '=', 'finished')
		->select(array('character_quest.repeatable_at', 'quests.*'));
	}

	/**
	 *	Obtenemos todas las misiones aceptadas/pedidas/iniciadas
	 *	de un personaje
	 *
	 *	@param Character $character
	 *	@return Object
	 */
	public function started_quests_of(Character $character)
	{
		return $this
		->quests()
		//->join('quests as quest', 'npc_quests.quest_id', '=', 'quest.id')
		->join('character_quests', 'quests.id', '=', 'character_quests.quest_id')
		->where('character_quests.character_id', '=', $character->id)
		->where('character_quests.progress', '=', 'started');
	}

	public function reward_quests_of(Character $character)
	{
		return $this
		->quests()
		//->join('quests as quest', 'npc_quests.quest_id', '=', 'quest.id')
		->join('character_quests', 'quests.id', '=', 'character_quests.quest_id')
		->where('character_quests.character_id', '=', $character->id)
		->where('character_quests.progress', '=', 'reward');
	}

	public function quests()
	{
		//return $this->has_many('NpcQuest', 'npc_id');
		return $this->has_many_and_belongs_to('Quest', 'npc_quests');
	}

	public function merchandises()
	{
		return $this->has_many('NpcMerchandise', 'npc_id');
	}
}
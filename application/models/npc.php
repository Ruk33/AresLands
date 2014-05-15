<?php

class Npc extends Base_Model
{
	public static $softDelete = false;
	public static $timestamps = false;
	public static $table = 'npcs';
	public static $key = 'id';
    
    /**
     * Obtenemos la ruta de la imagen del npc
     * @return type
     */
    public function get_image_path()
    {
        return URL::base() . "/img/icons/npcs/{$this->id}.png";
    }
	
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

		$character = Character::get_character_of_logged_user(array('id', 'level'));

		return Npc::select(array('id', 'name', 'dialog', 'tooltip_dialog'))
		->where('zone_id', '=', $zone->id)
		->where('type', '=', 'npc')
		->order_by('level_to_appear', 'asc')
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
		
		if ( ! $this->level_to_appear )
		{
			return false;
		}

		return $this->level_to_appear > $character->level;
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
				where('min_level', '<=', $character->level + 10);
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

	public function zone()
	{
		return $this->belongs_to("Zone", "zone_id");
	}

	public function quests()
	{
		return $this->has_many_and_belongs_to('Quest', 'npc_quests');
	}

	public function merchandises()
	{
		return $this->has_many('NpcMerchandise', 'npc_id');
	}
	
	public function random_merchandises()
	{
		return $this->has_many('NpcRandomMerchandise', 'npc_id');
	}
	
	/**
	 * Verificamos si la lista de mercancias aleatoria esta vieja (usada
	 * para saber si necesitamos generar una nueva)
	 * @return boolean
	 */
	public function is_random_merchandises_list_old()
	{
		$randomMerchandise = $this->random_merchandises()->first();
		
		if ( ! $randomMerchandise )
		{
			return true;
		}
		
		return $randomMerchandise->valid_until < time();
	}
	
	/**
	 * Obtenemos el mejor objeto de la lista de mercancias del npc
	 * @return Eloquent
	 */
	public function get_best_item_from_normal_merchandise_list()
	{
		return $this->merchandises()
					->join('items', 'items.id', '=', 'npc_merchandises.item_id')
					->order_by('items.level', 'desc')
					->select(array('npc_merchandises.*'));
	}
	
	/**
	 * Generamos nueva lista de mercancias aleatorias
	 */
	public function generate_random_merchandise_list()
	{
		// Borramos la lista anterior
		$this->random_merchandises()->delete();
		
		// Valido luego de un dia de haber sido generado
		$validUntil = time() + 60 * 60 * 24;
		
		$bestMerchandise = $this->get_best_item_from_normal_merchandise_list()->with('item')->first();
		$bestItem = ( $bestMerchandise ) ? $bestMerchandise->item : new Item();
		
		if ( $bestItem->type == 'mercenary' )
		{
			$types = array('mercenary');
		}
		else
		{
			$types = array('blunt','bigblunt','sword','bigsword','bow','dagger','staff','bigstaff','hammer','bighammer','ring','axe','shield','potion','arrow');

			switch ( $this->zone_id )
			{
				// Montes barbaros
				case 1:
					$types = array("blunt", "bigblunt", "sword", "bigsword", "dagger", "hammer", "bighammer", "axe");
					break;

				// Valle de la sangre
				case 2:
					$types = array("staff", "bigstaff", "ring");
					break;

				// Lago subterraneo
				case 3:
					$types = array("potion");
					break;

				// Piramides
				case 4:
					$types = array("bow", "dagger", "shield");
					break;
			}
		}
		
		$items = Item::where('level', '>', $bestItem->level + mt_rand(0, 10))
					 ->where('level', '<', $bestItem->level + mt_rand(25, 60))
					 ->where_in('type', $types)
					 ->take(16) // 16 asi encaja justo con la pagina :D
					 ->order_by(DB::raw("RAND()"))
					 ->select(array('id', 'level'))
					 ->get();
		
		foreach ( $items as $item )
		{
			$this->random_merchandises()->insert(new NpcRandomMerchandise(array(
				'item_id'      => $item->id,
				'price_copper' => $item->level * mt_rand(11800, 15700),
				'valid_until'  => $validUntil
			)));
		}
	}
	
	/**
	 * Obtenemos lista de mercancias para personaje
	 * @param Character $character
	 * @return Eloquent
	 */
	public function get_merchandises_for(Character $character)
	{
		$bestItem = $this->get_best_item_from_normal_merchandise_list()
						 ->with('item')
						 ->first();
		
		if ( $bestItem && $bestItem->item->level < $character->level - 3 )
		{
			if ( $this->is_random_merchandises_list_old() )
			{
				$this->generate_random_merchandise_list();
			}
			
			return $this->random_merchandises();
		}
		else
		{
			return $this->merchandises();
		}
	}

	/**
	 * Usar solo cuando sea necesario
	 * @return Eloquent
	 */
	public function drops()
	{
		return $this->has_many("MonsterDrop", "monster_id");
	}
}
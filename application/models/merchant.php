<?php

class Merchant extends Npc
{
    public function get_tooltip()
    {
        return "<strong>Mercader {$this->name}</strong><p>{$this->dialog}</p>";
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
		return $this->quests()
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
		return $this->quests()
                    ->join('character_quests', 'quests.id', '=', 'character_quests.quest_id')
                    ->where('character_quests.character_id', '=', $character->id)
                    ->where('character_quests.progress', '=', 'started');
	}

	public function reward_quests_of(Character $character)
	{
		return $this->quests()
                    ->join('character_quests', 'quests.id', '=', 'character_quests.quest_id')
                    ->where('character_quests.character_id', '=', $character->id)
                    ->where('character_quests.progress', '=', 'reward');
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
        // Misiones que no se pueden repetir o que aun
        // estan con CD
		$notRepeatableCharacterQuests = DB::raw(
        "(
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
		
		return $this->quests()
                    ->where('quests.id', 'NOT IN', $notRepeatableCharacterQuests)
                    ->where('min_level', '<=', $character->level);
	}
    
    /**
     * Query para obtener las misiones que puede ofrecer el mercader
     * 
     * @return Eloquent
     */
    public function quests()
	{
		return $this->has_many_and_belongs_to('Quest', 'npc_quests');
	}
    
    public function get_link()
    {
        return URL::to('authenticated/npc/' . $this->id . '/' . Str::slug($this->name));
    }
    
    public function is_blocked_to(Character $character)
    {
        return $this->level_to_appear > $character->level;
    }
    
    /**
     * Query para obtener las mercancias del mercader
     * 
     * @return Eloquent
     */
    public function merchandises()
	{
		return $this->has_many('NpcMerchandise', 'npc_id');
	}
	
    /**
     * Query para obtener las mercancias aleatorias del mercader
     * 
     * @return Eloquent
     */
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
		
        // Cuantos objetos vamos a agregar a la lista
        // 16 asi encaja justo con la pagina :D
        $howMany = 16;
        
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
                    
                    // Aseguramos que haya pocion de vida en la nueva lista
                    $howMany--;
                    $this->random_merchandises()->insert(new NpcRandomMerchandise(array(
                        'item_id'      => 122,
                        'price_copper' => 500,
                        'valid_until'  => $validUntil
                    )));
                    
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
                     ->where('id', '<>', 122) // pocion de vida
					 ->take($howMany)
					 ->order_by(DB::raw("RAND()"))
					 ->select(array('id', 'level'))
					 ->get();
		
		foreach ( $items as $item )
		{
            $min = 11800;
            $max = 15700;
            
            // Si es una pocion, evitamos que su precio sea muy elevado
            if ( $item->type == 'potion' )
            {
                $min = 663;
                $max = 779;
            }
            
			$this->random_merchandises()->insert(new NpcRandomMerchandise(array(
				'item_id'      => $item->id,
				'price_copper' => $item->level * mt_rand($min, $max),
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
        // Como no tenemos muchas pociones, en el lago subterraneo
        // simplemente hacemos que devuelva la lista normal
        if ( $character->zone_id == 3 )
        {
            return $this->merchandises();
        }
        
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

    protected function inject_query($query)
    {
        return $query->where('type', '=', 'npc');
    }
}
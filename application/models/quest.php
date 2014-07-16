<?php

class Quest extends Base_Model
{
	public static $softDelete = false;
	public static $timestamps = false;
	public static $table = 'quests';
	public static $key = 'id';
        
    /**
     * Obtenemos query para obtener mision que ayuda a desbloquear
     * (util cuando se quiere mostrar algo como: "ayuda a desbloquear la mision...")
     * @return Eloquent
     */
    public function get_deblock_quest()
    {
        return self::where('complete_required', '=', $this->id);
    }

    /**
     * Obtenemos clase css para mision (roja en caso de ser muy dificil, etc.)
     * @param Character $character
     * @return string
     */
    public function get_css_class(Character $character)
    {
        if ( $character->level <= $this->min_level - 10 )
        {
            return 'very-hard-quest';
        }
        elseif ( $character->level <= $this->min_level - 5 )
        {
            return 'hard-quest';
        }
        elseif ( $character->level <= $this->min_level )
        {
            return 'normal-quest';
        }
        elseif ( $character->level <= $this->min_level + 5 )
        {
            return 'easy-quest';
        }
        else
        {
            return 'very-easy-quest';
        }
    }
    
    /**
     * Devolvemos string con formato mostrando los
     * npcs con los que se debe interactuar
     * @return string
     */
    public function get_actions_for_view()
    {
        $view = "";
        
        foreach ( $this->quest_npcs()->with('npc')->get() as $questNpc )
        {
            if ( $questNpc->action == 'kill' )
            {
                $action = "Derrotar {$questNpc->amount}";
            }
            else
            {
                $action = "Hablar";
            }
            
            $view .= "<li style='vertical-align: top;'>"
                  . "<div class='quest-reward-item' data-toggle='tooltip' data-original-title='<b>{$questNpc->npc->name}</b><p class=\"text-left\">Ubicacion: {$questNpc->npc->zone->name}<br>Accion: {$action}</p>'>"
                  . "<img src='{$questNpc->npc->get_image_path()}' width='30px' height='30px' />"
                  . "</div>"
                  . "</li>";
        }
        
        return "<ul class='inline'>{$view}</ul>";
    }
    
	/**
	 *	Devolvemos un string con formato
	 *	para mostrar la recompensa en las vistas
	 *
	 *	@return <string>
	 */
	public function get_rewards_for_view()
	{
		$character = Character::get_character_of_logged_user(array('id', 'xp', 'level'));

		$rewards = $this->rewards;
		$formatedString = '';

		foreach ( $rewards as $reward )
		{
			switch ( $reward->item_id )
			{
				case Config::get('game.coin_id'):
					$coins = Item::get_divided_coins((int) ($reward->amount * (max($character->level, 5) / 5) * $character->get_quest_coins_rate()));
					
					$text = '<i class="coin coin-copper"></i>';
					$text = '<span data-toggle="tooltip" data-original-title="Cantidad: 
						<ul class=\'inline\' style=\'margin: 0;\'>
						<li><i class=\'coin coin-gold pull-left\'></i> ' . $coins['gold'] . '</li>
						<li><i class=\'coin coin-silver pull-left\'></i> ' . $coins['silver'] . '</li>
						<li><i class=\'coin coin-copper pull-left\'></i> ' . $coins['copper'] . '</li>
					</ul>">' . $text . '</span>';
					break;
				
				case Config::get('game.xp_item_id'):
					$text = '<img src="' . URL::base() . '/img/xp.png" width="22px" height="18px" />';
					$text = '<span data-toggle="tooltip" data-original-title="Cantidad: ' . (int) ($reward->amount * $character->get_xp_quest_rate()) . '">' . $text . '</span>';
					break;

				default:
					$text = '<img src="' . URL::base() . '/img/icons/items/'. $reward->item_id .'.png" />';
					$item = $reward->item;

					if ( $item )
					{
						$text = '<span data-toggle="tooltip" data-original-title="' . $item->get_text_for_tooltip() . '<p>Cantidad: ' . $reward->amount . '</p>">' . $text . '</span>';
					}
					break;
			}

			$formatedString .= '<li style="vertical-align: top;"><div class="quest-reward-item">' . $text . '</div></li>';
		}

		return '<ul class="inline">' . $formatedString . '</ul>';
	}

	/**
     * Damos recompensa de mision al personaje
     * 
     * @param Character $character
     */
	public function give_reward(Character $character)
	{
		ActivityBar::add($character, 3);

		/*
		 *	Obtenemos todas las recompensas
		 *	de la misión
		 */
		$rewards = $this->rewards;

		foreach ( $rewards as $reward )
		{
			switch ( $reward->item_id )
			{
				case Config::get('game.coin_id'):
					$reward->amount = (int) ($reward->amount * (max($character->level, 5) / 5) * $character->get_quest_coins_rate());
					break;
				
				case Config::get('game.xp_item_id'):
					$reward->amount = (int) ($reward->amount * $character->get_xp_quest_rate());
					break;
			}
			
			$character->add_item($reward->item_id, $reward->amount);
		}
	}
    
    /**
     * Verificamos si personaje puede aceptar mision
     * @param Character $character
     * @return boolean
     */
    public function can_character_accept_quest(Character $character)
    {
        if ( $this->complete_required )
		{
			if ( ! $character->has_quest_completed($this->required_quest) )
			{
				return false;
			}
		}

		// Nos fijamos si ya no tiene pedida la mision
		// y no la ha completado
		if ( $character->has_unfinished_quest($this) )
		{
			return false;
		}
        
        if ( $character->has_quest_completed($this) )
		{
			if ( $this->repeatable )
			{
                $characterQuest = $character->quests()
                                            ->where('quest_id', '=', $this->id)
                                            ->first();
                
                // Verificamos si ha pasado el tiempo requerido
				// para volver a pedir nuevamente la misión
				if ( $this->repeatable_after > time() - $characterQuest->time )
				{
					return false;
				}
            }
            else
            {
                return false;
            }
        }
        
        return true;
    }

	/**
	 *	Aceptamos la misión para un personaje
	 *
	 *	Se devuelve false en caso de que
	 *	no se haya podido aceptar.
	 *
	 *	@return <bool>
	 */
	public function accept(Character $character)
	{
		if ( ! $this->can_character_accept_quest($character) )
        {
            return false;
        }

		if ( $character->has_quest_completed($this) )
		{
            // Si ya tiene completada la mision, entonces reiniciamos
            // para que pueda volver a hacerla
            // Nota: no hay necesidad de verificar si la mision puede ser
            // repetible, ya que can_character_accept_quest se encarga de ello
            $character->quests()->where('quest_id', '=', $this->id)->delete();
		}

		/*
		 *	Creamos el progreso
		 *	para el personaje
		 */
		$characterQuest = new CharacterQuest();

		$characterQuest->character_id = $character->id;
		$characterQuest->quest_id = $this->id;
		$characterQuest->progress = 'started';
		$characterQuest->data = $characterQuest->get_initial_data_for_quest($this);

		$characterQuest->save();

		/*
		 *	Disparamos el evento de aceptar
		 *	misiones
		 */
		Event::fire('acceptQuest', array($character, $this));

		return true;
	}

	public function npcs()
	{
		return $this->has_many_and_belongs_to('Npc', 'npc_quests');
	}

	/**
	 * @return \Laravel\Database\Eloquent\Relationship
	 */
	public function quest_npcs()
	{
		return $this->has_many('QuestNpc', 'quest_id');
	}

	public function rewards()
	{
		return $this->has_many('QuestReward', 'quest_id');
	}
    
    /**
     * Query para obtener la mision que es requerida
     * @return Eloquent
     */
    public function required_quest()
    {
        return $this->belongs_to("Quest", "complete_required");
    }
}
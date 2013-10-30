<?php

class Item extends Base_Model
{
	public static $softDelete = false;
	public static $timestamps = false;
	public static $table = 'items';
	public static $key = 'id';

	/**
	 *	Obtenemos las monedas dividas en
	 *	oro, plata y cobre de un personaje
	 *
	 *  @param <integer> $coins Cantidad de monedas
	 *	@return <Array> Monedas dividas en oro, plata y cobre
	 */
	public static function get_divided_coins($coins)
	{
		return array(
			'gold' => substr($coins, 0, -4) ? substr($coins, 0, -4) : 0,
			'silver' => substr($coins, -4, -2) ? substr($coins, -4, -2) : 0,
			'copper' => substr($coins, -2) ? substr($coins, -2) : 0,
		);
	}
	
	/**
	 *	@return <array> Array de Skill (model)
	 */
	public function get_skills()
	{
		/*
		 *	Patrón para guardar habilidades:
		 *	skill_id-skill_level
		 *
		 *	Ej.: 2-1 (guardamos el skill con el id 2 y su nivel 1)
		 */
		if ( $this->skill == 0 )
		{
			return array();
		}

		$skillsPattern = explode(';', $this->skill);
		$skills = array();
		$skillId;
		$skillLevel;

		foreach ( $skillsPattern as $skillPattern )
		{
			list($skillId, $skillLevel) = explode('-', $skillPattern);

			if ( $skillId != 0 && $skillLevel > 0 )
			{
				$skills[] = Skill::where('level', '=', (int) $skillLevel)->where('id', '=', (int) $skillId)->first();
			}
		}

		return $skills;
	}

	public function get_text_for_tooltip()
	{
		$message = "<div style='min-width: 250px; text-align: left;'>";

		//$message .= "<img src='" . URL::base() . "/img/icons/items/$this->id.png' class='pull-left' width='32px' height='32px'>";
		
		$message .= "<small class='pull-right' style='color: #AFAFAF;'>";
		switch ( $this->type )
		{
			case 'blunt':
				$message .= 'Mazo';
				break;
				
			case 'bigblunt':
				$message .= 'Mazo de dos manos';
				break;
				
			case 'sword':
				$message .= 'Espada';
				break;
			
			case 'bigsword':
				$message .= 'Espada de dos manos';
				break;
			
			case 'bow':
				$message .= 'Arco';
				break;
			
			case 'dagger':
				$message .= 'Daga';
				break;
			
			case 'staff':
				$message .= 'Váculo';
				break;
			
			case 'bigstaff':
				$message .= 'Váculo de dos manos';
				break;
			
			case 'shield':
				$message .= 'Escudo';
				break;
			
			case 'potion':
				$message .= 'Poción';
				break;
			
			case 'arrow':
				$message .= 'Flecha';
				break;
			
			case 'etc':
				$message .= 'Misceláneo';
				break;
			
			case 'mercenary':
				$message .= 'Mercenario';
				break;
			
			case 'none':
				break;
			
			default:
				$message .= 'Desconocido';
				break;
		}
		$message .= '</small>';
		
		$message .= "<strong style='color: white;'>$this->name</strong>";
		$message .= "<p style='color: #FFC200;'>Requiere nivel $this->level</p>";
		$message .= "<p><small><em>$this->description</em></small></p>";

		$message .= "<ul class='unstyled'>";

		switch ( $this->body_part )
		{
			case 'chest':
				$message .= '<li>Pecho</li>';
				break;

			case 'legs':
				$message .= '<li>Piernas</li>';
				break;

			case 'feet':
				$message .= '<li>Pies</li>';
				break;

			case 'head':
				$message .= '<li>Cabeza</li>';
				break;

			case 'hands':
				$message .= '<li>Manos</li>';
				break;

			case 'lhand':
				$message .= '<li>Mano izquierda</li>';
				break;

			case 'rhand':
				$message .= '<li>Mano derecha</li>';
				break;

			case 'lrhand':
				$message .= '<li>Mano izquierda y derecha</li>';
				break;

			case 'mercenary':
				$message .= '<li>Mercenario</li>';
				break;
		}

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
}
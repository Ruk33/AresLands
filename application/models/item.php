<?php

class Item extends Base_Model
{
	public static $softDelete = false;
	public static $timestamps = false;
	public static $table = 'items';
	public static $key = 'id';

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
				$skills[] = Skill::get($skillId, $skillLevel);
			}
		}

		return $skills;
	}

	public function get_text_for_tooltip()
	{
		$message = "<div style='width: 600px; text-align: left;'>";

		//$message .= "<img src='" . URL::base() . "/img/icons/items/$this->id.png' class='pull-left' width='32px' height='32px'>";

		$message .= "<strong style='color: white;'>$this->name</strong> (<small>$this->type</small>)";
		$message .= "<p style='color: #ADFF00;'>Requiere nivel $this->level</p>";
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

		if ( $this->p_defense != 0 )
		{
			$message .= "<li>Defensa física: $this->p_defense</li>";
		}

		if ( $this->m_defense != 0 )
		{
			$message .= "<li>Defensa mágica: $this->m_defense</li>";
		}

		if ( $this->stat_life != 0 )
		{
			$message .= "<li>Vitalidad: $this->stat_life</li>";
		}

		if ( $this->stat_dexterity != 0 )
		{
			$message .= "<li>Destreza: $this->stat_dexterity</li>";
		}

		if ( $this->stat_magic != 0 )
		{
			$message .= "<li>Magia: $this->stat_magic</li>";
		}

		if ( $this->stat_strength != 0 )
		{
			$message .= "<li>Fuerza: $this->stat_strength</li>";
		}

		if ( $this->stat_luck != 0 )
		{
			$message .= "<li>Suerte: $this->stat_luck</li>";
		}

		$message .= '</ul>';

		$message .= '</div>';

		return $message;
	}
}
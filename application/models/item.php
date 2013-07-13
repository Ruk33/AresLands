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
				$skills[] = Skill::where('id', '=', $skillId)->where('level', '=', $skillLevel)->first();
			}
		}

		return $skills;
	}

	public function get_text_for_tooltip()
	{
		$message = "<div style='width: 600px; text-align: left;'>";

		$message .= "<strong>$this->name</strong> (<small>$this->type</small>)";
		$message .= "<p style='width: 210px;'><em>$this->description</em></p>";

		$message .= '<ul>';

		if ( $this->p_defense != 0 )
		{
			$message .= "<li><b>Defensa física:</b> $this->p_defense</li>";
		}

		if ( $this->m_defense != 0 )
		{
			$message .= "<li><b>Defensa mágica:</b> $this->m_defense</li>";
		}

		if ( $this->stat_life != 0 )
		{
			$message .= "<li><b>Vitalidad:</b> $this->stat_life</li>";
		}

		if ( $this->stat_dexterity != 0 )
		{
			$message .= "<li><b>Destreza:</b> $this->stat_dexterity</li>";
		}

		if ( $this->stat_magic != 0 )
		{
			$message .= "<li><b>Magia:</b> $this->stat_magic</li>";
		}

		if ( $this->stat_strength != 0 )
		{
			$message .= "<li><b>Fuerza:</b> $this->stat_strength</li>";
		}

		if ( $this->stat_luck != 0 )
		{
			$message .= "<li><b>Suerte:</b> $this->stat_luck</li>";
		}

		$message .= '</ul>';

		$message .= '</div>';

		return $message;
	}
}
<?php

class Item extends Base_Model
{
	public static $softDelete = false;
	public static $timestamps = false;
	public static $table = 'items';

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
		$skillsPattern = explode(';', $this->skill);
		$skills = [];
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
		return "<div style='width: 600px; text-align: left;'>
			<strong>$this->name</strong> (<small>$this->type</small>)
			<p>$this->description</p>
			<ul>
				<li>Vitalidad: $this->stat_life</li>
				<li>Destreza: $this->stat_dexterity</li>
				<li>Magia: $this->stat_magic</li>
				<li>Fuerza: $this->stat_strength</li>
				<li>Suerte: $this->stat_luck</li>
			</ul>
		</div>";
	}
}
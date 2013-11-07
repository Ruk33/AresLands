<?php

class Skill extends Base_Model
{
	public static $softDelete = false;
	public static $timestamps = false;
	public static $table = 'skills';
	public static $key = 'id';
	
	/**
	 * 
	 * @return array
	 */
	public function get_required_skills()
	{
		$skillsPattern = $this->get_attribute('required_skills');
		$skills = explode(';', $skillsPattern);
		$requiredSkills = array();
		
		foreach ( $skills as $skill )
		{
			list($skillId, $skillLevel) = explode('-', $skill);
			$requiredSkills[] = array('id' => $skillId, 'level' => $skillLevel);
		}
		
		return $requiredSkills;
	}
	
	/**
	 * Verificamos si un clan puede
	 * aprender una habilidad
	 * @param Clan $clan
	 * @return boolean
	 */
	public function can_be_learned_by_clan(Clan $clan)
	{
		if ( $clan->level < $this->clan_level )
		{
			return false;
		}
		
		foreach ( $this->required_skills as $requiredSkill )
		{
			if ( $requiredSkill['id'] == 0 || $requiredSkill['level'] == 0 )
			{
				continue;
			}
			
			if ( $clan->skills()->where('skill_id', '=', $requiredSkill['id'])->where('level', '<=', $requiredSkill['level'])->take(1)->count() == 0 )
			{
				return false;
			}
		}
		
		return true;
	}
	
	/**
	 * Query para habilidades de clan
	 * @return Eloquent
	 */
	public static function clan_skills()
	{
		return static::where('target', '=', 'clan');
	}
	
	/**
	 * Función que se ejecuta al expirar el timer
	 * (timeout) y al recibir habilidad
	 * @param Character $target
	 */
	public function periodic(CharacterSkill $characterSkill)
	{
        $target = $characterSkill->character;
        
        if ( $characterSkill->end_time != 0 && time() >= $characterSkill->end_time )
        {
            $data = $characterSkill->data;
            $extraStat = $data['extra_stat'];

            $target->update_extra_stat($extraStat, false);

            $characterSkill->delete();

            return;
        }

        if ( mt_rand(0, 100) <= $this->chance )
        {
            $characterSkill->last_execution_time = time();

            $data = $characterSkill->data;
            $extraStat = $data['extra_stat'];

            $strengthBonus = $this->stat_strength * $characterSkill->amount;
            $extraStat['stat_strength'] += $strengthBonus;

            $dexterityBonus = $this->stat_dexterity * $characterSkill->amount;
            $extraStat['stat_dexterity'] += $dexterityBonus;

            $resistanceBonus = $this->stat_resistance * $characterSkill->amount;
            $extraStat['stat_resistance'] += $resistanceBonus;

            $magicBonus = $this->stat_magic * $characterSkill->amount;
            $extraStat['stat_magic'] += $magicBonus;

            $magicSkillBonus = $this->stat_magic_skill * $characterSkill->amount;
            $extraStat['stat_magic_skill'] += $magicSkillBonus;

            $magicResistanceBonus = $this->stat_magic_resistance * $characterSkill->amount;
            $extraStat['stat_magic_resistance'] += $magicResistanceBonus;

            $target->update_extra_stat(array(
                'stat_strength' => $strengthBonus,
                'stat_dexterity' => $dexterityBonus,
                'stat_resistance' => $resistanceBonus,
                'stat_magic' => $magicBonus,
                'stat_magic_skill' => $magicSkillBonus,
                'stat_magic_resistance' => $magicResistanceBonus
            ), true);

            // Usar formula de batalla!
            $target->current_life -= $this->physical_damage * $characterSkill->amount;
            $target->current_life -= $this->magical_damage * $characterSkill->amount;

            $target->current_life += $this->life * $characterSkill->amount;

            $data['extra_stat'] = $extraStat;
            $characterSkill->data = $data;

            $characterSkill->save();
            $target->save();
        }
    }
	
	/**
	 * Lanzamos habilidad
	 * @param Character $caster
	 * @param Character $target
	 * @param int $amount
	 * @return boolean
	 */
	public function cast(Character $caster, Character $target, $amount = 1)
	{
		if ( ! $caster || ! $target )
		{
			return false;
		}
		
		if ( $caster->level < $this->min_level_required )
		{
			return false;
		}
		
		switch ( $this->target )
		{
			case 'none':
				return false;
				break;
			
			case 'one':
				break;
			
			case 'self':
				if ( $caster != $target )
				{
					return false;
				}
				
				break;
			
			case 'clan':
				// El objetivo debe estar en el mismo clan
				// que el caster
				if ( $caster->clan_id != $target->clan_id )
				{
					return false;
				}
				
				break;
		}
		
		switch ( $target->race )
		{
			case 'dwarf':
				if ( $this->dwarf != 'both' && $this->dwarf != $target->gender )
				{
					return false;
				}
				
				break;
				
			case 'human':
				if ( $this->human != 'both' && $this->human != $target->gender )
				{
					return false;
				}
				
				break;
				
			case 'elf':
				if ( $this->elf != 'both' && $this->elf != $target->gender )
				{
					return false;
				}
				
				break;
				
			case 'drow':
				if ( $this->drow != 'both' && $this->drow != $target->gender )
				{
					return false;
				}
				
				break;
		}
		
		// Las habilidades que no stackean solamente
		// pueden tener 1 de cantidad
		if ( ! (bool) $this->stackable && $amount > 1 )
		{
			$amount = 1;
		}
		
		if ( $this->duration != -1 )
		{
			$this->periodic(CharacterSkill::register($target, $this, $amount));
		}
		else
		{
			// Usar formula de batalla!
			$target->current_life -= $this->physical_damage * $amount;
			$target->current_life -= $this->magical_damage * $amount;

			$target->current_life += $this->life * $amount;
			
			$target->save();
		}
		
		return true;
	}
	
	/**
	 * 
	 * @return string
	 */
	public function get_tooltip()
	{
		$message = "<div style='width: 350px; text-align: left;'>";
		
		$message .= "<p><b>$this->name</b> - Nivel: $this->level</p>";
		$message .= "<p>$this->description</p>";
		$message .= "<p class='negative'>$this->requirements_text</p>";
		
		$message .= "</div>";
		
		return $message;
	}
}
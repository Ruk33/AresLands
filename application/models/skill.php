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
			$requiredSkills[] = array('id' => (int) $skillId, 'level' => (int) $skillLevel);
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
			
            $skill = Skill::where('id', '=', $requiredSkill['id'])->where('level', '=', $requiredSkill['level'])->first();
            
			if ( ! $skill || ! $clan->has_skill($skill) )
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
	public static function periodic(CharacterSkill $characterSkill)
	{
        $target = $characterSkill->character;
        
        $data = $characterSkill->data;
        $extraStat = $data['extra_stat'];
        
        $time = time();
        
        if ( $characterSkill->end_time != 0 && $time >= $characterSkill->end_time )
        {
			if ( $characterSkill->skill_id == Config::get('game.invisibility_skill') )
			{
				$target->invisible_until = 0;
				$target->save();
			}
			
            $target->update_extra_stat($extraStat, false);
            $characterSkill->delete();

            return;
        }
        
        $skill = $characterSkill->skill()->select(array(
			'id',
			'level',
			'physical_damage',
			'magical_damage',
			'stat_strength',
			'stat_dexterity',
			'stat_resistance',
			'stat_magic',
			'stat_magic_skill',
			'stat_magic_resistance',
			'luck',
			'evasion',
			'magic_defense',
			'physical_defense',
			'critical_chance',
			'attack_speed',
			'life',
			'regeneration_per_second',
			'reflect_damage',
			'reflect_magic_damage',
			'travel_time',
			'battle_rest',
			'xp_rate',
			'quest_xp_rate',
			'drop_rate',
			'explore_reward_rate',
			'coin_rate',
			'quest_coin_rate',
			'skill_cd_time',
			'chance'
		))->first();

        if ( mt_rand(0, 100) <= $skill->chance )
        {
            $characterSkill->last_execution_time = $time;
			
			$skillAtributes = $skill->to_array();
			$amount = $characterSkill->amount;
			$bonus = array();
			
			foreach ( $skillAtributes as $key => $value )
			{
				if ( in_array($key, array('life', 'id', 'level', 'chance')) )
				{
					continue;
				}
				
				$bonus[$key] = $value * $amount;
				
				if ( ! isset($extraStat[$key]) )
				{
					$extraStat[$key] = 0;
				}
				
				$extraStat[$key] += $bonus[$key];
			}
			
            $target->update_extra_stat($bonus, true);

            // Usar formula de batalla!
            //$target->current_life -= $skill->physical_damage * $characterSkill->amount;
            //$target->current_life -= $skill->magical_damage * $characterSkill->amount;

            $target->current_life += $skill->life * $amount;

            $data['extra_stat'] = $extraStat;
            $characterSkill->data = $data;

            $characterSkill->save();
            $target->save();
        }
    }
	
	public function can_be_casted(Character $caster, Character $target)
	{
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
			
			case 'notself':
				if ( $caster->id == $target->id )
				{
					return false;
				}
				
				break;
			
			case 'self':
				if ( $caster->id != $target->id )
				{
					return false;
				}
				
				break;
			
			case 'allClan':
				if ( $caster->id != $target->id )
				{
					return false;
				}
				
				break;
				
			case 'clan':
				if ( ! $caster->clan_id )
				{
					return false;
				}
				
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

		if ( $this->target != 'clan' )
		{
			if ( $target->zone_id != $caster->zone_id )
			{
				return false;
			}

			if ( $caster->has_skill(Config::get('game.silence_skill')) )
			{
				return false;
			}
		}
		
		if ( $this->type == 'debuff' && $target->has_skill(Config::get('game.anti_magic_skill')) )
		{
			return false;
		}
		
		return true;
	}
	
	/**
	 * Obtenemos query para habilidad aleatoria
	 * @return Eloquent
	 */
	public static function get_random()
	{
		return self::where('can_be_random', '=', true)
				   ->order_by(DB::raw('RAND()'));
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
		if ( ! $this->can_be_casted($caster, $target) )
		{
			return false;
		}
		
		// Las habilidades que no stackean solamente
		// pueden tener 1 de cantidad
		if ( ! (bool) $this->stackable && $amount > 1 )
		{
			$amount = 1;
		}
		
		if ( $this->id == Config::get('game.clean_skill') )
		{
			$target->remove_debuffs(2);
			return true;
		}
		
		if ( $this->id == Config::get('game.cure_skill') )
		{
			$target->remove_debuffs(1);
			return true;
		}
		
		if ( $this->id == Config::get('game.mastery_skill') )
		{
			$skill = self::get_random()->first();
			
			if ( $skill )
			{
				return $skill->cast($caster, $target);
			}
		}
		
		if ( $this->id == Config::get('game.ongoing_skill') )
		{
			if ( $caster->clan_id > 0 )
			{
				$members = $caster->clan->members;
				$time = time();
				
				foreach ( $members as $member )
				{
					$randomTalent = $member->get_random_talent()
										   ->where('usable_at', '>', $time)
										   ->first();
					
					if ( $randomTalent )
					{
						$member->refresh_talent($randomTalent);
					}
				}
			}
			
			return true;
		}
		
		if ( $this->id == Config::get('game.invisibility_skill') )
		{
			// La duracion de las habilidades esta en minutos
			// por lo que tenemos que pasarlos a segundos
			$target->invisible_until += $this->duration * 60;
			$target->save();
		}
		
		if ( $target->has_skill(Config::get('game.reflect_skill')) && $this->type == 'debuff' )
		{			
			$target = &$caster;
		}
		
		if ( $this->target == 'allClan' )
		{
			$skill = $this;
			
			if ( $skill->id == Config::get('game.concede_skill') )
			{
				$skill = Skill::find(Config::get('game.concede_member_skill'));
			}
			
			// Evitamos recursion infinita
			$skill->target = 'one';
			
			$members = $caster->clan->members;
			
			foreach ( $members as $member )
			{
				if ( $member->id != $caster->id )
				{
					$skill->cast($caster, $member);
				}
			}
		}

		if ( $this->id == Config::get('game.invocation') )
		{
			$second_mercenary = Item::where('class', '=', 'mercenary')
				->where('level', '>=', $target->level / 2)
				->order_by(DB::raw('RAND()'))
				->select(array('id'))
				->first();

			$target->second_mercenary = $second_mercenary->id;
			$target->save();
		}
		
		if ( $this->duration != -1 )
		{
			CharacterSkill::register($target, $this, $amount);
		}
		else
		{
			// Usar formula de batalla
			$target->current_life -= $this->direct_magic_damage * $amount;
			$target->current_life -= $this->direct_physical_damage * $amount;
			
			$target->current_life += $this->life * $amount;
			
			if ( $this->id == Config::get('game.stone_skill') )
			{
				if ( mt_rand(0, 100) <= $this->chance )
				{
					Skill::cast_stun_on($target);
				}
			}
			
			$target->save();
		}
		
		return true;
	}
	
	/**
	 * Lanzamos stun sobre un personaje
	 * @param Character $target
	 */
	public static function cast_stun_on(Character $target)
	{
		$skill = Skill::where_id(Config::get('game.stun_skill'))->where_level(1)->first();
		
		if ( $skill )
		{
			$skill->cast($target, $target);
		}
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
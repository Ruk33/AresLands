<?php

class Clan extends Base_Model
{
	public static $softDelete = false;
	public static $timestamps = false;
	public static $table = 'clans';
	public static $key = 'id';
	
	/**
	 * @var <integer> Permiso para aceptar peticiones de ingreso
	 */
	const PERMISSION_ACCEPT_PETITION = 1;
	
	/**
	 * @var <integer> Permiso para declinar peticiones de ingreso
	 */
	const PERMISSION_DECLINE_PETITION = 2;
	
	/**
	 * @var <integer> Permiso para expulsar a un miembro
	 */
	const PERMISSION_KICK_MEMBER = 4;
	
	/**
	 * @var <integer> Permiso para aprender una habilidad
	 */
	const PERMISSION_LEARN_SPELL = 8;
	
	/**
	 * @var <integer> Permiso para editar el mensaje del clan
	 */
	const PERMISSION_EDIT_MESSAGE = 16;
	
	/**
	 * @var integer Permiso para registrar grupo en torneo
	 */
	const PERMISSION_REGISTER_TOURNAMENT = 32;

	protected $rules = array(
		'name' => 'required|between:3,35|unique:clans',
		'message' => 'between:0,1000',
	);

	protected $messages = array(
		'name_required' => 'El nombre es requerido',
		'name_between' => 'El nombre debe tener entre 3 y 35 carácteres',
		'name_unique' => 'Ya hay un grupo con ese nombre',

		'message_between' => 'El mensaje del grupo puede tener hasta 1000 caracteres',
	);
	
	/**
	 * Verificamos si el personaje tiene un permiso
	 * en específico
	 * 
	 * @param <Character> $character
	 * @param <integer> $permission
	 * @return <boolean>
	 */
	public function has_permission(Character $character, $permission)
	{
		return ($character->id == $this->leader_id || (bool) ($character->clan_permission & $permission)) && $character->clan_id == $this->id;
	}
	
	/**
	 * Agregamos permiso a personaje
	 * 
	 * @param <Character> $character
	 * @param <integer> $permission
	 * @param <boolean> $save true para guarda el registro (util cuando se hacen muchas modificaciones)
	 */
	public function add_permission(Character $character, $permission, $save = true)
	{
		$character->clan_permission |= $permission;
		
		if ( $save )
		{
			$character->save();
		}
	}
	
	/**
	 * Removemos permiso a personaje
	 * 
	 * @param <Character> $character
	 * @param <integer> $permission
	 * @param <boolean> $save true para guarda el registro (util cuando se hacen muchas modificaciones)
	 */
	public function revoke_permission(Character $character, $permission, $save = true)
	{
		$character->clan_permission &= ~$permission;
		
		if ( $save )
		{
			$character->save();
		}
	}
    
    /**
     * Verificamos si un clan tiene una habilidad
     * @param Skill $skill
     * @return boolean
     */
    public function has_skill(Skill $skill)
    {
        return $this->skills()->where('skill_id', '=', $skill->id)->where('level', '=', $skill->level)->take(1)->count() == 1;
    }

	public function add_xp($amount)
	{
		if ( $this && $this->level < Config::get('game.clan_max_level') )
		{
			$this->xp += $amount;

			if ( $this->xp >= $this->xp_next_level )
			{
				$this->level++;
				$this->points_to_change++;

				if ( $this->level < Config::get('game.clan_max_level') )
				{
					$this->xp_next_level = $this->xp_next_level + 10 * $this->level;
				}
			}

			$this->save();
		}
	}

	public function remove_clan_skill_from_member(Character $member, Skill $skill)
	{
		$memberSkill = $member->skills()->where('skill_id', '=', $skill->id)->where('level', '=', $skill->level)->first();

		if ( $memberSkill )
		{
			$memberSkill->end_time = 1;
			$memberSkill->save();
			
			$skill->periodic($memberSkill);
		}
	}

	public function remove_clan_skills_from_member(Character $member)
	{
		$clanSkills = $this->skills()->select(array('id', 'skill_id', 'level'))->get();

		foreach ( $clanSkills as $clanSkill )
		{
			$skill = Skill::where('id', '=', $clanSkill->skill_id)->where('level', '=', $clanSkill->level)->first();
			$this->remove_clan_skill_from_member($member, $skill);
		}
	}

	public function give_clan_skill_to_member(Character $member, Skill $skill)
	{
		if ( $member->skills()->where('skill_id', '=', $skill->id)->where('level', '=', $skill->level)->take(1)->count() == 0 )
		{
			$skill->cast($this->lider()->select(array('id', 'clan_id'))->first(), $member, 1);
		}
	}

	public function give_clan_skills_to_member(Character $member)
	{
		$clanSkills = $this->skills()->select(array('skill_id', 'level'))->get();

		foreach ( $clanSkills as $clanSkill )
		{
			$skill = Skill::where('id', '=', $clanSkill->skill_id)->where('level', '=', $clanSkill->level)->first();
			$this->give_clan_skill_to_member($member, $skill);
		}
	}

	public function update_members_skill(Skill $skill)
	{
		$members = $this->members()->select(array('id', 'clan_id'))->get();
		$prevSkill = Skill::where('id', '=', $skill->id)->first();

		foreach ( $members as $member )
		{
			if ( $prevSkill )
			{
				$this->remove_clan_skill_from_member($member, $prevSkill);
			}
			
			$this->give_clan_skill_to_member($member, $skill);
		}
	}

	public function learn_skill(Skill $skill)
	{
		$clanSkill = $this->skills()->where('skill_id', '=', $skill->id)->select(array('id', 'skill_id', 'level'))->first();

		if ( $clanSkill )
		{
			$clanSkill->level++;
		}
		else
		{
			$clanSkill = new ClanSkill();

			$clanSkill->clan_id = $this->id;
			$clanSkill->skill_id = $skill->id;
			$clanSkill->level = $skill->level;
		}

		$clanSkill->save();
		$this->update_members_skill($skill);
	}

	public function join(Character $member)
	{
		$member->clan_permission = 0;
		$member->save();
		
		$this->give_clan_skills_to_member($member);
	}

	public function leave(Character $member)
	{
		$member->clan_permission = 0;
		$member->save();
		
 		$this->remove_clan_skills_from_member($member);
	}

	public function delete()
	{
		$this->leave($this->lider()->select(array('id'))->first());

		$this->skills()->delete();
		$this->petitions()->delete();

		parent::delete();
	}

	public function skills()
	{
		return $this->has_many('ClanSkill', 'clan_id');
	}

	public function members()
	{
		return $this->has_many('Character', 'clan_id');
	}

	public function get_link()
	{
		return '<a href="' . URL::to('authenticated/clan/' . $this->id ) . '">' . htmlspecialchars($this->name) . '</a>';
	}

	public function lider()
	{
		return $this->belongs_to('Character', 'leader_id');
	}

	public function petitions()
	{
		return $this->has_many('ClanPetition', 'clan_id');
	}
}
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
	 * Verificamos si personaje puede ver las peticiones del grupo
	 * 
	 * @param Character $character
	 * @return boolean
	 */
	public function can_see_petitions(Character $character)
	{
		return $this->can_accept_petitions($character) || $this->can_reject_petitions($character);
	}
	
	/**
	 * 
	 * @param array $attributes
	 * @return Clan
	 */
	public static function create_instance(Array $attributes = array())
	{
		return new static($attributes);
	}
	
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
        return $this->learned_skills()
					->where('skill_id', '=', $skill->id)
					->where('level', '=', $skill->level)
					->take(1)
					->count() == 1;
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
	
	public function can_learn_skill(Skill $skill)
	{
		return $this->points_to_change > 0 && ! $this->has_skill($skill) && $skill->can_be_learned_by_clan($this);
	}

	public function learn_skill(Skill $skill)
	{
		$clanSkill = $this->learned_skills()->where('skill_id', '=', $skill->id)->select(array('id', 'skill_id', 'level'))->first();

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
		
		$this->points_to_change--;
		$this->save();
	}
	
	/**
	 * Verificamos si personaje puede unirse al grupo
	 * @param Character $character
	 * @return boolean
	 */
	public function can_join(Character $character)
	{
		return $character->clan_id == 0;
	}
	
	/**
	 * Verificamos si personaje puede enviar peticiones de ingreso al grupo
	 * @param Character $character
	 */
	public function can_send_petition(Character $character)
	{
		if ( ! $this->can_join($character) )
		{
			return false;
		}
		
		// Verificamos si ya ha enviado peticiones
		if ( $this->petitions()->where_character_id($character->id)->take(1)->count() != 0 )
		{
			return false;
		}
		
		return true;
	}
	
	/**
	 * Enviamos peticion de ingreso de personaje
	 * @param Character $character
	 */
	public function send_petition(Character $character)
	{		
		$petition = new ClanPetition();

		$petition->clan_id = $this->id;
		$petition->character_id = $character->id;

		$petition->save();
		
		Message::clan_new_petition($character, $this->lider);
	}

	/**
	 * Ingresar personaje a grupo
	 * @param Character $member
	 */
	public function join(Character $member)
	{
		$member->petitions()->delete();
		
		$member->clan_id = $this->id;
		$member->clan_permission = 0;
		$member->save();
		
		$this->give_clan_skills_to_member($member);
	}
	
	/**
	 * Verificamos si personaje puede alterar permisos
	 * @param Character $character
	 * @return boolean
	 */
	public function can_modify_permissions(Character $character)
	{
		return $this->leader_id == $character->id;
	}
	
	/**
	 * Verificamos si personaje puede aceptar peticiones de inclusion al grupo
	 * @param Character $character
	 * @return boolean
	 */
	public function can_accept_petitions(Character $character)
	{
		return $this->has_permission($character, self::PERMISSION_ACCEPT_PETITION);
	}
	
	/**
	 * Aceptamos peticion de inclusion al grupo
	 * @param Character $character
	 * @param ClanPetition $petition
	 * @return boolean
	 */
	public function accept_petition(Character $character, ClanPetition $petition)
	{
		$newMember = $petition->character;
		
		if ( $this->can_join($newMember) )
		{
			$this->join($newMember);
			Message::clan_accept_message($character, $newMember, $this);
			
			return true;
		}
		
		return false;
	}
	
	/**
	 * Verificamos si personaje puede rechazar peticiones de inclusion al grupo
	 * @param Character $character
	 * @return boolean
	 */
	public function can_reject_petitions(Character $character)
	{
		return $this->has_permission($character, self::PERMISSION_DECLINE_PETITION);
	}
	
	/**
	 * Rechazamos peticion de inclusion al grupo
	 * @param Character $character
	 * @param ClanPetition $petition
	 */
	public function reject_petition(Character $character, ClanPetition $petition)
	{
		Message::clan_reject_message($character, $petition->character, $this);
		$petition->delete();
	}
	
	/**
	 * Verificamos si personaje puede expulsar a otro del grupo
	 * @param Character $character
	 * @param Character $member
	 * @return boolean
	 */
	public function can_kick_member(Character $character, Character $member)
	{
		if ( $member->clan_id != $character->clan_id )
		{
			return false;
		}
		
		if ( ! $this->has_permission($character, self::PERMISSION_KICK_MEMBER) )
		{
			return false;
		}
		
		if ( $character->id == $member->id )
		{
			return false;
		}
		
		if ( $this->leader_id == $member->id )
		{
			return false;
		}
		
		if ( Tournament::is_active() )
		{
			$tournament = Tournament::get_active()->first();

			if ( $tournament->is_clan_registered($this) )
			{
				return false;
			}
		}
		
		return true;
	}
	
	/**
	 * Sacamos a un personaje del grupo
	 * @param Character $performer El personaje que saco al miembro del grupo
	 * @param Character $member
	 */
	public function kick_member(Character $performer, Character $member)
	{
		$this->leave($member);
		Message::clan_expulsion_message($performer, $member);
	}
	
	/**
	 * Verificamos si personaje puede salir de clan
	 * @param Character $character
	 * @return boolean
	 */
	public function can_leave(Character $character)
	{
		if ( $character->clan_id != $this->id )
		{
			return false;
		}
		
		if ( $this->leader_id == $character->id )
		{
			return false;
		}
		
		if ( Tournament::is_active() )
		{
			$tournament = Tournament::get_active()->first();
			
			if ( $tournament->is_clan_registered($this) )
			{
				return false;
			}
		}
		
		return true;
	}

	/**
	 * Sacar personaje del grupo
	 * @param Character $member
	 */
	public function leave(Character $member)
	{
		$member->cancel_all_clan_trades();

		$member->clan_id = 0;
		$member->clan_permission = 0;
		$member->save();
		
 		$this->remove_clan_skills_from_member($member);
	}
	
	/**
	 * Verificamos si personaje puede borrar grupo
	 * @param Character $character
	 * @return boolean
	 */
	public function can_delete(Character $character)
	{
		if ( $character->clan_id != $this->id )
		{
			return false;
		}
		
		if ( $character->id != $this->leader_id )
		{
			return false;
		}
		
		if ( $this->members()->count() != 1 )
		{
			return false;
		}
		
		if ( Tournament::is_active() )
		{
			$tournament = Tournament::get_active()->first();

			if ( $tournament->is_clan_registered($this) )
			{
				return false;
			}
		}
		
		return true;
	}

	public function delete()
	{
		$this->leave($this->lider);

		$this->learned_skills()->delete();
		$this->petitions()->delete();
		$this->orb_points()->delete();

		return parent::delete();
	}
	
	/**
	 * Query para obtener habilidades de clan
	 * @return Eloquent
	 */
	public function get_skills()
	{
		return Skill::clan_skills();
	}
	
	/**
	 * Query para obtener habilidades no aprendidas del clan
	 * @return Eloquent
	 */
	public function get_non_learned_skills()
	{
		$learnedSkills = DB::raw("( SELECT skill_id FROM clan_skills WHERE clan_id = $this->id )");
		return static::get_skills()
					 ->where('level', '=', 1)
					 ->where('id', 'NOT IN', $learnedSkills);
	}

	/**
	 * Query para obtener habilidades aprendidas del clan
	 * @return Eloquent
	 */
	public function learned_skills()
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
	
	public function orb_points()
	{
		return $this->has_one('ClanOrbPoint', 'clan_id');
	}
	
	public function save()
	{
		$exists = $this->exists;
		$result = parent::save();
		
		// Si no existe, entonces lo agregamos al ranking de clanes
		if ( ! $exists )
		{
			$clanRanking = new ClanOrbPoint();
			
			$clanRanking->clan_id = $this->id;
			$clanRanking->points = 0;
			
			$clanRanking->save();
		}
		
		return $result;
	}
}
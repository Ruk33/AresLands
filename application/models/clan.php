<?php

class Clan extends Base_Model
{
	public static $softDelete = false;
	public static $timestamps = false;
	public static $table = 'clans';
	public static $key = 'id';

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

	public function add_xp($amount)
	{
		if ( $this )
		{
			$this->xp += $amount;

			if ( $this->xp >= $this->xp_next_level )
			{
				$this->level++;
				$this->points_to_change++;
				$this->xp_next_level = $this->xp_next_level + 10 * $this->level;
			}

			$this->save();
		}
	}

	public function remove_clan_skill_from_member(Character $member, ClanSkill $clanSkill)
	{
		$memberSkill = $member->skills()->where('skill_id', '=', $clanSkill->skill_id)->first();

		if ( $memberSkill )
		{
			$memberSkill->delete();
		}
	}

	public function remove_clan_skills_from_member(Character $member)
	{
		$clanSkills = $this->skills;

		foreach ( $clanSkills as $clanSkill )
		{
			$this->remove_clan_skill_from_member($member, $clanSkill);
		}
	}

	public function give_clan_skill_to_member(Character $member, ClanSkill $clanSkill)
	{
		if ( $member->skills()->where('skill_id', '=', $clanSkill->skill_id)->take(1)->count() == 0 )
		{
			$characterSkill = new CharacterSkill();

			$characterSkill->skill_id = $clanSkill->skill_id;
			$characterSkill->level = $clanSkill->level;
			$characterSkill->character_id = $member->id;
			$characterSkill->end_time = 0;
			$characterSkill->amount = 1;

			$characterSkill->save();
		}
	}

	public function give_clan_skills_to_member(Character $member)
	{
		$clanSkills = $this->skills;

		foreach ( $clanSkills as $clanSkill )
		{
			$this->give_clan_skill_to_member($member, $clanSkill);
		}
	}

	public function update_members_skill(ClanSkill $clanSkill)
	{
		$members = $this->members->select(array('id'))->get();

		foreach ( $members as $member )
		{
			$memberSkill = $member->skills()->where('skill_id', '=', $skill->skill_id)->select(array('id', 'level'))->first();

			if ( $memberSkill )
			{
				$memberSkill->level = $clanSkill->level;
				$memberSkill->save();
			}
			else
			{
				$this->give_clan_skill_to_member($member, $clanSkill);
			}
		}
	}

	public function learn_skill(Skill $skill)
	{
		$clanSkill = $this->skills()->where('skill_id', '=', $skill->skill_id)->select(array('id', 'level'))->first();

		if ( $clanSkill )
		{
			$clanSkill->level++;
		}
		else
		{
			$clanSkill = new ClanSkill();

			$clanSkill->clan_id = $this->id;
			$clanSkill->skill_id = $skill->skill_id;
			$clanSkill->level = 1;
		}

		$clanSkill->save();
		$this->update_members_skill($skill);
	}

	public function join(Character $member)
	{
		$this->give_clan_skills_to_member($member);
	}

	public function leave(Character $member)
	{
 		$this->remove_clan_skills_from_member($member);
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
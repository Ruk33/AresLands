<?php

class Skill extends Base_Model {

    public static $softDelete = false;
    public static $timestamps = false;
    public static $table = 'skills';
    public static $factory = array();

    /**
     * Obtenemos los skills de personalidad en array cuyo key es el nombre de la personalidad
     * Ejemplo: array('nombre_personalidad' => array(skills))
     * @param string|array $characteristics
     * @return array
     */
    public function get_talents($characteristics) {
        $talents = array();

        foreach ((array) $characteristics as $characteristic) {
            if (is_string($characteristic)) {
                $characteristic = Characteristic::get($characteristic);
            }

            if ($characteristic) {
                $talents[$characteristic->get_name()] = static::where_in('id', $characteristic->get_skills())->get();
            }
        }

        return $talents;
    }

    /**
     * Obtenemos las raciales de una raza
     * @param string $race
     * @return array
     */
    public function get_racials($race) {
        $racialSkills = Config::get('game.racial_skills');

        if (!isset($racialSkills[$race])) {
            return array();
        }

        return static::where_in('id', $racialSkills[$race])->get();
    }

    public function get_next_level() {
        return static::where('id', '=', $this->id)
                        ->where('level', '=', $this->level + 1);
    }

    /**
     * 
     * @return array
     */
    public function get_required_skills() {
        $skillsPattern = $this->get_attribute('required_skills');
        $skills = explode(';', $skillsPattern);
        $requiredSkills = array();

        foreach ($skills as $skill) {
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
    public function can_be_learned_by_clan(Clan $clan) {
        if ($clan->level < $this->clan_level) {
            return false;
        }

        foreach ($this->required_skills as $requiredSkill) {
            if ($requiredSkill['id'] == 0 || $requiredSkill['level'] == 0) {
                continue;
            }

            $skill = Skill::where('id', '=', $requiredSkill['id'])->where('level', '=', $requiredSkill['level'])->first();

            if (!$skill || !$clan->has_skill($skill)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Query para habilidades de clan
     * @return Eloquent
     */
    public static function clan_skills() {
        return static::where('target', '=', 'clan');
    }

    /**
     * Función que se ejecuta al expirar el timer
     * (timeout) y al recibir habilidad
     * @param CharacterSkill $characterSkill
     */
    public static function periodic(CharacterSkill $characterSkill) {
        $target = $characterSkill->character;

        if ($characterSkill->is_over()) {
            if ($characterSkill->skill_id == Config::get('game.invisibility_skill')) {
                $target->invisible_until = 0;
                $target->save();
            }

            $characterSkill->delete();
        } else {
            $skill = $characterSkill->skill;

            // Usar formula de batalla!
            //$target->current_life -= $skill->physical_damage * $characterSkill->amount;
            //$target->current_life -= $skill->magical_damage * $characterSkill->amount;

            $target->current_life += $skill->life * $characterSkill->amount;

            $characterSkill->save();
            $target->save();
        }
    }

    public function can_be_casted(Character $caster, Character $target) {
        if ($caster->level < $this->min_level_required) {
            return false;
        }

        switch ($this->target) {
            case 'none':
                return false;
                break;

            case 'one':
                break;

            case 'notself':
                if ($caster->id == $target->id) {
                    return false;
                }

                break;

            case 'self':
                if ($caster->id != $target->id) {
                    return false;
                }

                break;

            case 'allClan':
                if ($caster->id != $target->id) {
                    return false;
                }

                break;

            case 'clan':
                if (!$caster->clan_id) {
                    return false;
                }

                if ($caster->clan_id != $target->clan_id) {
                    return false;
                }

                break;
        }

        switch ($target->race) {
            case 'dwarf':
                if ($this->dwarf != 'both' && $this->dwarf != $target->gender) {
                    return false;
                }

                break;

            case 'human':
                if ($this->human != 'both' && $this->human != $target->gender) {
                    return false;
                }

                break;

            case 'elf':
                if ($this->elf != 'both' && $this->elf != $target->gender) {
                    return false;
                }

                break;

            case 'drow':
                if ($this->drow != 'both' && $this->drow != $target->gender) {
                    return false;
                }

                break;
        }

        if ($this->target != 'clan') {
            if ($target->zone_id != $caster->zone_id) {
                return false;
            }

            if ($caster->has_skill(Config::get('game.silence_skill'))) {
                return false;
            }
        }

        if ($this->type == 'debuff' && $target->has_skill(Config::get('game.anti_magic_skill'))) {
            return false;
        }

        return true;
    }

    /**
     * Obtenemos query para habilidad aleatoria
     * @return Eloquent
     */
    public static function get_random() {
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
    public function cast(Character $caster, Character $target, $amount = 1) {
        if (!$this->can_be_casted($caster, $target)) {
            return false;
        }

        if ($caster->server_id != $target->server_id) {
            return false;
        }

        // Las habilidades que no stackean solamente
        // pueden tener 1 de cantidad
        if (!(bool) $this->stackable && $amount > 1) {
            $amount = 1;
        }

        if ($this->id == Config::get('game.clean_skill')) {
            $target->remove_debuffs(2);
            return true;
        }

        if ($this->id == Config::get('game.cure_skill')) {
            $target->remove_debuffs(1);
            return true;
        }

        if ($this->id == Config::get('game.mastery_skill')) {
            $skill = self::get_random()->first();

            if ($skill) {
                return $skill->cast($caster, $target);
            }
        }

        if ($this->id == Config::get('game.ongoing_skill')) {
            if ($caster->clan_id > 0) {
                $members = $caster->clan->members;
                $time = time();

                foreach ($members as $member) {
                    $randomTalent = $member->get_random_talent()
                            ->where('usable_at', '>', $time)
                            ->first();

                    if ($randomTalent) {
                        $member->refresh_talent($randomTalent);
                    }
                }
            }

            return true;
        }

        if ($this->id == Config::get('game.invisibility_skill')) {
            // La duracion de las habilidades esta en minutos
            // por lo que tenemos que pasarlos a segundos
            $target->invisible_until += $this->duration * 60;
            $target->save();
        }

        if ($target->has_skill(Config::get('game.reflect_skill')) && $this->type == 'debuff') {
            $target = &$caster;
        }

        if ($this->target == 'allClan') {
            $skill = $this;

            if ($skill->id == Config::get('game.concede_skill')) {
                $skill = Skill::find(Config::get('game.concede_member_skill'));
            }

            // Evitamos recursion infinita
            $skill->target = 'one';

            $members = $caster->clan->members;

            foreach ($members as $member) {
                if ($member->id != $caster->id) {
                    $skill->cast($caster, $member);
                }
            }
        }

        if ($this->id == Config::get('game.invocation')) {
            $second_mercenary = Item::get_random_secondary_mercenary($target)
                    ->select(array('id'))
                    ->first();

            if ($second_mercenary) {
                $target->second_mercenary = $second_mercenary->id;
                $target->save();
            }
        }

        if ($this->duration != -1) {
            CharacterSkill::register($target, $this, $amount);
        } else {
            // Usar formula de batalla
            $target->current_life -= $this->direct_magic_damage * $amount;
            $target->current_life -= $this->direct_physical_damage * $amount;

            $target->current_life += $this->life * $amount;

            if ($this->id == Config::get('game.stone_skill')) {
                if (mt_rand(0, 100) <= $this->chance) {
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
    public static function cast_stun_on(Character $target) {
        $skill = Skill::where_id(Config::get('game.stun_skill'))->where_level(1)->first();

        if ($skill) {
            $skill->cast($target, $target);
        }
    }

    /**
     * Obtenemos path de la imagen de la habilidad
     * @return string
     */
    public function get_image_path() {
        return URL::base() . '/img/icons/skills/' . $this->id . '.png';
    }

    /**
     * 
     * @return string
     */
    public function get_tooltip() {
        $message = "<div style='width: 350px; text-align: left;'>";

        $message .= "<img src='{$this->get_image_path()}' class='pull-left' style='margin-right: 15px;' />";
        $message .= "<p><b>$this->name</b> - Nivel: $this->level</p>";
        $message .= "<p>$this->description</p>";
        $message .= "<p class='negative'>$this->requirements_text</p>";

        $message .= "</div>";

        return $message;
    }

}

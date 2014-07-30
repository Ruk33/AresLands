<?php

class MonsterDamage extends Damage
{
    public function get_damage()
    {
        if ($this->is_magical()) {
            return $this->get_attacker()->get_final_magic() / 10 * 1.3;
        } else {
            return $this->get_attacker()->get_final_strength() / 12 * 1.2;
        }
    }
}
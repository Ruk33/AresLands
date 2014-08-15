<?php

class SethArmor extends MonsterArmor
{
    public function get_miss_chance(Damage $damage)
    {
        return 25;
    }
}

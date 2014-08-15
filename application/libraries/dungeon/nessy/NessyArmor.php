<?php

class NessyArmor extends MonsterArmor
{
    public function get_miss_chance(Damage $damage)
    {
        return 35;
    }
}

<?php

class LohgArmor extends MonsterArmor
{
    public function before(Damage $damage, Battle $battle)
    {
        $attacker = $damage->get_attacker();
        $lohg = $this->get_defender();
        
        // Devolvemos el 10% de daño
        $lohg->get_combat_behavior()
             ->get_damage()
             ->normal_with_amount($attacker, false, $damage->get_amount() * 0.1);
    }
}

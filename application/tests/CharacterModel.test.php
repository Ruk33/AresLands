<?php

class CharacterModel extends PHPUnit_Framework_TestCase {

	public $character;

	public function __construct()
	{
		$this->character = Character::find(1);
	}

	public function testBatallaContraJugador()
	{
		$target = Character::find(2);
		$targetPvpPoints = $target->pvp_points;

		$characterPvpPoints = $this->character->pvp_points;

		$battle = $this->character->battle_against($target);

		if ( $battle['winner']->id == $this->character->id )
		{
			$this->assertEquals($characterPvpPoints+1, $this->character->pvp_points);
		}
		else
		{
			$this->assertEquals($targetPvpPoints+1, $target->pvp_points);
		}
	}

	public function testEquiparArma()
	{
		// comprobamos que no tenga arma
		$this->assertEquals(null, $this->character->get_equipped_weapon());

		// arma a equipar
		$weapon = $this->character->items()->find(8);
		// equipamos
		$this->character->equip_item($weapon);

		// confirmamos que se equipó
		$this->assertEquals($weapon->id, $this->character->get_equipped_weapon()->id);
	}

	public function testSacarArma()
	{
		// arma equipada
		$weapon = $this->character->get_equipped_weapon();

		// no debe ser null (acabamos de equipar una)
		$this->assertNotNull($weapon);

		// se la sacamos
		$this->character->unequip_item($weapon);

		// veriricamos que realmente se sacó
		$this->assertNull($this->character->get_equipped_weapon());
	}

	public function testEquiparArmaYSacarLaQueTieneEquipada()
	{
		// buscamos arma y equipamos
		$weapon = $this->character->items()->find(8);
		$this->character->equip_item($weapon);

		// buscamos otra arma y equipamos
		$anotherWeapon = $this->character->items()->find(2);
		$this->character->equip_item($anotherWeapon);

		// comprobamos que la anterior está en inventario
		$this->assertEquals('inventory', CharacterItem::find($weapon->id)->location);

		// comprobamos que el otro arma está equipada
		$this->assertEquals($anotherWeapon->id, $this->character->get_equipped_weapon()->id);
	}

}
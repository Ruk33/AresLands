<?php

class CharacterModel extends PHPUnit_Framework_TestCase {

	public $character;

	public function __construct()
	{
		$this->character = Character::find(1);
	}

	public function testEquiparArma()
	{
		// comprobamos que no tenga arma
		$this->assertEquals(null, $this->character->get_equipped_weapon());

		// arma a equipar
		$weapon = $this->character->items()->find(8);
		// equipamos
		$this->character->equip_weapon($weapon);

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
		$this->character->equip_weapon($weapon);

		// buscamos otra arma y equipamos
		$anotherWeapon = $this->character->items()->find(2);
		$this->character->equip_weapon($anotherWeapon);

		// comprobamos que la anterior está en inventario
		$this->assertEquals('inventory', CharacterItem::find($weapon->id)->location);

		// comprobamos que el otro arma está equipada
		$this->assertEquals($anotherWeapon->id, $this->character->get_equipped_weapon()->id);
	}

}
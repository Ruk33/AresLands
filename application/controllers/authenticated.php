<?php

class Authenticated_Controller extends Base_Controller
{
	public $layout = 'layouts.default';
	public $restful = true;

	public function __construct()
	{
		parent::__construct();

		/*
		 *	Solo queremos logueados
		 */
		$this->filter('before', 'auth');

		/*
		 *	Si no tiene personaje lo
		 *	redireccionamos a la página
		 *	para que se pueda crear uno
		 */
		$this->filter('before', 'hasNoCharacter');
	}

	/*
	public function get_setData()
	{
		$skill = new Skill();

		$skill->name = 'Might';
		$skill->level = 1;
		$skill->duration = -1;
		$skill->data = [
			'stat_strength' => 15,
		];

		$skill->save();
	}
	*/

	public function get_index()
	{
		$character = Session::get('character');

		/*
		 *	Obtenemos los objetos del personaje
		 */
		$items = $character->items;
		$itemsToView = [];

		/*
		 *	Los ordenamos solo para que sea
		 *	más cómodo de trabajar en la vista
		 */
		foreach ( $items as $item )
		{
			$itemsToView[$item->location][] = $item;
		}

		/*
		 *	Obtenemos todos los skills (buffs)
		 *	del personaje
		 */
		$skills = $character->skills()->get();

		/*
		 *	
		 */
		$positiveBonifications = $character->get_bonifications(true);
		$negativeBonifications = $character->get_bonifications(false);

		$this->layout->title = 'Inicio';
		$this->layout->content = View::make('authenticated.index')
		->with('character', $character)
		->with('items', $itemsToView)
		->with('skills', $skills)
		->with('positiveBonifications', $positiveBonifications)
		->with('negativeBonifications', $negativeBonifications);
	}

	public function get_manipulateItem($id = 0, $count = 1)
	{
		/*
		 *	No queremos ejecutar acciones innecesariamente
		 */
		if ( $id > 0 && $count > 0 ) 
		{
			$characterItem = CharacterItem::where('id', '=', $id)->where('owner_id', '=', Session::get('character')->id)->first();

			/*
			 *	¿Existe el objeto?
			 */
			if ( $characterItem )
			{
				/*
				 *	Primero vamos a verificar
				 *	si lo tiene equipado, puesto
				 *	que si así es quiere decir
				 *	que quiere sacarse el objeto
				 */
				if ( $characterItem->location != 'inventory' )
				{
					$emptySlot = CharacterItem::get_empty_slot();

					/*
					 *	¿No hay espacio en el inventario?
					 */
					if ( ! $emptySlot )
					{
						/*
						 *	Redireccionamos notificándolo
						 */
						return Redirect::to('authenticated/index');
					}

					/*
					 *	Si hay espacio, entonces ponemos
					 *	el objeto en el inventario y guardamos
					 */
					$characterItem->location = 'inventory';
					$characterItem->slot = $emptySlot;

					$characterItem->save();

					/*
					 *	Disparamos el evento de desequipar objeto
					 */
					Event::fire('unequipItem', [$characterItem]);

					return Redirect::to('authenticated/index');
				}

				/*
				 *	¿Tiene la cantidad?
				 */
				if ( $characterItem->count >= $count )
				{
					$item = $characterItem->item;

					switch ( $item->body_part )
					{
						case 'lhand':
						case 'rhand':
							/*
							 *	Obtenemos el objeto que tiene equipado
							 *	que será reemplazado con $characterItem
							 */
							$equippedItem = CharacterItem::where('location', '=', $item->body_part)->first();

							/*
							 *	Evitamos acciones innecesarias
							 *	verificando que el personaje de hecho
							 *	tenga un objeto equipado
							 */
							if ( $equippedItem )
							{
								/*
								 *	Si tiene, movemos este objeto
								 *	equipado al inventario, al slot
								 *	de $characterItem
								 */
								$equippedItem->location = 'inventory';
								$equippedItem->slot = $characterItem->slot;

								$equippedItem->save();

								/*
								 *	Disparamos el evento de desequipar objeto
								 */
								Event::fire('unequipItem', [$equippedItem]);
							}

							/*
							 *	¡Le equipamos el objeto al personaje!
							 */
							$characterItem->location = $item->body_part;
							$characterItem->slot = 0;

							$characterItem->save();

							break;

						case 'lrhand':
							/*
							 *	En caso de que $characterItem sea de dos manos
							 *	entonces tenemos que buscar los objetos
							 *	que tiene equipados en ambas manos
							 */
							$equippedItems = CharacterItem::where('location', '=', 'lhand')->or_where('location', '=', 'rhand')->get();

							/*
							 *	Evitamos acciones innecesarias...
							 */
							if ( count($equippedItems) > 0 )
							{
								/*
								 *	Array en el que guardaremos los slots
								 *	que están vacíos
								 */
								$emptySlots = [];

								/*
								 *	¿Hay espacio en el inventario?
								 */
								$spaceInInventory = true;

								foreach ( $equippedItems as $equippedItem )
								{
									if ( $spaceInInventory )
									{
										/*
										 *	Buscamos un slot en el inventario para los objetos
										 */
										$spaceInInventory = CharacterItem::get_empty_slot();

										if ( $spaceInInventory )
										{
											$emptySlots[$equippedItem->id] = $spaceInInventory;
											$spaceInInventory = true;
										}
									}
								}

								/*
								 *	Verificamos si no hay espacio en el inventario
								 *	para guardar los objetos que vamos a sacar
								 */
								if ( ! $spaceInInventory )
								{
									/*
									 *	Si no hay, redirigimos notificándolo
									 */
									return Redirect::to('authenticated/index');
								}

								/*
								 *	Ahora que sabemos que tenemos
								 *	espacio en el inventario para los objetos
								 *	los situamos en el inventario
								 */
								foreach ( $equippedItems as $equippedItem )
								{
									$equippedItem->location = 'inventory';
									$equippedItem->slot = $emptySlots[$equippedItem->id];

									$equippedItem->save();

									/*
									 *	Disparamos el evento de desequipar objeto
									 */
									Event::fire('unequipItem', [$equippedItem]);
								}
							}

							/*
							 *	Finalmente, ¡equipamos el objeto!
							 */
							$characterItem->location = 'lrhand';
							$characterItem->slot = 0;

							$characterItem->save();

							break;

						case 'none':
							/*
							 *	Que sea none no significa que sea
							 *	poción, así que nos aseguramos
							 */
							if ( $item->type == 'potion' )
							{
								/*
								 *	Restamos la cantidad que vamos a usar
								 */
								$characterItem->count -= $count;

								/*
								 *	Si se quedó con cero o menos simplemente
								 *	borramos el registro
								 */
								if ( $characterItem->count <= 0 )
								{
									$characterItem->delete();
								}
								else
								{
									$characterItem->save();
								}
							}

							break;
					}
				}
			}
		}

		/*
		 *	Disparamos el evento de equipar objeto
		 */
		Event::fire('equipItem', [$characterItem]);

		return Redirect::to('authenticated/index');
	}
}
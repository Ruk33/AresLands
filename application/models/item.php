<?php

class Item extends Base_Model
{
	public static $softDelete = false;
	public static $timestamps = false;
	public static $table = 'items';
	public static $key = 'id';

	const WARRIOR = 1;
	const WIZARD  = 2;
	const MIXED   = 3;
    
	/**
	 * Obtenemos la ruta de la imagen del objeto
	 * @return string
	 */
	public function get_image_path()
	{
		return URL::base() . '/img/' . $this->image;
	}

	/**
	 * Generamos objeto aleatorio
	 * @param  int    $min_level Nivel minimo
	 * @param  int    $max_level Nivel maximo
	 * @param  int    $magical   Ver constantes WARRIOR, WIZARD y MIXED
	 * @param  bool   $adjetive  true para agregar un adjetivo
	 * @param  string $type      Valores posibles: random, blunt, sword, bow, dagger, staff, shield, potion y mercenary
	 * @return Item
	 */
	public static function generate_random($min_level, $max_level, $magical, $adjetive, $type)
	{
		$imgPath = "icons/items/";

		switch ( $type )
		{
			case "random":
				$types = array("blunt", "sword", "bow", "dagger", "staff", "shield", /*"potion", "mercenary"*/);
				return self::get_random_name($min_level, $max_level, $magical, $adjetive, $types[mt_rand(0, count($types) - 1)]);

			case "blunt":
				$names = array(
					"Garrote",
					"Lucero",
					"Mazo",
					"Cascanueces",
					"Guardian",
					"Cascanucas",
					"Rajanucas",
					"Abrenucas",
					"Sacaojos",
					"Despojador de almas",
					"Mazo de la forja de enanos",
					"Maza antigua",
					"Rompecorazones",
				);

				$images = array(
					"39.png",
					"40.png",
					"41.png",
					"43.png",
					"64.png",
					"65.png",
					"66.png",
				);
				
				$body_part = "rhand";
				$class = "weapon";

				break;

			case "sword":
				$names = array(
					"Tajador",
					"Lamento de viuda",
					"Guardajuramento",
					"Aguja",
					"Espada",
					"Cazamonstruos",
					"Cortanucas",
					"Espada condenadora",
					"Decapitadora",
					"Espada runica",
					"Decapitareyes",
					"Desgarradora",
				);
				
				$images = array(
					"7.png",
					"21.png",
					"48.png",
					"49.png",
					"50.png",
					"51.png",
					"53.png",
					"59.png",
					"60.png",
					"61.png",
					"62.png",
					"63.png",
					"153.png",
				);

				$body_part = "rhand";
				$class = "weapon";

				break;

			case "bow":
				$names = array(
					"Arco",
					"Arco golpeanucas",
					"Arco de batalla",
					"Soplo",
					"Arco largo",
					"Arco elfico",
					"Arco forestal",
					"Arco de guardia real",
					"Arco cazademonios",
					"Arco flexible",
					"Arco del canto",
					"Arco lunar",
					"Arco rastreador",
					"Arco ejecutamonstruos",
				);
				
				$images = array(
					"17.png",
					"18.png",
					"19.png",
					"20.png",
					"44.png",
					"45.png",
					"46.png",
				);

				$body_part = "rhand";
				$class = "weapon";
				
				break;

			case "dagger":
				$names = array(
					"Apuñalanucas",
					"Navaja",
					"Arrancatripas",
					"Desangradora",
					"Cortademonios",
					"Cortajuramentos",
					"Drenasangre",
					"Hoja de sacrificio",
					"Daga de reyes",
					"Cortademonios",
					"Venganza",
					"Daga",
					"Puñal",
					"Espina",
					"Filo",
					"Daga del ladron",
					"Desgarradora",
					"Hojanuca",
				);
				
				$images = array(
					"33.png",
					"34.png",
					"35.png",
					"36.png",
					"37.png",
					"38.png",
					"53.png",
					"58.png",
				);

				$body_part = "rhand";
				$class = "weapon";
				
				break;

			case "staff":
				$names = array(
					"Baston",
					"Palo",
					"Cetro",
					"Baston de guerra",
					"Gran baston",
					"Baston espiritual",
					"Baston lunar",
					"Cetro elfico",
					"Baston con escencia de Drow",
					"Hechiza enanos",
					"Perdicion oscura",
					"Cetro de mil estrellas",
					"Baston malevolo",
					"Baston bailarin",
				);
				
				$images = array(
					"3.png",
					"4.png",
					"5.png",
					"6.png",
					"8.png",
					"9.png",
					"10.png",
					"11.png",
					"29.png",
					"30.png",
					"31.png",
					"32.png",
					"61.png",
				);

				$body_part = "rhand";
				$class = "weapon";
				
				break;

			case "shield":
				$names = array(
					"Escudo",
					"Reflejo de la muerte",
					"Muro de desviacion",
					"Escudo real",
					"Muro repelemonstruos",
					"Barrera ancestral",
					"Escudo burlon",
					"Escudo grande",
					"Escudo pequeño",
					"Escudo guerrero",
					"Escudo antidemonios",
				);
				
				$images = array(
					"68.png",
					"69.png",
					"70.png",
					"71.png",
					"72.png",
					"73.png",
					"74.png",
					"75.png",
					"76.png",
					"77.png",
					"78.png",
					"79.png",
					"80.png",
					"82.png",
					"83.png",
					"84.png",
					"85.png",
					"87.png",
					"88.png",
					"89.png",
					"90.png",
					"92.png",
				);

				$body_part = "lhand";
				$class = "armor";
				
				break;

			case "potion":
				$names = array(
					""
				);

				$body_part = "none";
				$class = "consumible";
				
				break;

			case "mercenary":
				$names = array(
					""
				);

				$body_part = "mercenary";
				$class = "mercenary";
				
				break;

			default:
				throw new Exception("{$type} no se reconoce para generar un nombre aleatorio");
		}

		$name = $names[mt_rand(0, count($names) - 1)];

		if ( $type != "mercenary" && $adjetive )
		{
			$adjetives = array(
				"Deleitante", 
				"Pesado", 
				"Mortal", 
				"Divino",
				"Perturbador",
				"Abismal",
				"Destructor",
				"Infernal",
				"Oscuro",
				"Dentada",
				"del Maestro",
				"Sangrienta",
				"Ancestral",
				"Resistente",
				"Reflejante",
				"Cegador",
				"Inferior",
				"Superior",
				"Elite",
			);

			$name .= " " . $adjetives[mt_rand(0, count($adjetives) - 1)];
		}

		$stats = array(
			"stat_strength"         => ($max_level * mt_rand(1, 3)) + mt_rand($min_level * 2, $max_level * 4) * .2,
			"stat_dexterity"        => ($max_level * mt_rand(1, 3)) + mt_rand($min_level * 2, $max_level * 6) * .3,
			"stat_resistance"       => ($max_level * mt_rand(1, 3)) + mt_rand($min_level * 2, $max_level * 3) * .2,
			"stat_magic"            => ($max_level * mt_rand(1, 3)) + mt_rand($min_level * 3, $max_level * 7) * .4,
			"stat_magic_skill"      => ($max_level * mt_rand(1, 3)) + mt_rand($min_level * 4, $max_level * 6) * .3,
			"stat_magic_resistance" => ($max_level * mt_rand(1, 3)) + mt_rand($min_level * 1, $max_level * 2) * .1
		);

		switch ( $magical )
		{
			case self::WARRIOR:
				$stats['stat_magic'] /= mt_rand(2, 3);
				$stats['stat_magic_skill'] /= mt_rand(2, 3);
				$stats['stat_magic_resistance'] /= mt_rand(2, 3);
				break;

			case self::WIZARD:
				$stats['stat_strength'] /= mt_rand(2, 3);
				$stats['stat_dexterity'] /= mt_rand(2, 3);
				$stats['stat_resistance'] /= mt_rand(2, 3);
				break;

			case self::MIXED:
				$stats['stat_strength'] /= mt_rand(1, 3);
				$stats['stat_dexterity'] /= mt_rand(1, 3);
				$stats['stat_resistance'] /= mt_rand(1, 3);
				$stats['stat_magic'] *= 0.6;
				$stats['stat_magic_skill'] /= mt_rand(1, 4);
				$stats['stat_magic_resistance'] /= mt_rand(1, 2);
				break;
		}

		$item = new Item(array(
			"name"                  => $name,
			"image"                 => $imgPath . $images[mt_rand(0, count($images) - 1)],
			"level"                 => $min_level,
			"body_part"             => $body_part,
			"type"                  => $type,
			"class"                 => $class,
			"stat_strength"         => $stats["stat_strength"],
			"stat_dexterity"        => $stats["stat_dexterity"],
			"stat_resistance"       => $stats["stat_resistance"],
			"stat_magic"            => $stats["stat_magic"],
			"stat_magic_skill"      => $stats["stat_magic_skill"],
			"stat_magic_resistance" => $stats["stat_magic_resistance"],
		));

		return $item;
	}

	/**
	 * Query para obtener mercenario secundario
	 * @param  Character $character
	 * @return Eloquent
	 */
	public static function get_random_secondary_mercenary(Character $character)
	{
		return Item::where('class', '=', 'mercenary')
				   ->where('level', '>=', $character->level / 2)
				   ->order_by(DB::raw('RAND()'));
	}

	/**
	 *	Obtenemos las monedas dividas en
	 *	oro, plata y cobre de un personaje
	 *
	 *  @param <integer> $coins Cantidad de monedas
	 *	@return <Array> Monedas dividas en oro, plata y cobre
	 */
	public static function get_divided_coins($amount)
	{
		$coins = array();

		$coins['gold'] = substr($amount, 0, -4) ? substr($amount, 0, -4) : 0;
		$coins['silver'] = substr($amount, -4, -2) ? substr($amount, -4, -2) : 0;
		$coins['copper'] = substr($amount, -2) ? substr($amount, -2) : 0;
		$coins['text'] = "<ul class='inline coin-list'>
							<li><i class='coin coin-gold pull-left'></i> {$coins['gold']}</li>
							<li><i class='coin coin-silver pull-left'></i> {$coins['silver']}</li>
							<li><i class='coin coin-copper pull-left'></i> {$coins['copper']}</li>
						</ul>";

		return $coins;
	}
	
	/**
	 *	@return <array> Array de Skill (model)
	 */
	public function get_skills()
	{
		/*
		 *	Patrón para guardar habilidades:
		 *	skill_id-skill_level
		 *
		 *	Ej.: 2-1 (guardamos el skill con el id 2 y su nivel 1)
		 */
		if ( $this->skill == 0 )
		{
			return array();
		}

		$skillsPattern = explode(';', $this->skill);
		$skills = array();
		$skillId;
		$skillLevel;

		foreach ( $skillsPattern as $skillPattern )
		{
			list($skillId, $skillLevel) = explode('-', $skillPattern);

			if ( $skillId != 0 && $skillLevel > 0 )
			{
				$skills[] = Skill::where('level', '=', (int) $skillLevel)->where('id', '=', (int) $skillId)->first();
			}
		}

		return $skills;
	}

	public function get_text_for_tooltip()
	{
		$message = "<div class='pull-left text-center' style='width: 80px;'><img src='" . $this->get_image_path() . "' /></div>";

		$message .= "<div style='margin-left: 85px; text-align: left;'>";
		$message .= "<span class='pull-right' style='color: #AFAFAF; font-size: 10px; text-transform: uppercase;'>";
		switch ( $this->type )
		{
			case 'blunt':
				$message .= 'Mazo';
				break;
				
			case 'bigblunt':
				$message .= 'Mazo de dos manos';
				break;
				
			case 'sword':
				$message .= 'Espada';
				break;
			
			case 'bigsword':
				$message .= 'Espada de dos manos';
				break;
			
			case 'bow':
				$message .= 'Arco';
				break;
			
			case 'dagger':
				$message .= 'Daga';
				break;
			
			case 'staff':
				$message .= 'Váculo';
				break;
			
			case 'bigstaff':
				$message .= 'Váculo de dos manos';
				break;
			
			case 'shield':
				$message .= 'Escudo';
				break;
			
			case 'potion':
				$message .= 'Poción';
				break;
			
			case 'arrow':
				$message .= 'Flecha';
				break;
			
			case 'etc':
				$message .= 'Misceláneo';
				break;
			
			case 'mercenary':
				$message .= 'Mercenario';
				break;
			
			case 'none':
				break;
			
			default:
				$message .= 'Desconocido';
				break;
		}
		$message .= '</span>';

		$message .= "<div style='width: 300px;'>";
		$message .= "<strong style='color: white;'>$this->name</strong>";
		$message .= "<p style='color: #FFC200;'>Requiere nivel $this->level</p>";
		$message .= "<p><small><em>$this->description</em></small></p>";
		$message .= '</div>';

		$message .= "<ul class='unstyled'>";

		switch ( $this->body_part )
		{
			case 'chest':
				$message .= '<li>Pecho</li>';
				break;

			case 'legs':
				$message .= '<li>Piernas</li>';
				break;

			case 'feet':
				$message .= '<li>Pies</li>';
				break;

			case 'head':
				$message .= '<li>Cabeza</li>';
				break;

			case 'hands':
				$message .= '<li>Manos</li>';
				break;

			case 'lhand':
				$message .= '<li>Mano izquierda</li>';
				break;

			case 'rhand':
				$message .= '<li>Mano derecha</li>';
				break;

			case 'lrhand':
				$message .= '<li>Mano izquierda y derecha</li>';
				break;

			case 'mercenary':
				$message .= '<li>Mercenario</li>';
				break;
		}

		if ( $this->stat_strength != 0 )
		{
			$message .= "<li>Fuerza física: $this->stat_strength</li>";
		}

		if ( $this->stat_dexterity != 0 )
		{
			$message .= "<li>Destreza física: $this->stat_dexterity</li>";
		}
		
		if ( $this->stat_resistance != 0 )
		{
			$message .= "<li>Resistencia: $this->stat_resistance</li>";
		}

		if ( $this->stat_magic != 0 )
		{
			$message .= "<li>Poder mágico: $this->stat_magic</li>";
		}

		if ( $this->stat_magic_skill != 0 )
		{
			$message .= "<li>Habilidad mágica: $this->stat_magic_skill</li>";
		}

		if ( $this->stat_magic_resistance != 0 )
		{
			$message .= "<li>Contraconjuro: $this->stat_magic_resistance</li>";
		}

		$message .= '</ul>';

		$message .= '</div>';

		return $message;
	}
}
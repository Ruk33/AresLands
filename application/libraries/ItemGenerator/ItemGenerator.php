<?php namespace Libraries\ItemGenerator;

use Item;
use Skill;

abstract class ItemGenerator
{
    const TARGET_WARRIOR = 1;
    const TARGET_WIZARD = 2;
    const TARGET_MIXED = 3;

    /**
     * @param boolean $adjetive
     * @param integer $quality
     * @return Array<ItemData>
     */
    protected abstract function getItemDataArray($adjetive, $quality);

    /**
     * @param boolean $adjetive
     * @param integer $quality
     * @return ItemData
     */
    protected final function getRandomItemData($adjetive, $quality)
    {
        $itemDataArray = $this->getItemDataArray($adjetive, $quality);
        return $itemDataArray[mt_rand(0, count($itemDataArray)-1)];
    }

    /**
     * @param integer $min
     * @param integer $max
     * @return integer
     */
    protected function getLevel($min, $max)
    {
        return $min;
    }

    /**
     * @return string
     */
    protected abstract function getType();

    /**
     * @return string
     */
    protected abstract function getClass();

    /**
     * @return string
     */
    protected abstract function getBodyPart();

    /**
     * @return integer
     */
    protected function getQuality()
    {
        return Item::QUALITY_COMMON;
    }

    /**
     * @return Array<Skill>
     */
    protected function getSkillsArray()
    {
        return array(
            new Skill(array("id" => 0, "level" => 0))
        );
    }

    /**
     * @return Skill
     */
    protected function getSkill()
    {
        $skills = $this->getSkillsArray();
        return $skills[mt_rand(0, count($skills)-1)];
    }

    /**
     * @return string
     */
    protected function getAttackSpeed()
    {
        return 'normal';
    }

    /**
     * @param Item $item Instancia del objeto que esta siendo generado con 
     *                   algunos datos ya establecidos (level, type, class,
     *                   body_part, attack_speed, skill, quality)
     * @param integer $target
     * @return array
     */
    protected function getStats(Item $item, $target)
    {
        return array(
            "stat_strength" => 0,
            "stat_dexterity" => 0,
            "stat_resistance" => 0,
            "stat_magic" => 0,
            "stat_magic_skill" => 0,
            "stat_magic_resistance" => 0,
        );
    }

    /**
     *
     * @param integer $min
     * @param integer $max
     * @param boolean $adjetive
     * @param integer $target
     * @param null|integer $quality
     * @return Item
     */
    public final function generate($min, $max, $adjetive = false, $target = self::TARGET_MIXED, $quality = null)
    {
        $item = new Item;
        $skill = $this->getSkill();

        $item->level = $this->getLevel($min, $max);
        $item->type = $this->getType();
        $item->class = $this->getClass();
        $item->body_part = $this->getBodyPart();
        $item->attack_speed = $this->getAttackSpeed();
        $item->skill = "{$skill->id}-{$skill->level}";
        $item->quaility = (! $quality) ? $this->getQuality() : $quality;

        $itemData = $this->getRandomItemData($adjetive, $item->quaility);

        $item->name = $itemData->getName();
        $item->image = $itemData->getImage();

        foreach ($this->getStats($item, $target) as $stat => $value) {
            $item->set_attribute($stat, $value);
        }

        return $item;
    }

    /*
    public function generate($min_level, $max_level, $magical, $adjetive, $type)
    {
        $imgPath = "icons/items/";

        switch ( $type )
        {
            case "random":
                $types = array("blunt", "sword", "bow", "dagger", "staff", "shield", "potion", "mercenary");
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
            $adjetives = array();

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
    }*/
} 
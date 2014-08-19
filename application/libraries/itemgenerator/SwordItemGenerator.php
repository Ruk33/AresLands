<?php

class SwordItemGenerator extends WeaponItemGenerator
{
    /**
     * @param boolean $adjetive
     * @param integer $quality
     * @return Array<ItemData>
     */
    protected function getItemDataArray($adjetive, $quality)
    {
        return array(
            new ItemData("Tajador", $adjetive, "icons/items/7.png"),
            new ItemData("Lamento de viuda", $adjetive, "icons/items/21.png"),
            new ItemData("Guardajuramento", $adjetive, "icons/items/48.png"),
            new ItemData("Aguja", $adjetive, "icons/items/49.png"),
            new ItemData("Espada", $adjetive, "icons/items/50.png"),
            new ItemData("Cazamonstruos", $adjetive, "icons/items/51.png"),
            new ItemData("Cortanucas", $adjetive, "icons/items/53.png"),
            new ItemData("Espada condenadora", $adjetive, "icons/items/59.png"),
            new ItemData("Decapitadora", $adjetive, "icons/items/60.png"),
            new ItemData("Espada runica", $adjetive, "icons/items/61.png"),
            new ItemData("Decapitareyes", $adjetive, "icons/items/62.png"),
            new ItemData("Desgarradora", $adjetive, "icons/items/63.png"),
        );
    }

    /**
     * @return string
     */
    protected function getType()
    {
        return 'sword';
    }
} 
<?php namespace Libraries\ItemGenerator;

class ItemData
{
    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $image;

    /**
     * @return array
     */
    public function getAdjetiveArray()
    {
        return array(
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
    }

    /**
     * @return string
     */
    protected final function getRandomAdjetive()
    {
        $adjetives = $this->getAdjetiveArray();
        return $adjetives[mt_rand(0, count($adjetives)-1)];
    }

    /**
     * @param string $name
     * @param boolean useAdjetive
     * @param string $path Ruta a la imagen (ejemplo: "icons/items/1.png")
     */
    public function __construct($name, $useAdjetive, $path)
    {
        $adjetive = ($useAdjetive) ? $this->getRandomAdjetive() : "";

        $this->name = trim($name . " " . $adjetive);
        $this->image = $path;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getImage()
    {
        return $this->image;
    }
} 
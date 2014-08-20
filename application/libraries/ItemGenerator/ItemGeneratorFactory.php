<?php namespace Libraries\ItemGenerator;

class ItemGeneratorFactory
{
    /**
     * @param string $type
     * @return ItemGenerator
     */
    public function get($type)
    {
        $factory = null;

        switch ($type) {
            case 'sword':
                $factory = new SwordItemGenerator;
                break;

            default:
                $factory = new RandomItemGenerator;
        }

        return $factory;
    }
} 
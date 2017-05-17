<?php

namespace AdminBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Base entity
 *
 * @ORM\MappedSuperclass
 */
abstract class BaseEntity implements BaseEntityInterface
{
    abstract public function __toString();

    public static function __getName()
    {
        $array = explode('\\', static::class);
        return end($array);
    }

    public static function __getNameUnderscore()
    {
        return strtolower(preg_replace('/([^A-Z])([A-Z])/', '$1_$2', static::__getName()));
    }

    public static function getShowColumns()
    {
        return self::getIndexColumns();
    }
}

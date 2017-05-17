<?php

namespace AdminBundle\Entity;

/**
 * Base entity interface
 *
 * @ORM\MappedSuperclass
 */
interface BaseEntityInterface
{
    public static function getIndexColumns();
    public static function getShowColumns();
}

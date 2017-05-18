<?php

namespace AdminBundle\Entity;

/**
 * Base entity interface
 *
 */
interface BaseEntityInterface
{
    public static function getIndexColumns();
    public static function getShowColumns();
}

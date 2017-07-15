<?php

namespace app\graphql\helpers;

trait SingletonTrait
{
    protected static $instance;

    public static function instance()
    {
        return static::$instance ?: (static::$instance = new static);
    }
}
<?php

namespace app\graphql;

use GraphQL\Type\Definition\Type;
use GraphQL\Type\Definition\ListOfType;
use GraphQL\Type\Definition\NonNull;

class Types
{
    public static function boolean()
    {
        return Type::boolean();
    }

    public static function float()
    {
        return Type::float();
    }

    public static function id()
    {
        return Type::id();
    }

    public static function int()
    {
        return Type::int();
    }

    public static function string()
    {
        return Type::string();
    }

    public static function listOf($type)
    {
        return new ListOfType($type);
    }

    public static function nonNull($type)
    {
        return new NonNull($type);
    }
}
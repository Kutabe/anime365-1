<?php

namespace app\graphql\translation;

use app\graphql\helpers\ObjectType;
use app\graphql\Types;

class TranslationType extends ObjectType
{
    public function __construct()
    {
        $config = [
            'fields' => [
                'id' => Types::id(),
            ]
        ];
        parent::__construct($config);
    }
}

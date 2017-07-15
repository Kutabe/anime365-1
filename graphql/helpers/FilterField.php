<?php

namespace app\graphql\helpers;

use GraphQL\Type\Definition\InputObjectField;

class FilterField extends InputObjectField
{
    /**
     * @var string
     */
    public $column;
    /**
     * @var callable
     */
    public $process;
    /**
     * @var array
     */
    public $operators;
    /**
     * @var string
     */
    public $operator;
}
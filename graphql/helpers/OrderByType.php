<?php

namespace app\graphql\helpers;

use GraphQL\Error\Error;
use GraphQL\Type\Definition\EnumType;
use yii\db\ActiveQuery;

class OrderByType extends EnumType
{
    use SingletonTrait;

    /**
     * @param ActiveQuery $query
     * @param string $value
     * @throws Error
     */
    public function processQuery(ActiveQuery $query, string $value)
    {
        if ($value !== null) {
            if (substr($value, -4) == '_ASC') {
                $column = substr($value, 0, -4);
                $query->addOrderBy([$column => SORT_ASC]);
            } elseif (substr($value, -5) == '_DESC') {
                $column = substr($value, 0, -5);
                $query->addOrderBy([$column => SORT_DESC]);
            } else {
                $query->addOrderBy($value);
            }
        }
    }
}
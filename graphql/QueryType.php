<?php

namespace app\graphql;

use app\graphql\helpers\ActiveRecordHelpers;
use app\graphql\helpers\ObjectType;
use app\graphql\series\SeriesFilterType;
use app\graphql\series\SeriesOrderByType;
use app\graphql\series\SeriesType;
use app\models\Series;

use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;

class QueryType extends ObjectType
{
    public function __construct()
    {
        $config = [
            'name' => 'Query',
            'fields' => [
                'series' => [
                    'type' => Types::listOf(SeriesType::instance()),
                    'args' => [
                        'filter' => SeriesFilterType::instance(),
                        'orderBy' => SeriesOrderByType::instance(),
                    ],
                    'resolve' => ActiveRecordHelpers::listResolver(function ($source, $args, AppContext $context, ResolveInfo $info) {
                        $query = Series::find();
                        $query->andWhere(['isHentaiValue' => $context->isHentaiSite]);
                        SeriesFilterType::instance()->processQuery($query, $args['filter']);
                        SeriesOrderByType::instance()->processQuery($query, $args['orderBy']);
                        return $query->andWhere('"isActive" = 1')->limit(3);
                    })
                ]
            ],
            'resolveField' => function ($val, $args, $context, ResolveInfo $info) {
                return $this->{$info->fieldName}($val, $args, $context, $info);
            }
        ];
        parent::__construct($config);
    }
}
<?php

namespace app\graphql\series;

use app\graphql\helpers\OrderByType;

class SeriesOrderByType extends OrderByType
{
    public function __construct()
    {
        $config = [
            'values' => [
                'id_ASC',
                'id_DESC',
                'myAnimeListScore_ASC',
                'myAnimeListScore_DESC',
                'worldArtScore_ASC',
                'worldArtScore_DESC',
                'worldArtTopPlace_ASC',
                'worldArtTopPlace_DESC',
                // Example with sql:
                // Note: you should use an array instead you'll get an error like "Names must match..."
                //'test_ASC' => [
                //    'value' => 'id ASC'
                //]
            ],
        ];

        parent::__construct($config);
    }
}
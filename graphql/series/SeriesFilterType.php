<?php

namespace app\graphql\series;

use app\graphql\helpers\FilterType;
use app\graphql\Types;
use Yii;
use yii\db\ActiveQuery;

class SeriesFilterType extends FilterType
{
    public function __construct()
    {
        $config = [
            'fields' => [
                'isOngoing' => [
                    'column' => 'isAiring',
                    'type' => Types::boolean()
                ],
                'title_search' => [
                    'type' => Types::string(),
                    'process' => function (ActiveQuery $query, $value) {
                        // TODO: Use search engine for title search
                        $value = mb_strtolower($value);
                        $value = str_replace('сезон', '', $value);
                        $value = preg_replace('/\s+/', ' ', $value);
                        $value = trim($value);

                        $cacheId = 'seriesTitleFilter_' . $value;
                        $ids = Yii::$app->cache->get($cacheId);
                        if ($ids === false) {
                            if (strpos($value, 'ё') !== false) {
                                $value2 = str_replace('ё', 'е', $value);
                            } elseif (strpos($value, 'е') !== false) {
                                $value2 = str_replace('е', 'ё', $value);
                            } else {
                                $value2 = $value;
                            }

                            $params = [':value' => '%' . $value . '%', ':value2' => '%' . $value2 . '%'];
                            $ids = Yii::$app->db->createCommand('SELECT "seriesId" FROM "series_titles" WHERE "title" ILIKE :value OR "title" ILIKE :value2 GROUP BY "seriesId" LIMIT 200;', $params)->queryColumn();
                            Yii::$app->cache->set($cacheId, $ids, 3600);
                        }

                        $query->andWhere(['id' => $ids]);
                    }
                ],
                'titleOriginal' => Types::string(),
                'year' => Types::int(),
                //'genres' => Types::listOf(Types::int()),
            ]
        ];
        parent::__construct($config);
    }
}

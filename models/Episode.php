<?php

namespace app\models;

use Yii;

/**
 * Episodes connected to Series via seriesId.
 * Each series can have many episodes.
 *
 * @property integer $id
 * @property integer $seriesId
 * @property string $episodeType 'tv', 'movie', 'ova', etc, see more in self::$episodeTypes
 * @property double $episodeInt not Int anymore, not it's double TODO: rename it
 * @property string $episodeTitle not used yet
 * @property integer $isFirstUploaded 1 = if has at least one active translation
 * @property string $firstUploadedDateTime DateTime when at least one translation became active
 * @property integer $isActive 1 = if has at least one translation (maybe inactive)
 * @property integer $countViews Count unique views of this episode
 *
 * @property integer $notifyId deprecated
 * @property integer $isReserved deprecated
 * @property string $reservedDateTime deprecated
 * @property integer $firstUploadedTranslationId deprecated?
 */
class Episode extends \yii\db\ActiveRecord
{
    // Variants for $this->episodeType
    public static $episodeTypes = [
        'tv' => 'TV',
        'ova' => 'OVA',
        'ona' => 'ONA',
        'movie' => 'Movie',
        'preview' => 'Preview',
        'special' => 'Special',
        'opening' => 'Opening',
        'ending' => 'Ending',
        'menu' => 'Menu',
        'bonus' => 'Bonus',
        'other' => 'Other',
    ];

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'episodes';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [];
    }

    public function getTranslations()
    {
        return $this->hasMany(Translation::className(), ['episodeId' => 'id'])->andWhere(['>=', 'isActive', 1])->orderBy(['priority' => SORT_DESC, 'id' => SORT_DESC])->inverseOf('episode');
    }
}

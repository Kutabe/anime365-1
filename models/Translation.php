<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "translations".
 *
 * @property integer $id
 * @property integer $episodeId
 * @property integer $seriesId
 * @property string $type
 * @property integer $videoId
 * @property integer $fansubsTranslationId
 * @property string $subFile
 * @property integer $audioId
 * @property string $originalFilename
 * @property string $authors
 * @property string $authorsSummary
 * @property integer $notifyId
 * @property string $addedDateTime
 * @property string $activeDateTime
 * @property integer $isActive
 * @property string $subFileLink
 * @property string $qualityType
 * @property string $source
 * @property string $subFileMd5
 * @property integer $torrentId
 * @property string $torrentFilePath
 * @property integer $externalVideoId
 * @property string $updatedDateTime
 * @property string $flags
 * @property integer $streamIndex
 * @property integer $originalVideoId
 * @property integer $priority
 * @property integer $countChecksEpisode
 * @property integer $countChecksVideo
 * @property integer $redirectToId
 * @property integer $countViews
 * @property integer $translationFromId
 * @property integer $timingFromId
 * @property integer $addedByUserId
 * @property integer $addedByAuthor
 */
class Translation extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'translations';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'episodeId', 'seriesId', 'type', 'videoId', 'fansubsTranslationId', 'subFile', 'audioId', 'originalFilename', 'authors', 'authorsSummary', 'notifyId', 'addedDateTime', 'activeDateTime', 'isActive', 'qualityType', 'source', 'subFileMd5', 'torrentId', 'torrentFilePath', 'externalVideoId', 'updatedDateTime', 'flags', 'streamIndex', 'originalVideoId', 'priority', 'countChecksEpisode', 'countChecksVideo', 'redirectToId', 'countViews', 'translationFromId', 'timingFromId', 'addedByUserId', 'addedByAuthor'], 'required'],
            [['id', 'episodeId', 'seriesId', 'videoId', 'fansubsTranslationId', 'audioId', 'notifyId', 'isActive', 'torrentId', 'externalVideoId', 'streamIndex', 'originalVideoId', 'priority', 'countChecksEpisode', 'countChecksVideo', 'redirectToId', 'countViews', 'translationFromId', 'timingFromId', 'addedByUserId', 'addedByAuthor'], 'integer'],
            [['authors', 'subFileLink', 'flags'], 'string'],
            [['addedDateTime', 'activeDateTime', 'updatedDateTime'], 'safe'],
            [['type', 'authorsSummary', 'qualityType', 'subFileMd5'], 'string', 'max' => 255],
            [['subFile', 'originalFilename', 'source', 'torrentFilePath'], 'string', 'max' => 4000],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'episodeId' => 'Episode ID',
            'seriesId' => 'Series ID',
            'type' => 'Type',
            'videoId' => 'Video ID',
            'fansubsTranslationId' => 'Fansubs Translation ID',
            'subFile' => 'Sub File',
            'audioId' => 'Audio ID',
            'originalFilename' => 'Original Filename',
            'authors' => 'Authors',
            'authorsSummary' => 'Authors Summary',
            'notifyId' => 'Notify ID',
            'addedDateTime' => 'Added Date Time',
            'activeDateTime' => 'Active Date Time',
            'isActive' => 'Is Active',
            'subFileLink' => 'Sub File Link',
            'qualityType' => 'Quality Type',
            'source' => 'Source',
            'subFileMd5' => 'Sub File Md5',
            'torrentId' => 'Torrent ID',
            'torrentFilePath' => 'Torrent File Path',
            'externalVideoId' => 'External Video ID',
            'updatedDateTime' => 'Updated Date Time',
            'flags' => 'Flags',
            'streamIndex' => 'Stream Index',
            'originalVideoId' => 'Original Video ID',
            'priority' => 'Priority',
            'countChecksEpisode' => 'Count Checks Episode',
            'countChecksVideo' => 'Count Checks Video',
            'redirectToId' => 'Redirect To ID',
            'countViews' => 'Count Views',
            'translationFromId' => 'Translation From ID',
            'timingFromId' => 'Timing From ID',
            'addedByUserId' => 'Added By User ID',
            'addedByAuthor' => 'Added By Author',
        ];
    }

    public function getEpisode() {
        return $this->hasOne(Episode::className(), ['id' => 'episodeId'])->inverseOf('translations');
    }
}

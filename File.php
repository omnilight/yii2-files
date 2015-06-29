<?php

namespace omnilight\files;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\db\Expression;
use yii\helpers\FileHelper;
use yii\web\UploadedFile;


/**
 * @property string $id
 * @property string $original_name
 * @property string $name
 * @property int $file_size
 * @property string $created_at
 * @property string $updated_at
 *
 * Class File is the base class for all file uploads
 */
class File extends ActiveRecord
{
    const SCENARIO_FILE_UPLOAD = 'fileUpload';

    /**
     * @var UploadedFile
     */
    public $fileUpload;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%files}}';
    }

    public function behaviors()
    {
        return [
            'timestamp' => [
                'class' => TimestampBehavior::className(),
                'value' => new Expression('NOW()'),
            ]
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            'fileRule' => [['fileUpload'], 'file', 'skipOnEmpty' => false, 'on' => self::SCENARIO_FILE_UPLOAD, 'when' => function () {
                return $this->isNewRecord;
            }],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('omnilight/files', 'ID'),
            'original_name' => Yii::t('omnilight/files', 'Original Name'),
            'name' => Yii::t('omnilight/files', 'Name'),
            'file_size' => Yii::t('omnilight/files', 'File Size'),
            'created_at' => Yii::t('omnilight/files', 'Created At'),
            'updated_at' => Yii::t('omnilight/files', 'Updated At'),
            'fileUpload' => Yii::t('omnilight/files', 'File'),
        ];
    }

    public function loadWithFile($data, $formName = null)
    {
        if ($this->load($data, $formName) == false) {
            return false;
        }

        if (!($this->fileUpload instanceof UploadedFile)) {
            $this->fileUpload = UploadedFile::getInstance($this, 'fileUpload');
        }
        return true;
    }

    public function afterSave($insert, $changedAttributes)
    {
        if ($this->fileUpload instanceof UploadedFile) {
            $this->uploadFile($this->fileUpload);
        }
        parent::afterSave($insert, $changedAttributes);
    }

    public function uploadFile(UploadedFile $fileUpload)
    {
        if ($this->name) {
            @unlink($this->getFileName());
        }

        $this->original_name = $fileUpload->name;
        $this->file_size = filesize($fileUpload->tempName);
        $this->name = $this->generateName();

        $this->updateAttributes(['original_name', 'name', 'files_size']);

        $fileName = $this->getFileName();
        FileHelper::createDirectory(dirname($fileName));
        $this->fileUpload->saveAs($fileName);
    }

    /**
     * @return bool|string
     */
    public function getFileName()
    {
        return Yii::getAlias('@webroot/uploads/' . $this->name);
    }

    /**
     * @return string
     */
    protected function generateName()
    {
        return $this->id . '.' . $this->getOriginalExtension();
    }

    /**
     * @return string
     */
    public function getOriginalExtension()
    {
        return strtolower(pathinfo($this->original_name, PATHINFO_EXTENSION));
    }

    public function afterDelete()
    {
        @unlink($this->getFileName());
        parent::afterDelete();
    }

    /**
     * @return bool|string
     */
    public function getUrl()
    {
        return Yii::getAlias('@web/uploads/' . $this->name);
    }

    /**
     * @return string
     */
    public function getFileExtension()
    {
        return strtolower(pathinfo($this->name, PATHINFO_EXTENSION));
    }
}
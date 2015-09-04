<?php

namespace omnilight\files;

use Yii;
use yii\base\Model;
use yii\db\ActiveRecord;
use yii\helpers\FileHelper;
use yii\web\UploadedFile;


/**
 * Trait FileTrait
 *
 * @property string $file_original_name
 * @property string $file_name
 * @property int $file_size
 */
trait FileTrait
{
    /**
     * @var UploadedFile
     */
    public $fileUpload;

    public function loadWithFile($data, $formName = null)
    {
        /** @var Model | self $this */
        if ($this->load($data, $formName) == false) {
            return false;
        }

        if (!($this->fileUpload instanceof UploadedFile)) {
            $this->fileUpload = UploadedFile::getInstance($this, 'fileUpload');
        }
        return true;
    }

    public function saveWithFile($runValidation = true, $attributeNames = null)
    {
        /** @var ActiveRecord | self $this */
        $transaction = $this->getDb()->beginTransaction();
        if ($this->save($runValidation, $attributeNames) == false) {
            $transaction->rollBack();
            return false;
        }

        if ($this->fileUpload instanceof UploadedFile) {
            $this->uploadFile($this->fileUpload);
        }
        $transaction->commit();
        return true;
    }

    public function uploadFile(UploadedFile $fileUpload)
    {
        /** @var ActiveRecord | self $this */
        if ($this->name) {
            @unlink($this->getFileName());
        }

        $this->file_original_name = $fileUpload->name;
        $this->file_size = filesize($fileUpload->tempName);
        $this->file_name = $this->generateFileName();

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
        return Yii::getAlias('@webroot/uploads/' . $this->file_name);
    }

    /**
     * Method should generate file name
     * @return string
     */
    abstract protected function generateFileName();

    public function deleteWithFile()
    {
        /** @var ActiveRecord | self $this */
        if ($this->delete()) {
            $this->deleteFile();
            return true;
        }
        return false;
    }

    public function deleteFile()
    {
        @unlink($this->getFileName());
    }

    /**
     * @return string
     */
    public function getFileOriginalExtension()
    {
        return strtolower(pathinfo($this->file_original_name, PATHINFO_EXTENSION));
    }

    /**
     * @return bool|string
     */
    public function getFileUrl()
    {
        return Yii::getAlias('@web/uploads/' . $this->file_name);
    }

    /**
     * @return string
     */
    public function getFileExtension()
    {
        return strtolower(pathinfo($this->file_name, PATHINFO_EXTENSION));
    }
}
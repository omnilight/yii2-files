<?php

namespace omnilight\files;

use yii\base\Application;
use yii\base\BootstrapInterface;


/**
 * Class Bootstrap
 * @package \omnilight\phonenumbers
 */
class Bootstrap implements BootstrapInterface
{

    /**
     * Bootstrap method to be called during application bootstrap stage.
     * @param Application $app the application currently running
     */
    public function bootstrap($app)
    {
        $app->i18n->translations['omnilight/files'] = [
            'class' => 'yii\i18n\PhpMessageSource',
            'basePath' => '@omnilight/files/messages',
            'sourceLanguage' => 'en-US',
        ];
    }
}
<?php

namespace app\modules\vault;

use app\modules\vault\components\Client;
use yii\base\InvalidConfigException;

/**
 * Vault module.
 *
 * @property Client $client Vault client
 */
class Module extends \yii\base\Module
{
    public $controllerNamespace = 'app\modules\vault\commands';
    public $clientConfig = '@app/modules/vault/config/client.php';

    public function init()
    {
        parent::init();

        $this->setComponents([
            'client' => $this->loadClientConfig(),
        ]);
    }

    /**
     * @return Client
     */
    public static function getClient()
    {
        return static::getInstance()->client;
    }

    /**
     * Load vault client configuration.
     *
     * @return mixed|string
     * @throws InvalidConfigException
     */
    private function loadClientConfig()
    {
        $config = $this->clientConfig;

        if (is_string($config)) {
            $filename = \Yii::getAlias($config);

            if (!file_exists($filename)) {
                throw new InvalidConfigException(sprintf('Vault client config file %s not found.', $filename));
            }

            /** @noinspection PhpIncludeInspection */
            $config = require($filename);
        }

        if (!isset($config['class'])) {
            $config['class'] = 'app\modules\vault\components\Client';
        }

        return $config;
    }
}
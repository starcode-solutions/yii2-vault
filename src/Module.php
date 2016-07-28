<?php

namespace starcode\yii\vault;

use starcode\yii\vault\components\Client;
use yii\base\InvalidConfigException;

/**
 * Vault module.
 *
 * @property Client $client Vault client
 */
class Module extends \yii\base\Module
{
    const DEFAULT_CLIENT_CLASS = 'starcode\yii\vault\components\Client';

    public $controllerNamespace = 'starcode\yii\vault\commands';
    public $clientConfig = __DIR__ . '/config/client.php';

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
            $config['class'] = self::DEFAULT_CLIENT_CLASS;
        }

        return $config;
    }
}
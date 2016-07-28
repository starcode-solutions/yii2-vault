<?php

namespace app\modules\vault\commands;

use app\modules\vault\components\Client;
use app\modules\vault\exceptions\SecretPathNotFound;
use app\modules\vault\Module;
use yii\console\Controller;

class ClientController extends Controller
{
    public function actionList($path)
    {
        try {
            $result = $this->getClient()->listSecrets($path);

            print_r($result);
        } catch (SecretPathNotFound $e) {
            $this->stderr($e->getMessage() . "\n");
        }
    }

    public function actionRead($path)
    {
        try {
            $result = $this->getClient()->read($path);

            print_r($result);
        } catch (SecretPathNotFound $e) {
            $this->stderr($e->getMessage() . "\n");
        }
    }

    public function actionWrite($path, $value)
    {
        $this->getClient()->write($path, $value);
    }

    public function actionDelete($path)
    {
        try {
            $this->getClient()->delete($path);
        } catch (SecretPathNotFound $e) {
            $this->stderr($e->getMessage() . "\n");
        }
    }

    /**
     * @return null|Client
     * @throws \yii\base\InvalidConfigException
     */
    private function getClient()
    {
        return Module::getClient();
    }
}
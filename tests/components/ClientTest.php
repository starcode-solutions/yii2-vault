<?php

namespace starcode\yii\vault\tests\components;

use starcode\yii\vault\components\Client;
use starcode\yii\vault\tests\TestCase;
use yii\base\InvalidConfigException;

class ClientTest extends TestCase
{
    public function testInitPassEmptyToken()
    {
        $this->expectException(InvalidConfigException::class);

        new Client([
            'token' => null,
        ]);
    }
}
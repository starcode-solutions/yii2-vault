<?php

namespace starcode\yii\vault\tests;

use starcode\yii\vault\components\Client;
use starcode\yii\vault\components\ClientTest;
use starcode\yii\vault\Module;
use yii\base\InvalidConfigException;

class ModuleTest extends TestCase
{
    public function testLoadModuleConfig()
    {
        $module = new Module('vault', null, [
            'clientConfig' => __DIR__ . '/config/client.php',
        ]);

        $this->assertTrue($module->has('client'));
        $this->assertInstanceOf(ClientTest::class, $module->get('client'));
    }

    public function testLoadClientConfigThrowException()
    {
        $this->expectException(InvalidConfigException::class);

        new Module('vault', null, [
            'clientConfig' => '/not/found/path',
        ]);
    }

    public function testLoadClientConfigPassArray()
    {
        $module = new Module('vault', null, [
            'clientConfig' => [
                'class' => ClientTest::class,
            ],
        ]);

        $this->assertTrue($module->has('client'));
        $this->assertInstanceOf(ClientTest::class, $module->get('client'));
    }

    public function testLoadClientConfigDefaultClientClassSet()
    {
        $module = new Module('vault', null, [
            'clientConfig' => [
                'token' => 'abc',
            ],
        ]);

        $this->assertTrue($module->has('client'));
        $this->assertInstanceOf(Client::class, $module->get('client'));
    }
}
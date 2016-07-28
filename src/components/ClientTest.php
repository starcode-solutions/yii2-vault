<?php

namespace starcode\yii\vault\components;

use starcode\yii\vault\exceptions\SecretPathNotFound;
use yii\base\Component;
use yii\base\InvalidConfigException;
use yii\helpers\Json;

class ClientTest extends Component implements ClientInterface
{
    public $dir = '/tmp/vault';

    public function init()
    {
        if (!is_dir(\Yii::getAlias($this->dir))) {
            if (!mkdir(\Yii::getAlias($this->dir), 0777)) {
                throw new InvalidConfigException("Failed to make dir {$this->dir}");
            }
        }
    }

    public function listSecrets($path)
    {
        $dir = \Yii::getAlias($this->dir);
        $fullPath = implode(DIRECTORY_SEPARATOR, [
            rtrim($dir, DIRECTORY_SEPARATOR),
            rtrim(str_replace('/', DIRECTORY_SEPARATOR, $path), DIRECTORY_SEPARATOR),
        ]);
        
        if (!is_dir($fullPath)) {
            return [];
        }
        
        $secretFiles = array_filter(scandir($fullPath), function($file) { return !in_array($file, ['.', '..']); });
        foreach ($secretFiles as $i => $file) {
            $fullFilePath = implode(DIRECTORY_SEPARATOR, [rtrim($dir, DIRECTORY_SEPARATOR), $file]);
            if (is_dir($fullFilePath)) {
                $secretFiles[$file] = $this->listSecrets($path . '/' . $file);
            } else {
                $secretFiles[$file] = file_get_contents($fullFilePath);
            }
            unset($secretFiles[$i]);
        }
        
        return $secretFiles;
    }

    public function read($path)
    {
        $dir = \Yii::getAlias($this->dir);
        $fullPath = implode(DIRECTORY_SEPARATOR, [
            rtrim($dir, DIRECTORY_SEPARATOR),
            ltrim(str_replace('/', DIRECTORY_SEPARATOR, $path), DIRECTORY_SEPARATOR),
        ]);

        if (!is_file($fullPath)) {
            throw new SecretPathNotFound($path);
        }

        $body = file_get_contents($fullPath);
        return Json::decode($body, true);
    }

    public function write($path, $body)
    {
        $dir = \Yii::getAlias($this->dir);
        $fullPath = implode(DIRECTORY_SEPARATOR, [
            rtrim($dir, DIRECTORY_SEPARATOR),
            ltrim(str_replace('/', DIRECTORY_SEPARATOR, $path), DIRECTORY_SEPARATOR),
        ]);

        if (!file_exists(dirname($fullPath))) {
            if (!mkdir(dirname($fullPath), 0777, true)) {
                throw new InvalidConfigException('Failed to create secret dir');
            }
        }

        if (is_object($body) || is_array($body)) {
            $body = Json::encode($body);
        }
        file_put_contents($fullPath, $body);
    }

    public function delete($path)
    {
        $dir = \Yii::getAlias($this->dir);
        $fullPath = implode(DIRECTORY_SEPARATOR, [
            rtrim($dir, DIRECTORY_SEPARATOR),
            ltrim(str_replace('/', DIRECTORY_SEPARATOR, $path), DIRECTORY_SEPARATOR),
        ]);

        if (!is_file($fullPath)) {
            throw new SecretPathNotFound($path);
        }

        unlink($fullPath);
    }
}
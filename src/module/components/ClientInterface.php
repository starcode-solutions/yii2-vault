<?php

namespace app\modules\vault\components;

interface ClientInterface
{
    public function listSecrets($path);
    public function read($path);
    public function write($path, $body);
    public function delete($path);
}
<?php

namespace app\modules\vault\exceptions;

use Exception;

class SecretPathNotFound extends VaultException
{
    private $_path;

    public function __construct($path, Exception $previous = null)
    {
        $this->_path = $path;
        parent::__construct("Not found vault secret path {$path}", 0, $previous);
    }

    /**
     * @return string
     */
    public function getPath()
    {
        return $this->_path;
    }
}
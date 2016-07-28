<?php

namespace app\modules\vault\components;

use app\modules\vault\exceptions\ParseJsonInputException;
use app\modules\vault\exceptions\SecretPathNotFound;
use app\modules\vault\exceptions\VaultException;
use GuzzleHttp\Exception\ClientException;
use yii\base\Component;
use yii\base\InvalidConfigException;
use yii\helpers\ArrayHelper;
use GuzzleHttp\Client as GuzzleClient;

class Client extends Component implements ClientInterface
{
    /**
     * @var string Vault server address.
     */
    public $address = 'localhost:8200';

    public $endPoint = '/v1';

    /**
     * @var string Client token.
     */
    public $token;

    /**
     * @var GuzzleClient Use guzzle library for send requests.
     */
    private $_guzzleClient;

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        if (empty($this->token)) {
            throw new InvalidConfigException('Token is required');
        }
    }

    /**
     * List secrets in path.
     *
     * @param $path
     * @return array JSON response as associative array
     * @throws SecretPathNotFound
     */
    public function listSecrets($path)
    {
        try {
            $response = $this->request('GET', $path, [
                'query' => [
                    'list' => true,
                ],
            ]);

            return json_decode($response->getBody()->getContents(), true);
        } catch (ClientException $e) {
            if ($e->hasResponse()) {
                $response = $e->getResponse();

                if ($response->getStatusCode() == 404) {
                    throw new SecretPathNotFound($path, $e);
                }

                throw $e;
            }
        }
    }

    /**
     * Read vault secret.
     *
     * @param $path
     * @return array JSON response as associative array
     * @throws SecretPathNotFound
     */
    public function read($path)
    {
        try {
            $response = $this->request('GET', $path);

            return json_decode($response->getBody()->getContents(), true);
        } catch (ClientException $e) {
            if ($e->hasResponse()) {
                $response = $e->getResponse();

                if ($response->getStatusCode() == 404) {
                    throw new SecretPathNotFound($path, $e);
                }

                throw $e;
            }
        }
    }

    /**
     * Write vault secret.
     *
     * @param $path
     * @param $body
     * @return mixed|\Psr\Http\Message\ResponseInterface
     */
    public function write($path, $body)
    {
        if (is_object($body) || is_array($body)) {
            $body = json_encode($body);
        }

        return $this->request('POST', $path, [
            'body' => $body,
        ]);

    }

    /**
     * Delete vault secret.
     *
     * @param $path
     * @return mixed|\Psr\Http\Message\ResponseInterface
     * @throws SecretPathNotFound
     */
    public function delete($path)
    {
        try {
            return $this->request('DELETE', $path);
        } catch (ClientException $e) {
            if ($e->hasResponse()) {
                $response = $e->getResponse();

                if ($response->getStatusCode() == 404) {
                    throw new SecretPathNotFound($path, $e);
                }

                throw $e;
            }
        }
    }

    public function request($method, $path, $options = [])
    {
        $options = ArrayHelper::merge($options, [
            'headers' => [
                'X-Vault-Token' => $this->token,
            ],
        ]);

        return $this->getGuzzleClient()->request($method, $path, $options);
    }

    /**
     * @return GuzzleClient
     */
    public function getGuzzleClient()
    {
        if (!($this->_guzzleClient instanceof GuzzleClient)) {
            $this->_guzzleClient = new GuzzleClient([
                'base_uri' => $this->address . '/' . trim($this->endPoint, '/') . '/',
            ]);
        }
        return $this->_guzzleClient;
    }
}
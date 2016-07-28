# yii2-vault
[Vault](https://www.vaultproject.io/) integration for Yii2.
## About Vault by HashiCorp
![Vault](https://www.vaultproject.io/assets/images/hero-95b4a434.png)

[Vault](https://www.vaultproject.io/) secures, stores, and tightly controls access to tokens, passwords, certificates, API keys, and other secrets in modern computing. Vault handles leasing, key revocation, key rolling, and auditing. Through a unified API, users can access an encrypted Key/Value store and network encryption-as-a-service, or generate AWS IAM/STS credentials, SQL/NoSQL databases, X.509 certificates, SSH credentials, and more.
## How to install
Install from composer
```
composer require starcode-krasnodar/yii2-vault
```
or add to composer.json
```
  // ...
  "require": {
    // ...
    "starcode-krasnodar/yii2-vault": "dev-master",
    // ...
  }
  // ...
```
Add vault module in your application config
```php
<?php
return [
    // ...
    'modules' => [
        // other modules ...
        'vault' => [
            'class' => 'starcode',
            // module config params ...
        ],
    ],
    // ...
];
```
## CLI for vault module
Module provide CLI for yii2 app:

* `vault/client/list` list of vault secrets in path (first argument).
* `vault/client/read` read secret data by path (first argument)
* `vault/client/write` write secret data (first argument path, second JSON-string value)
* `vault/client/delete` delete secret data

### Examples

Create new secret

```sh 
php yii vault/client/write secret/path/to/secret/key '{"value": "some secret"}'
```

Read exist secret

```sh
php yii vault/client/read secret/path/to/secret/key

# output
Array
(
    [value] => some secret
)
```

Delete secret

```php
php yii vault/client/read secret/path/to/secret/key
```

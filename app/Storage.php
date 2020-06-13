<?php

class Storage
{
    protected static ?Storage $_instance = null;
    public static function getInstance()
    {
        self::$_instance = self::$_instance ?: new Storage();
        return self::$_instance;
    }

    protected PDO $pdo;

    protected function __construct()
    {
        global $config;

        $provider = $config['db']['provider'] ?? 'mysql';

        if (!empty($config['db']['socket'])) {
            $hostPart = 'unix_socket=' . $config['db']['socket'];
        } elseif (!empty($config['db']['host'])) {
            $hostPart = 'host=' . $config['db']['host'];
            if (!empty($config['db']['port'])) {
                $hostPart .= ';port=' . $config['db']['port'];
            }
        } else {
            throw new Exception('No host or unix socket path to db');
        }

        $dsn = $provider . ':' . $hostPart .
            ';dbname=' . $config['db']['db'] .
            ';charset=' . $config['db']['charset'];

        $opt = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
            PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci'
        ];

        $this->pdo = new PDO($dsn, $config['db']['user'], $config['db']['password'], $opt);
    }

    public function getAccount($id, $noActive = true)
    {
        $queryAccount = 'SELECT `id`, `name`, `active` FROM `accounts` WHERE `id` = :id';

        $pdoQueryAccount = $this->pdo->prepare($queryAccount);
        $pdoQueryAccount->execute(['id' => $id]);
        if (!$pdoQueryAccount->rowCount()) {
            return null;
        }

        $account = $pdoQueryAccount->fetch();
        $account['active'] = !empty($account['active']);
        if ($noActive && empty($account['active'])) return null;

        $account['allowed_sites'] = $this->getAllowedSites($account['id']);

        return $account;
    }

    public function getLogin($provider, $login, $onlyActive = true)
    {
        $queryLogin = 'SELECT `id`, `account_id`, `provider`, `login`, `active`, `data` FROM `logins` WHERE `provider` = :provider AND `login` = :login';
        if ($onlyActive) $queryLogin .= ' AND `active` = 1';
        $pdoQueryLogin = $this->pdo->prepare($queryLogin);
        $pdoQueryLogin->execute(['provider' => $provider, 'login' => $login]);

        if (!$pdoQueryLogin->rowCount()) {
            return null;
        }

        $account = $pdoQueryLogin->fetch();
        $account['data'] = json_decode($account['data'], true);

        return $account;
    }

    public function getAccountList()
    {
        $query = 'SELECT `id`, `active`, `name` FROM `accounts`';
        $pdoQuery = $this->pdo->prepare($query);
        $pdoQuery->execute();

        return $pdoQuery->fetchAll();
    }

    public function addAccount($name)
    {
        $name = trim($name);
        if (empty($name)) return 0;

        try {
            $query = 'INSERT INTO `accounts` (`name`, `active`) VALUES (:name, 1)';
            $pdoQuery = $this->pdo->prepare($query);
            $pdoQuery->execute(['name' => $name]);

            return $this->pdo->lastInsertId();
        } catch (Exception $ex) {
            return 0;
        }
    }

    public function updateAccount($id, $data)
    {
        if (empty($id)) return 0;
        $toUpdate = array_intersect_key($data, ['name' => 1, 'active'  => 1]);

        if (isset($toUpdate['name']) && empty($toUpdate['name'])) return  0;
        if (isset($toUpdate['active']) && !in_array($toUpdate['active'], [0, 1])) return  0;

        if (empty($toUpdate)) return 0;
        try {
            $query = 'UPDATE `accounts` SET ' . implode(', ', array_map(function ($x) {
                return "$x = :$x";
            }, array_keys($toUpdate))). ' WHERE id = :id';
            $pdoQuery = $this->pdo->prepare($query);
            $pdoQuery->execute(['id' => $id] + $toUpdate);

            return $pdoQuery->rowCount();
        } catch (Exception $ex) {
            return 0;
        }
    }

    public function setAccountActive($id, $active)
    {
        return $this->updateAccount($id, ['active' => $active]);
    }

    public function deleteAccount($id)
    {
        if (empty($id)) return 0;

        try {
            $query = 'DELETE FROM `accounts` WHERE id = :id';
            $pdoQuery = $this->pdo->prepare($query);
            $pdoQuery->execute(['id' => $id]);

            return $pdoQuery->rowCount();
        } catch (Exception $ex) {
            return 0;
        }
    }

    public function getAllowedSites($id)
    {
        if (empty($id)) return [];

        $querySites = 'SELECT `site` FROM `allowed_sites` WHERE `account_id` = :id';
        $pdoQuerySites = $this->pdo->prepare($querySites);
        $pdoQuerySites->execute(['id' => $id]);

        return array_column($pdoQuerySites->fetchAll(), 'site');
    }

    public function setAllowedSites($id, $sites)
    {
        $queryDelete = "DELETE FROM `allowed_sites` WHERE `account_id` = :id";
        $pdoQueryDelete = $this->pdo->prepare($queryDelete);
        $pdoQueryDelete->execute(['id' => $id]);

        if (!empty($sites) && is_array($sites)) {
            $items = array_map(function ($x) use ($id) {
                $id = $this->pdo->quote($id);
                $x = $this->pdo->quote($x);
                return "({$id}, {$x})";
            }, $sites);
            $items = implode(', ', $items);
            $queryInsert = "INSERT INTO `allowed_sites` (`account_id`, `site`) VALUES {$items}";
            $pdoQueryInsert = $this->pdo->prepare($queryInsert);
            $pdoQueryInsert->execute();
        }
    }

    public function getUserLoginByProvider($provider, $accountId)
    {
        if (!@class_exists($provider)) return null;
        $providerInstance = new $provider();
        if (!($providerInstance instanceof Abstract_LoginProvider)) return null;
        if(!@method_exists($providerInstance, 'adminFieldsList')) return null;
        $fields = $providerInstance->adminFieldsList();

        $queryLogin = 'SELECT `id`, `account_id`, `provider`, `login`, `active`, `data` FROM `logins` WHERE `provider` = :provider AND `account_id` = :account_id';
        $pdoQueryLogin = $this->pdo->prepare($queryLogin);
        $pdoQueryLogin->execute(['provider' => $provider, 'account_id' => $accountId]);

        $items = $pdoQueryLogin->fetchAll();
        $result = [];
        foreach ($items as $item) {
            $xItem = [
                '_login' => $item['login'],
                '_active' => $item['active'],
            ];
            $itemData = json_decode($item['data'], true) ?: [];
            foreach ($fields as $fieldName => $field) {
                if (substr($fieldName, 0, 1) == '_') continue;
                if (empty($field['get'])) {
                    $xItem[$fieldName] = null;
                    continue;
                }
                $xItem[$fieldName] = $itemData[$fieldName] ?? null;
            }
            $result[$item['id']] = $xItem;
        }

        return [
            'providerName' => $provider::getName(),
            'fields' => $fields,
            'data' => $result,
        ];
    }

    public function setUserLoginByProvider($provider, $id, $data)
    {
        if (!is_array($data)) return null;
        if (!@class_exists($provider)) return null;
        $providerInstance = new $provider();
        if (!($providerInstance instanceof Abstract_LoginProvider)) return null;
        if(!@method_exists($providerInstance, 'adminFieldsProcess')) return null;

        foreach ($data as $itemId => $itemValues) {
            if (!empty($itemValues['_delete'])) {
                $query = 'DELETE FROM `logins` WHERE id = :id';
                $pdoQuery = $this->pdo->prepare($query);
                $pdoQuery->execute(['id' => $itemId]);
                continue;
            } else {
                if (empty($itemValues['_login'])) continue;
                if ($itemId != 'new') {
                    $queryLogin = 'SELECT `id`, `account_id`, `provider`, `login`, `active`, `data` FROM `logins` WHERE `id` = :id';
                    $pdoQueryLogin = $this->pdo->prepare($queryLogin);
                    $pdoQueryLogin->execute(['id' => $itemId]);
                    if (!$pdoQueryLogin->rowCount()) {
                        continue;
                    }
                    $oldRow = $pdoQueryLogin->fetch();
                    $oldRow['data'] = json_decode($oldRow['data'], true) ?: [];
                } else {
                    $oldRow = [];
                }

                $itemValuesProcessed = $providerInstance->adminFieldsProcess($itemValues, $oldRow);
                if (empty($itemValuesProcessed) || empty($itemValuesProcessed['_login'])) continue;

                $row = [
                    'login' => $itemValuesProcessed['_login'],
                    'provider'=> $provider,
                    'account_id'=> $id,
                    'active' => $itemValuesProcessed['_active'] ?? 1,
                    'data' => json_encode(array_diff_key($itemValuesProcessed, ['_login' => 1, '_active' => 1])),
                ];

                if ($itemId == 'new') {
                    $keys = array_keys($row);
                    $values = array_map(function ($x) {
                        return ":{$x}";
                    }, $keys);

                    $insertUpdate = 'INSERT IGNORE INTO  `logins` (' . implode(', ', $keys). ') VALUES (' . implode(', ', $values). ')';
                    $pdoQueryInsertUpdate = $this->pdo->prepare($insertUpdate);
                    $pdoQueryInsertUpdate->execute($row);
                } else {
                    $query = 'UPDATE `logins` SET ' . implode(', ', array_map(function ($x) {
                        return "$x = :$x";
                    }, array_keys($row))). ' WHERE id = :id AND provider = :check_provider AND account_id = :check_account_id';
                    $pdoQuery = $this->pdo->prepare($query);
                    $pdoQuery->execute([
                        'id' => $itemId,
                        'check_provider' => $row['provider'],
                        'check_account_id' => $row['account_id']
                    ] + $row);
                }
            }
        }
    }
}

<?php

if (empty($_REQUEST['id'])) die('No user id specified');

include '../../../include.php';

$storage = Storage::getInstance();
$user = $storage->getAccount($_REQUEST['id'], false);
if (empty($user['id'])) die('Account not found');

$action = $_REQUEST['action'] ?? '';
switch ($action) {
    case '':
        break;
    case 'update-account':
        $storage->updateAccount($_REQUEST['id'], $_REQUEST);
        http_response_code(302);
        header("Location: /users/user.php?id={$_REQUEST['id']}");
        break;
    case 'update-sites':
        $storage->setAllowedSites($_REQUEST['id'], $_REQUEST['sites'] ?? []);
        http_response_code(302);
        header("Location: /users/user.php?id={$_REQUEST['id']}");
        break;
    case 'set-login-provider':
        $storage->setUserLoginByProvider(@$_REQUEST['provider'], $_REQUEST['id'], $_REQUEST['data'] ?? []);
        http_response_code(302);
        header("Location: /users/user.php?id={$_REQUEST['id']}");
        break;
}

?><!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="/css/main.css">
    <title>User editor</title>
</head>
<body>
<div class="wrapper">
    <div class="user-change">
        <div class="container">
            <div class="user-change__inner">
                <h1 class="site__title">
                    <a href="/" class="site__title-text">AUTH ADMIN PAGE</a>
                </h1>

                <ul class="short__link">
                    <li><a href="/">Home→</a></li>
                    <li><a href="/users/">Users→</a></li>
                    <li><a href="/users/user.php?id=<?= $_REQUEST['id'] ?>">User editor</a></li>
                </ul>

              <div class="update-account">
                  <h2 class="update__title">Update account info</h2>
                  <form method="post">
                      <input type="hidden" name="action" value="update-account">
                      <table>
                          <tr>
                              <th>Id</th>
                              <td><?= $user['id'] ?></td>
                          </tr>
                          <tr>
                              <th><label for="account-name">Name</label></th>
                              <td><input type="text" name="name" id="account-name" value="<?= $user['name'] ?>"></td>
                          </tr>
                          <tr>
                              <th><label for="account-active">Active</label></th>
                              <td>
                                  <select name="active" id="account-active" style="width: 100%">
                                      <option value="0"<?= $user['active'] ? '' : ' selected="selected"' ?>>No</option>
                                      <option value="1"<?= $user['active'] ? ' selected="selected"' : '' ?>>Yes</option>
                                  </select>
                              </td>
                          </tr>
                          <tr>
                              <td colspan="3">
                                  <button type="submit" class="hbtn hb-fill-top-rev">Save user data</button>
                              </td>
                          </tr>
                      </table>
                  </form>
              </div>

                <div class="update-account">
                    <h2 class="update__title">Update allowed sites</h2>
                    <form method="post">
                        <input type="hidden" name="action" value="update-sites">
                        <ul>
                            <? foreach ($config['sites'] as $site): ?>
                                <li class="update__set">
                                    <input type="checkbox" name="sites[]" value="<?= $site ?>"<?= in_array($site, $user['allowed_sites']) ? 'checked="checked"' : ''?>>
                                    <?= $site ?>
                                </li>
                            <? endforeach; ?>
                        </ul>
                        <button type="submit" class="hbtn hb-fill-top-rev">Save allowed sites</button>
                    </form>
                </div>

                <h2 class="update__title">Update login methods</h2>

                <? foreach ($config['login_providers'] as $provider): ?>
                    <? $data = $storage->getUserLoginByProvider($provider, $user['id']); ?>
                    <? if (empty($data)) continue; ?>
                    <form>
                        <input type="hidden" name="action" value="set-login-provider">
                        <input type="hidden" name="id" value="<?= $user['id'] ?>">
                        <input type="hidden" name="provider" value="<?= $provider ?>">

                        <h3 class="update__title"><?= $provider ?> (<?= $data['providerName'] ?>)</h3>
                        <table border="1">
                            <thead>
                            <tr>
                                <th>Id</th>
                                <? foreach ($data['fields'] as $fKey => $field): ?>
                                    <th><?= $field['name'] ?></th>
                                <? endforeach; ?>
                                <th>Active</th>
                                <th class="warning" title="Delete">[D]</th>
                            </tr>
                            </thead>
                            <tbody>
                            <? foreach ($data['data'] as $dataId => $dataItem): ?>
                                <tr>
                                    <td>
                                        <?= $dataId ?>
                                    </td>
                                    <? foreach ($data['fields'] as $fKey => $field): ?>
                                        <td>
                                            <? if (!empty($field['set'])): ?>
                                                <input type="<?= $field['input'] ?? 'text'?>" name="data[<?= $dataId ?>][<?= $fKey ?>]" value="<?= $dataItem[$fKey] ?>" autocomplete="off">
                                            <? else: ?>
                                                <?= $dataItem[$fKey] ?>
                                            <? endif; ?>
                                        </td>
                                    <? endforeach; ?>
                                    <td>
                                        <select name="data[<?= $dataId ?>][_active]">
                                            <option value="0"<?= $dataItem['_active'] ? '' : ' selected="selected"' ?>>No</option>
                                            <option value="1"<?= $dataItem['_active'] ? ' selected="selected"' : '' ?>>Yes</option>
                                        </select>
                                    </td>
                                    <td>
                                        <input type="checkbox" name="data[<?= $dataId ?>][_delete]" title="Delete" value="1">
                                    </td>
                                </tr>
                            <? endforeach; ?>
                            <tr>
                                <td>
                                    New
                                </td>
                                <? foreach ($data['fields'] as $fKey => $field): ?>
                                    <td>
                                        <input type="<?= $field['input'] ?? 'text'?>" name="data[new][<?= $fKey ?>]" value="" autocomplete="off">
                                    </td>
                                <? endforeach; ?>
                                <td>
                                    <select name="data[new][_active]">
                                        <option value="0">No</option>
                                        <option value="1" selected="selected">Yes</option>
                                    </select>
                                </td>
                                <td></td>
                            </tr>
                            </tbody>
                        </table>
                        <button type="submit" class="hbtn hb-fill-top-rev">Save</button>
                    </form>
                <? endforeach; ?>


            </div>
        </div>
    </div>
</div>
</body>
</html>

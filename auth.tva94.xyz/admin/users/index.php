<?php
include '../../../include.php';

$storage = Storage::getInstance();

$action = $_REQUEST['action'] ?? '';
switch ($action) {
    case '':
        break;
    case 'register':
        $storage->addAccount($_REQUEST['name'] ?? null);
        http_response_code(302);
        header("Location: /users");
        break;
    case 'delete':
        $storage->deleteAccount($_REQUEST['id'] ?? null);
        http_response_code(302);
        header("Location: /users");
        break;
    case 'active':
        $storage->setAccountActive($_REQUEST['id'] ?? null, $_REQUEST['active'] ?? null);
        http_response_code(302);
        header("Location: /users");
        break;
}

$list = $storage->getAccountList();

?><!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="/css/main.css">
    <title>Users list & manage</title>
</head>
<body>
<div class="wrapper">
    <div class="users">
        <div class="container">
            <div class="users__inner">
                <h1 class="site__title">
                    <a href="/" class="site__title-text">AUTH ADMIN PAGE</a>
                </h1>

                <ul class="short__link">
                    <li><a href="/">Home→</a></li>
                    <li><a href="/users/">Users</a></li>
                </ul>


                <table border="1" class="users-table">
                    <thead>
                    <tr>
                        <th>Id</th>
                        <th>Name</th>
                        <th>Active</th>
                        <th></th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach($list as $item): ?>
                        <tr class="users-data">
                            <td><?= $item['id'] ?></td>
                            <td><?= $item['name'] ?></td>
                            <td>
                                <? if ($item['active']): ?>
                                    <a href="?action=active&active=0&&id=<?= $item['id'] ?>">[Disable]</a>
                                <? else: ?>
                                    <a href="?action=active&active=1&&id=<?= $item['id'] ?>">[Enable]</a>
                                <? endif; ?>
                            </td>
                            <td>
                                <a href="/users/user.php?id=<?= $item['id'] ?>">[E]</a>
                                <a href="?action=delete&id=<?= $item['id'] ?>" onclick="return confirm('Are you sure?')">[D]</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    <form method="post">
                        <input type="hidden" name="action" value="register">
                        <tr>
                            <td>*</td>
                            <td>
                                <input type="text" name="name">
                            </td>
                            <td colspan="2">
                                <button type="submit" class="hbtn hb-fill-top-rev">Register</button>
                            </td>
                        </tr>
                    </form>
                    </tbody>
                </table>

            </div>
        </div>
    </div>
</div>
</body>
</html>

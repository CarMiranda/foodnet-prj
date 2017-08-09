<?php
try {
    
    include 'core/classes/DB.php';
    include 'core/classes/ORM.php';
    include 'core/classes/User.php';
    include 'core/helpers.php';
    include 'loader.php';

    header('Content-type: text/json;charset=UTF-8');

    if ($_REQUEST['path'] == '' || $_REQUEST['path'] == '/') {
        header('Content-type: text/html;');
        ?>
            <!DOCTYPE html>
            <html>
                <head>
                </head>
                <body>
                    <h1>KDF back-end API test site</h1>
                </body>
            </html>
        <?php
    } else if (preg_match('/^users\/?$/', $_REQUEST['path'])) {
        if ($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_REQUEST['id'])) {
            $user = new User($_REQUEST['id'], $_REQUEST['showFavorites'] == 1, $_REQUEST['showFriends'] == 1, $_REQUEST['showGroups'] == 1);
            echo $user->show();
        } else if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_REQUEST['mail'])) {
            if (!User::exists()) {
                $user = new User();
                var_dump($user); die;
                $user->save();
                echo $user->show();
            } else {
                header('Content-type: text/html;');
                ?>
                    <!DOCTYPE html>
                    <html>
                        <head>
                        </head>
                        <body>
                            <h1>Error. User with email <?=$_REQUEST['mail']?> already exists.</h1>
                        </body>
                    </html>
                <?php
            }
        } else if ($_SERVER['REQUEST_METHOD'] == 'PUT' && isset($_REQUEST['id'])) {
            $user = new User($_REQUEST['id']);
            $user->update();
            echo $user->show();
        } else if ($_SERVER['REQUEST_METHOD'] == 'DELETE' && isset($_REQUEST['id'])) {
            echo User::delete($_REQUEST['id']);
        }
    } else if (preg_match('/^users\/favorites\/?$/', $_REQUEST['path'])) {
        if ($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_REQUEST['id'])) {
            echo json_encode(User::getFavorites($_REQUEST['id']));
        } else if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_REQUEST['id']) && isset($_REQUEST['tid'])) {
            echo User::addFavorite($_REQUEST['id'], $_REQUEST['tid']);
        } else if ($_SERVER['REQUEST_METHOD'] == 'DELETE' && isset($_REQUEST['id']) && isset($_REQUEST['tid'])) {
            echo User::deleteFavorite($_REQUEST['id'], $_REQUEST['tid']);
        }
    }
} catch (Exception $e) {
    echo $e->getMessage();
}

?>
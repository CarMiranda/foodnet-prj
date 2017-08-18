<?php
try {

    
    include 'core/classes/DB.php';
    include 'core/classes/ORM.php';
    include 'core/classes/User.php';
    include 'core/helpers.php';
    include 'loader.php';

    header('Content-type: text/json;charset=UTF-8');

    // Case when nothing is requested
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
    } else {
        $headers = getallheaders(); // Get headers from HTTP request
        if (empty($headers['Authorization'])) { // If Authorization header is empty, send 401
            header('WWW-Authenticate: Bearer realm="KDF Authentication"');
            header('HTTP/1.1 401 Unauthorized');
        }

        // Users actions
        if (rtrim($_REQUEST['path'], '/') == 'users') {
            if ($_SERVER['REQUEST_METHOD'] == 'GET') {


                if (!preg_match('/^Bearer\s.+/', $headers['Authorization'])) {
                    throw new Exception("Invalid authorization header.", 1);
                }
                $_jwt = explode(' ', $headers['Authorization'])[1];
                if (!validateJWT($_jwt)) {
                    throw new Exception("Authentication error.");
                }
                $_jwt = decodeJWT($_jwt);
                $payload = json_decode($_jwt[1]);
                if (empty($payload)) {
                    throw new Exception("No identifier in authentication token.");
                }
                $user = new User($_REQUEST['id'], $_REQUEST['showFavorites'] == 1, $_REQUEST['showFriends'] == 1, $_REQUEST['showGroups'] == 1);
                echo $user->show();
            } else if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_REQUEST['mail'])) {
                if (!User::exists()) {
                    $user = new User();
                    var_dump($user); die;
                    $user->save();
                    echo $user->show();
                } else {
                    throw new Exception("User with email {$_REQUEST['mail']} already exists.");
                }
            } else if ($_SERVER['REQUEST_METHOD'] == 'PUT' && isset($_REQUEST['id'])) {
                $user = new User($_REQUEST['id']);
                $user->update();
                echo $user->show();
            } else if ($_SERVER['REQUEST_METHOD'] == 'DELETE' && isset($_REQUEST['id'])) {
                echo User::delete($_REQUEST['id']);
            }
        } else if (preg_match('/^users\/login\/?$/', $_REQUEST['path'])) {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                throw new Exception('Bad request.');
            }

            if (!empty($_REQUEST['mail'])) {
                $id = $_REQUEST['mail'];
            } else if (!empty($_REQUEST['uname'])) {
                $id = $_REQUEST['uname'];
            } else {
                throw new Exception('No identifier for login.');
            }

            if (User::exists($id)) {
                $user = new User($id);
                if ($user->checkPassword($_REQUEST['password'])) {
                    $response->status = "success";
                    $user_spec = [
                        "idt" => $user->id,
                        "sub" => $user->uname,
                    ];
                    $_data->jwt = encodeJWT($user_spec);
                    $response->data = $_data;
                    header("Content-type: text/json;charset=UTF-8");
                    header("HTTP/1.1 200 OK");
                    echo json_encode($response);
                } else {
                    throw new Exception("Wrong password.");
                }
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
    }
} catch (Exception $e) {
    header('Content-type: text/plain;charset=UTF-8');
    echo $e->getMessage();
}

?>
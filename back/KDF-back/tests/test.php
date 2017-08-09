<?php ?>
<!DOCTYPE html>
<html>
<head>
</head>
<body>
<?php
try {
    include('./config/database.php');
    include('./config/globals.php');
    include('./core/classes/DB.php');
    include('./core/classes/ORM.php');
    include('./loader.php');
    include('./User.php');

    ORM::set_db(DB::factory('users'));
    $user = new User('Uname1');
    echo $user->username;

} catch (Exception $e) {
    echo $e->getMessage();
}
?>
</body>


<?php

    class Group {
        
        public function __construct() {

        }

        public static function getGroup($gid) {
            ORM::set_db(DB::factory('app'), 'app');
            $res = ORM::for_table('groups')->find_one($gid);
            return $res;
        }

        public static function getUsers($gid) {
            ORM::set_db(DB::factory('users'), 'users');
            $res = ORM::for_table('users')
                        ->select_many(['`users`.`id`', '`users`.`uname`', '`' . APP__DB_NAME . '`.`users_x_groups`.`visibility`'])
                        ->left_outer_join('`' . APP__DB_NAME . '`.`users_x_groups`', '`users_x_groups`.`uid` = `users`.`id`')
                        ->where('`users_x_groups`.`gid`', $gid)
                        ->find_many();
            return $res;
        }

        public static function addGroup($name, $avatar, $visibility) {
            $db = DB::factory('app');
            $res = $db->query('INSERT INTO `' . APP__DB_NAME . '`.`groups` (`name`, `avatar`, `visibility`) VALUES (' . $name . ', ' . $avatar . ', ' . $visibility . ');');
            return $res;
        }

        public static function updateGroup($uid, $gid, $admin) {
            $db = DB::factory('app');
            $res = $db->query('UPDATE `' . APP__DB_NAME . '`.`groups`');
            return $res;
        }
    }
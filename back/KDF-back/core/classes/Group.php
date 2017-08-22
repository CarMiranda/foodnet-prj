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

        public static function create($id, $group_info) {
            ORM::set_db(DB::factory('app'), 'app');
            $group = ORM::for_table('groups', 'app')->create();
            if (isset($group_info->id)) unset($group_info->id);
            $_ginfo = (array)$group_info;
            foreach ($_ginfo as $key => $value) {
                $group->{$key} = $value;
            }
            $group->save();
            return TRUE;
        }

        public static function update($uid, $group_info) {
            ORM::set_db(DB::factory('app'), 'app');
            $group = ORM::for_table('groups', 'app')
                     ->find_one($group_info->id);
            $_ginfo = (array)$group_info;
            array_shift($_ginfo);
            foreach ($_ginfo as $key => $value) {
                $group->{$key} = $value;
            }
            $group->save();
            return TRUE;
        }

        public static function isAdmin($id, $gid) {
            ORM::set_db(DB::factory('app'), 'app');
            $res = ORM::for_table('users_x_groups', 'app')
                        ->select('status')
                        ->where('uid', $id)
                        ->where('gid', $gid)
                        ->find_one();
            return $res == 1;
        }
    }
<?php

    class Group {
        
        public function __construct() {

        }

        public static function get($gid) {
            ORM::set_db(DB::factory('app'), 'app');
            $res = ORM::for_table('groups', 'app')->find_one($gid);
            if (!$res) {
                throw new Exception("Group not found.");
            }
            return (object)$res->as_array();
        }

        public static function getUsers($gid) {
            ORM::set_db(DB::factory('users'), 'users');
            $res = ORM::for_table('users', 'users')
                        ->select_many(['users.id', 'users.uname', APP__DB_NAME . '.users_x_groups.visibility'])
                        ->left_outer_join(APP__DB_NAME . '.users_x_groups', 'users_x_groups.uid = users.id')
                        ->where('users_x_groups.gid', $gid)
                        ->find_many();
            $users = [];
            foreach ($res as $row) {
                $users[] = (object)$row->as_array();
            }
            return $users;
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

            //add user to group
            User::addToGroup($id, $group_info->id, 1);
            return TRUE;
        }

        public static function update($uid, $group_info) {
            ORM::set_db(DB::factory('app'), 'app');
            $group = ORM::for_table('groups', 'app')
                     ->find_one($group_info->id);
            if (!$group) {
                throw new Exception("Group not found.");
            }
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
<?php

    class Group {

        public static function getUsers($group_id) {
            ORM::set_db(DB::factory('users'), 'users');
            $res = ORM::for_table('users', 'users')
                        ->select('users.id', 'id')
                        ->select('users.uname', 'name')
                        ->left_outer_join(APP__DB_NAME . '.users_x_groups', 'users_x_groups.user_id = users.id')
                        ->where('users_x_groups.group_id', $group_id)
                        ->where_not_equal('status', 2)
                        ->find_many();
            return $res;
        }

        public static function getChat($group_id) {
            ORM::set_db(DB::factory('app'), 'app');
            $res = ORM::for_table('group_chats', 'app')
                        ->where_id_is('group_id')
                        ->find_one($group_id);
            return $res;
        }

        public static function get($group_id) {
            ORM::set_db(DB::factory('app'), 'app');
            $res = ORM::for_table('groups', 'app')->find_one($group_id);
            if (!$res) {
                throw new Exception("Group not found.");
            }
            return $res;
        }

        public static function create($id, $group_info) {
            ORM::set_db(DB::factory('app'), 'app');
            $group = ORM::for_table('groups', 'app')->create();
            if (isset($group_info->id)) unset($group_info->id);
            $_ginfo = (array)$group_info;
            foreach ($_ginfo as $key => $value) {
                if (property_exists($group, $key)) {
                    $group->{$key} = $value;
                }
            }
            $group->save();

            //add user to group
            User::addToGroup($id, $group_info->id, 1);
            return $group;
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
                if (property_exists($group, $key)) {
                    $group->{$key} = $value;
                }
            }
            $group->save();
            return $group;
        }
        
        public static function remove($user_id, $group_id) {
            if (!empty($group_id)) {
                ORM::set_db(DB::factory('app'), 'app');
                $res = ORM::for_table('groups', 'app')->find_one($group_id);
                if ($res && Group::isAdmin($user_id, $group_id)) {
                    $res->delete();
                    return TRUE;
                } else if (!$res) {
                    throw new Exception('Group does not exist.');
                } else {
                    throw new Exception('Not an admin.');
                }
            }
            return FALSE;
        }

        /**
        * 
        */
        public static function isAdmin($user_id, $group_id) {
            ORM::set_db(DB::factory('app'), 'app');
            $res = ORM::for_table('users_x_groups', 'app')
                        ->select('status')
                        ->where('group_id', $group_id)
                        ->where_id_is('user_id')
                        ->find_one($user_id);
            return $res == 1;
        }
    }
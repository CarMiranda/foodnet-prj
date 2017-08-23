<?php

    class Post {

        public static function getByUser($id, $offset) {
            ORM::set_db(DB::factory('app'), 'app');
            $res = ORM::for_table('posts', 'app')
                   ->where('owner', $id);
            if (!empty($offset)) {
                $res->offset($offset);
            }
            $res->find_many();
            $posts = [];
            foreach ($res as $row) {
                $posts[] = (object)$row->as_array();
            }
            return $posts;
        }

        public static function getByGroup($id, $offset) {
            ORM::set_db(DB::config('app'), 'app');
            $res = Group::getUsers($id);
            
        }

        public static function getByFriends() {

        }

        public static function getByRegion() {

        }

        public static function getByRadius() {

        }

        public static function getByTags() {

        }

        public static function get() {

        }

        public static function create() {

        }

        public static function update() {

        }

        public static function remove() {

        }

    }
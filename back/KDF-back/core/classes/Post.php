<?php

    class Post {

        public static function getByUser($id, $limit = 50, $offset = 0) {
            ORM::set_db(DB::factory('app'), 'app');
            $res = ORM::for_table('posts', 'app')
                   ->where('owner', $id)
                   ->limit($limit)
                   ->offset($offset)
                   ->find_many();
            return $res;
        }

        public static function getByGroup($id, $limit = 50, $offset = 0) {
            ORM::set_db(DB::factory('app'), 'app');
            $res = Group::getUsers($id);
            $ids = extractKey($res, "id");
            $res = ORM::for_table('posts', 'app')
                   ->where_in('owner', $ids)
                   ->order_by_asc('created_at')
                   ->limit($limit)
                   ->offset($offset)
                   ->find_many();
            return $res;
            
        }

        public static function getByFriends($user_id, $limit = 50, $offset = 0) {
            ORM::set_db(DB::factory('app'), 'app');
            $res = User::getFriendships($id);
            $ids = extractKey($res, "id");
            $res = ORM::for_table('posts', 'app')
                   ->where_in('owner_id', $ids)
                   ->order_by_asc('created_at')
                   ->limit($limit)
                   ->offset($offset)
                   ->find_many();
            return $res;
        }

        public static function getByRegion($code_postal, $limit = 50, $offset = 0) {
            ORM::set_db(DB::factory('app'), 'app');
            $res = User::getByRegion($code_postal);
            $ids = extractKey($res, "id");
            $res = ORM::for_table('posts', 'app')
                   ->where_in('owner_id', $ids)
                   ->order_by_asc('created_at')
                   ->limit($limit)
                   ->offset($offset)
                   ->find_many();
            return $res;
        }

        public static function getByRadius($lon, $lat, $radius, $limit = 50, $offset = 0) {
            ORM::set_dn(DB::factory('app'), 'app');
            return ORM::raw_execute('CALL geodist(' . $lon . ', ' . $lat . ', ' . $radius . ', ' . $limit . ', ' . $offset . ');');
        }

        public static function getByTags($tags_id, $limit = 50, $offset = 0) {
            ORM::set_db(DB::factory('app'), 'app');
            $res = ORM::for_table('posts', 'app')
                   ->left_outer_join(APP__DB_NAME . '.posts_x_tags', 'posts_x_tags.tag_id IN = posts.id')
                   ->where_in('posts_x_tags', $tags_id)
                   ->order_by_asc('created_at')
                   ->limit($limit)
                   ->offset($offset)
                   ->find_many();
            return $res;
        }

        public static function getById($post_id) {
            ORM::set_db(DB::factory('app'), 'app');
            $res = ORM::for_table('posts', 'app')->find_one($id);
            if (!$res) {
                throw new Exception('Post not found.');
            }
            return $res;
        }

        public static function getComments($post_id, $limit = 10, $offset = 0) {
            ORM::set_db(DB::factory('app'), 'app');
            $res = ORM::for_table('comments', 'app')
                   ->where('post_id', $post_id)
                   ->limit($limit)
                   ->offset($offset)
                   ->find_many();
            return $res;
        }

        public static function addComment($user_id, $post_id, $comment, $created_at) {
            ORM::set_db(DB::factory('app'), 'app');
            $res = ORM::for_table('comments', 'app')->create();
            $res->set([
                'user_id' => $user_id,
                'post_id' => $post_id,
                'comment' => $comment,
                'created_at' => $created_at
            ]);
            $res->save();
            return $res;
        }

        public static function removeComment($comment_id) {
            ORM::set_db(DB::factory('app'), 'app');
            $res = ORM::for_table('comments', 'app')->find_one($comment_id);
            if (!$res) {
                throw new Exception('Comment does not exists.');
            }
            $res->delete();
            return TRUE;
        }

        public static function getLikes($post_id, $limit = 10, $offset = 0) {
            ORM::set_db(DB::factory('app'), 'app');
            $res = ORM::for_table('likes', 'app')
                   ->where('post_id', $post_id)
                   ->limit($limit)
                   ->offset($offset)
                   ->find_many();
            return $res;
        }

        public static function addLike($user_id, $post_id, $like, $created_at = NULL) {
            ORM::set_db(DB::factory('app'), 'app');
            $res = ORM::for_table('likes', 'app')->create();
            $res->set([
                'user_id' => $user_id,
                'post_id' => $post_id,
                'like' => $like,
                'created_at' => $created_at
            ]);
            $res->save();
            return $res;
        }

        public static function removeLike($user_id, $post_id) {
            ORM::set_db(DB::factory('app'), 'app');
            $res = ORM::for_table('likes', 'app')
                   ->where_id_is('user_id')
                   ->where('post_id', $post_id)
                   ->find_one($user_id);
            return $res;
        }

        public static function get($limit = 50, $offset = 0) {
            ORM::set_db(DB::factory('app'), 'app');
            $res = ORM::for_table('posts', 'app')
                   ->order_by_asc('created_at')
                   ->limit($limit)
                   ->offset($offset)
                   ->find_many();
            return $res;
        }

        public static function create($post_info) {
            ORM::set_db(DB::factory('app'), 'app');
            $post = ORM::for_table('posts', 'app')->create();
            if (isset($post_info->id)) unset($post_info->id);
            $_pinfo = (array)$post_info;
            foreach ($_pinfo as $key => $value) {
                $post->{$key} = $value;
            }
            $post->save();
            return $post;
        }

        public static function update($user_id, $post_info) {
            ORM::set_db(DB::factory('app'), 'app');
            $post = ORM::for_table('posts', 'app')->find_one($post_id);
            if (!$post) {
                throw new Exception('Post not found.');
            }
            if ($post->owner == $user_id) {
                throw new Exception('User is not original poster.');
            }
            $_pinfo = (array)$post_info;
            foreach ($_pinfo as $key => $value) {
                $post->{$key} = $value;
            }
            $post->save();
            return $post;
        }

        public static function remove($user_id, $post_id) {
            if (!empty($user_id) && !empty($post_id)) {
                ORM::set_db(DB::factory('app'), 'app');
                $res = ORM::for_table('posts', 'app')->find_one($post_id);
                if ($res && $res->owner == $user_id) {
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

    }
<?php

    class Post {

        public static function getByUser($id, $limit = NULL, $offset = NULL) {
            ORM::set_db(DB::factory('app'), 'app');
            if (empty($limit)) $limit = 50;
            if (empty($offset)) $offset = 0;
            $res = ORM::for_table('posts', 'app')
                   ->where('owner_id', $id)
                   ->limit($limit)
                   ->offset($offset * $limit)
                   ->find_many();
            return $res;
        }

        public static function getByGroup($group_id, $limit = NULL, $offset = NULL) {
            ORM::set_db(DB::factory('app'), 'app');
            $res = Group::getUsers($group_id);
            $ids = extractKey($res, "id");
            if (empty($limit)) $limit = 50;
            if (empty($offset)) $offset = 0;
            $res = ORM::for_table('posts', 'app')
                   ->where_in('owner_id', $ids)
                   ->order_by_asc('created_at')
                   ->limit($limit)
                   ->offset($offset * $limit)
                   ->find_many();
            return $res;
            
        }

        public static function getByFriends($user_id, $limit = NULL, $offset = NULL) {
            ORM::set_db(DB::factory('app'), 'app');
            $res = User::getFriendships($user_id);
            $ids = extractKey($res, "id");
            if (empty($limit)) $limit = 50;
            if (empty($offset)) $offset = 0;
            $res = ORM::for_table('posts', 'app')
                   ->where_in('owner_id', $ids)
                   ->order_by_asc('created_at')
                   ->limit($limit)
                   ->offset($offset * $limit)
                   ->find_many();
            return $res;
        }

        public static function getByRegion($postal_code, $strict = NULL, $limit = NULL, $offset = NULL) {
            if (empty($strict)) $strict = FALSE;
            $res = User::getByRegion($postal_code, $strict);
            $ids = extractKey($res, "id");
            if (empty($limit)) $limit = 50;
            if (empty($offset)) $offset = 0;
            $res = ORM::for_table('posts', 'app')
                   ->where_in('owner_id', $ids)
                   ->order_by_asc('created_at')
                   ->limit($limit)
                   ->offset($offset * $limit)
                   ->find_many();
            return $res;
        }

        public static function getByRadius($lon, $lat, $radius, $limit = NULL, $offset = NULL) {
            $res = User::getByRadius($lon, $lat, $radius, $limit, $offset);
            $ids = extractKey($res, 'id');
            if (empty($limit)) $limit = 50;
            if (empty($offset)) $offset = 0;
            ORM::set_db(DB::factory('app'), 'app');
            $posts = ORM::for_table('posts', 'app')
                     ->where_in('owner_id', $ids)
                     ->limit($limit)
                     ->offset($limit * $offset)
                     ->order_by_asc('created_at')
                     ->find_many();
            foreach ($res as $row) {
                $row->posts = [];
                foreach ($posts as $post) {
                    if ($post->owner_id == $row->id) {
                        $row->posts[] = (object)$post->as_array();
                    }
                }
            }
            return $res;
        }

        public static function getByTags($tags_id, $limit = NULL, $offset = NULL) {
            ORM::set_db(DB::factory('app'), 'app');
            if (empty($limit)) $limit = 50;
            if (empty($offset)) $offset = 0;
            foreach ($tags_id as $key => $value) {
                $tags_id[$key] = (int)$value;
            }
            $res = ORM::for_table('posts', 'app')
                   ->select('posts.*')
                   ->select_expr('GROUP_CONCAT(tag_id)', 'tags')
                   ->select_expr('COUNT(id)', 'nb_tags')
                   ->left_outer_join(APP__DB_NAME . '.posts_x_tags', 'posts_x_tags.post_id = posts.id')
                   ->where_raw('posts_x_tags.tag_id IN (' . rtrim(implode(', ', $tags_id), ',') . ')')
                   ->group_by('posts.id')
                   ->order_by_desc('nb_tags')
                   ->order_by_desc('created_at')
                   ->limit($limit)
                   ->offset($offset * $limit)
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

        public static function getComments($post_id, $limit = NULL, $offset = NULL) {
            ORM::set_db(DB::factory('app'), 'app');
            if (empty($limit)) $limit = 10;
            if (empty($offset)) $offset = 0;
            $res = ORM::for_table('comments', 'app')
                   ->where('post_id', $post_id)
                   ->limit($limit)
                   ->offset($offset*$limit)
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

        public static function getLikes($post_id) {
            ORM::set_db(DB::factory('app'), 'app');
            $res = ORM::for_table('likes', 'app')
                   ->where('post_id', $post_id)
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

        public static function get($limit = NULL, $offset = NULL) {
            ORM::set_db(DB::factory('app'), 'app');
            if (empty($limit)) $limit = 50;
            if (empty($offset)) $offset = 0;
            $res = ORM::for_table('posts', 'app')
                   ->order_by_asc('created_at')
                   ->limit($limit)
                   ->offset($offset*$limit)
                   ->find_many();
            return $res;
        }

        public static function create($user_id, $post_info) {
            ORM::set_db(DB::factory('app'), 'app');
            $post = ORM::for_table('posts', 'app')->create();
            if (isset($post_info->id)) unset($post_info->id);
            $_pinfo = (array)$post_info;
            foreach ($_pinfo as $key => $value) {
                $post->{$key} = $value;
            }
            $post->owner_id = $user_id;
            $post->save();
            if (!empty($_FILES['upfile'])) {
                Image::upload('post_img', (string)$post->id);
            }
            return $post;
        }

        public static function update($user_id, $post_info) {
            ORM::set_db(DB::factory('app'), 'app');
            $post = ORM::for_table('posts', 'app')->find_one($post_id);
            if (!$post) {
                throw new Exception('Post not found.');
            }
            if ($post->owner_id == $user_id) {
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
                if ($res && $res->owner_id == $user_id) {
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
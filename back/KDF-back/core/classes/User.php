<?php

    class User {

        private $orm;               // Corresponding ORM
        private $favorites;         // Favorite tags
        private $friends;           // An array of the user's friend
        private $groups;            // An array of groups the user belongs to
        private $chats;

        /** 
        *   Constructors
        */

        /**
        *   Main constructor. Redirects to other function constructors.
        *
        *   @param mixed id User identifier or request body if user to be created
        */
        public function __construct($id = NULL, $fetch_favorites = FALSE, $fetch_friends = FALSE, $fetch_groups = FALSE) {
            try {
                if (is_object($id)) {
                    $this->from_request($id);
                } else if (is_numeric($id)) {
                    $this->from_id($id);
                } else if (is_string($id)) {
                    if (filter_var($id, FILTER_VALIDATE_EMAIL)) {
                        $this->from_email($id);
                    } else {
                        $this->from_username($id);
                    }
                }

                if ($fetch_favorites) {
                    $this->favorites = self::getFavorites($this->orm->id);
                }
                
                if ($fetch_friends) {
                    $this->friends = self::getFriendships($this->orm->id);
                }

                if ($fetch_groups) {
                    $this->groups = self::getGroups($this->orm->id);
                }
            } catch (Exception $e) {
                throw $e;
            }
        }

        /**
        *   New user constructor. Gets informations from the $_REQUEST array.
        */
        private function from_request($user_info) {
            if (empty($user_info->uname) || empty($user_info->mail) || empty($user_info->password) || empty($user_info->fname) || empty($user_info->lname)) {
                throw new Exception("Missing parameter.");
            } else {
                self::exists($user_info->mail);
                if ($this->orm) {
                    throw new Exception("User with specified mail already exists.");
                } else {
                    ORM::set_db(DB::factory('users'), 'users');
                    $this->orm = ORM::for_table('users', 'users')->create();
                    $this->orm->uname = $user_info->uname;
                    $this->orm->mail = $user_info->mail;
                    $this->orm->password = password_hash($user_info->password, PASSWORD_BCRYPT);
                    $this->orm->fname = $user_info->fname;
                    $this->orm->lname = $user_info->lname;
                    $this->orm->reg_date = ($user_info->regdate ? $user_info->regdate : date("Y-m-d H:i:s"));
                    $this->orm->status = ($user_info->status ? $user_info->status : 0);
                    $this->orm->address = ($user_info->address ? $user_info->address : NULL);
                    $this->orm->postal_code = ($user_info->postal_code ? $user_info->postal_code : 0);
                    $this->orm->lon = ($user_info->lon ? $user_info->lon : 0);
                    $this->orm->lat = ($user_info->lat ? $user_info->lat : 0);
                    $this->orm->gender = ($user_info->gender ? $user_info->gender : 0);
                    $this->orm->dob = ($user_info->dob ? $user_info->dob : 0);
                    $this->orm->avatar = ($user_info->avatar ? $user_info->avatar : NULL);
                    $this->orm->lang = ($user_info->lang ? $user_info->lang : 'FR');
                    $this->orm->last_seen = date("Y-m-d H:i:s");
                }
            }
        }

        /**
        *   Creates user instance from database. Look up is done by id.
        *
        *   @param integer id
        */
        private function from_id($id = 0) {
            ORM::set_db(DB::factory('users'), 'users');
            $this->orm = ORM::for_table('users', 'users')->find_one($id);
            if (!$this->orm) {
                throw new Exception("Could not find user.");
            }
            
        }

        /**
        *   Creates user instance from database. Look up is done by username.
        *
        *   @param string uname
        */
        private function from_username($uname = "") {
            ORM::set_db(DB::factory('users'), 'users');
            $this->orm = ORM::for_table('users', 'users')->where_raw('LOWER(`uname`) = ?', strtolower($uname))->find_one();
            if (!$this->orm) {
                throw new Exception("Could not find user.");
            }
        }

        /**
        *   Creates user instance from database. Look up is done by email.
        */
        private function from_email($mail = "") {
            ORM::set_db(DB::factory('users'), 'users');
            $this->orm = ORM::for_table('users', 'users')->where('mail', $mail)->find_one();
            if (!$this->orm) {
                throw new Exception("Could not find user.");
            }
        }

        /**
        *  End of Constructors
        */

        /**
        *
        */
        public function show() {
            $that = $this->orm->as_array();
            unset($that["mail"]);
            unset($that["password"]);
            $that["favorites"] = $this->favorites;
            $that["friends"] = $this->friends;
            $that["groups"] = $this->groups;
            return (object)$that;
        }

        private function checkPassword($pass) {
            return password_verify($pass, $this->orm->password);
        }

        public function __get($key) {
            return $this->orm->{$key};
        }

        public function __set($key, $value) {
            $this->orm->{$key} = $value;
        }

        /**
        *
        */
        public static function passwordReinit($id = NULL) {
            if (!empty($id)) {
                $user = new User($id);
                $pass = passwordGeneration();
                $hashed_pass = password_hash($pass, PASSWORD_BCRYPT);
                $user->password = $hashed_pass;
                $user->save();
                Mail::sendReinit($user->mail, $pass);
            }            
        }

        public function save() {
            $this->orm->save();
        }

        public static function getByRegion($postal_code, $strict = 0) {
            ORM::set_db(DB::factory('users'), 'users');
            switch ($strict) {
                case 1:
                    $_code = (int)$postal_code;
                    $_code = $_code - $code % 100;
                    $res = ORM::for_table('users', 'users')
                            ->where_gte('postal_code', $_code)
                            ->where_lte('postal_code', $_code + 99)
                            ->find_many();
                break;
                case 2:
                    $res = ORM::for_table('users', 'users')
                        ->where('postal_code', $postal_code)
                        ->find_many();
                break;
                default:
                    $_code = (int)$postal_code;
                    $_code = $_code - $code % 1000;
                    $res = ORM::for_table('users', 'users')
                            ->where_gte('postal_code', $_code)
                            ->where_lte('postal_code', $_code + 999)
                            ->find_many();
            }
            return $res;
        }

        public static function getByRadius($lon, $lat, $radius, $limit = NULL, $offset = NULL) {
            ORM::set_db(DB::factory('users'), 'users');
            if (empty($limit)) $limit = 50;
            if (empty($offset)) $offset = 0;
            ORM::raw_execute('CALL geodist(' . $lon .', '.$lat.', '.$radius.', '.$limit.', '.$offset.');', NULL, 'users');
            $res = ORM::get_last_statement();
            $rows = [];
            while ($row = $res->fetch(PDO::FETCH_ASSOC)) {
                $rows[] = (object)$row;
            }
            return $rows;
        }

        /**
        *   Checks if a user exists.
        *
        *   @param mixed id
        */
        public static function exists($id = NULL) {
            ORM::set_db(DB::factory('users'), 'users');
            if (!empty($id)) {
                if (is_numeric($id)) {
                    return ORM::for_table('users', 'users')->find_one($id) !== FALSE;
                } else if (is_string($id)) {
                    if (filter_var($id, FILTER_VALIDATE_EMAIL)) {
                        return ORM::for_table('users', 'users')->where('mail', $id)->find_one() !== FALSE;
                    } else {
                        return ORM::for_table('users', 'users')->where('uname', $id)->find_one() !== FALSE;
                    }
                }
            }            
            return FALSE;
        }

        /**
        *  Favorites
        */

        /**
        *
        *
        */
        public static function getFavorites($id) {
            ORM::set_db(DB::factory('app'), 'app');
            $res = ORM::for_table('tags', 'app')
                        ->select('tags.*')
                        ->left_outer_join('favorites', 'favorites.tag_id = tags.id')
                        ->where('favorites.user_id', $id)
                        ->find_many();
            return $res;
        }

        public static function addFavorites($user_id, $tids) {
            $db = DB::factory('app');
            $stmt = $db->prepare('INSERT INTO favorites (user_id, tag_id) VALUES (' . $user_id . ', :tid);');
            if (is_array($tids)) {
                foreach ($tids as $tag_id) {
                    try {
                        $stmt->execute([":tid" => $tag_id]);
                    } catch (Exception $e) {
                        if (strpos($e->getMessage(), "Duplicate entry") == FALSE) {
                            throw $e;
                        }
                    }
                }
                return TRUE;
            } else if (!empty($tids)) {
                try {
                    $stmt->execute([":tid" => $tids]);
                } catch (Exception $e) {
                    if (strpos($e->getMessage(), "Duplicate entry") == FALSE) {
                        throw $e;
                    }
                }
                return TRUE;
            } else {
                return FALSE;
            }
        }

        public static function updateFavorites($user_id, $toAdd, $toDel) {
            $res = TRUE;
            if (!empty($toAdd)) {
                $res = $res && self::addFavorites($user_id, $toAdd);
            }
            if (!empty($toDel)) {
                $res = $res && self::deleteFavorites($user_id, $toDel);
            }
            return $res;
        }

        public static function deleteFavorites($user_id, $tids) {
            $db = DB::factory('app');
            $stmt = $db->prepare('DELETE FROM favorites WHERE (user_id = ' . $user_id . ' AND tag_id = :tid);');
            if (is_array($tids)) {
                foreach ($tids as $tag_id) {
                    try {
                        $stmt->execute([":tid" => $tag_id]);
                    } catch (Exception $e) {
                        if (strpos($e->getMessage(), "Duplicate entry") == FALSE) {
                            throw $e;
                        }
                    }
                }
                return TRUE;
            } else if (!empty($tids)) {
                try {
                    $stmt->execute([":tid" => $tids]);
                } catch (Exception $e) {
                    if (strpos($e->getMessage(), "Duplicate entry") == FALSE) {
                        throw $e;
                    }
                }
                return TRUE;
            } else {
                return FALSE;
            }
        }

        /**
        *  End of Favorites
        */

        /**
        *  Groups
        */

        public static function getGroups($id) {
            ORM::set_db(DB::factory('app'), 'app');
            $res = ORM::for_table('groups', 'app')
                        ->select('groups.id', 'id')
                        ->select('groups.name', 'name')
                        ->select('users_x_groups.status', 'status')
                        ->left_outer_join('users_x_groups', 'groups.id = users_x_groups.group_id')
                        ->where('users_x_groups.user_id', $id)
                        ->where('groups.visibility', 1)
                        ->find_many();
            return $res;
        }

        public static function addToGroup($id, $user_id, $group_id, $status = 0) {
            if (Group::isAdmin($id, $group_id)) {
                ORM::set_db(DB::factory('app'), 'app');
                $relation = ORM::for_table('users_x_groups', 'app')->create();
                $relation->set([
                    'user_id' => $user_id,
                    'group_id' => $group_id,
                    'status' => $status
                ]);
                $relation->save();
                return $relation;
            }
            return FALSE;
        }

        public static function updateOnGroup($id, $user_id, $group_id, $status) {
            if (Group::isAdmin($id, $group_id)) {
                ORM::set_db(DB::factory('app'), 'app');
                $relation = ORM::for_table('users_x_groups', 'app')
                            ->use_id_column('user_id')
                            ->where_id_is($user_id)
                            ->where('group_id', $group_id)
                            ->find_one();
                if (!$relation) {
                    throw new Exception("User does not belong in group.");
                }
                $relation->status = $status;
                $relation->save();
                return $relation;
            }
            return FALSE;
        }

        public static function removeFromGroup($id, $user_id, $group_id) {
            if ($id == $user_id || Group::isAdmin($id, $group_id)) {
                ORM::set_db(DB::factory('app'), 'app');
                $relation = ORM::for_table('users_x_groups', 'app')
                            ->use_id_column('user_id')
                            ->where_id_is($user_id)
                            ->where('group_id', $group_id)
                            ->find_one();
                if (!$relation) {
                    throw new Exception("User does not belong in group.");
                }
                $relation->delete();
                return TRUE;
            }
            return FALSE;
        }

        /**
        * End of Groups
        */

        /**
        *  Friends
        */
        public static function getFriendships($id) {
            ORM::set_db(DB::factory('users'), 'users');
            $res = ORM::for_table('users', 'users')
                        ->select_many(['users.id', 'users.uname'])
                        ->left_outer_join(APP__DB_NAME . '.friends', 'users.id = friends.friend_id')
                        ->where('friends.user_id', $id)
                        ->find_many();
            return $res;
        }

        public static function addFriendship($user_id, $friend_id) {
            ORM::set_db(DB::factory('app'), 'app');
            $relation1 = ORM::for_table('friends', 'app')->create();
            $relation1->set([
                'user_id' => $user_id,
                'friend_id' => $friend_id,
                'status' => 1
            ]);
            $relation2 = ORM::for_table('friends', 'app')->create();
            $relation2->set([
                'user_id' => $friend_id,
                'friend_id' => $user_id,
                'status' => 0
            ]);
            try {
                $relation1->save();
                $relation2->save();
            } catch (Exception $e) {
                throw $e;
            }
            return [$relation1, $relation2];
        }

        public static function updateFriendship($user_id, $friend_id, $status) {
            ORM::set_db(DB::factory('app'), 'app');
            $relation1 = ORM::for_table('friends', 'app')
                        ->use_id_column('user_id')
                        ->where_id_is($user_id)
                        ->where('friend_id', $friend_id)
                        ->find_one();
            $relation2 = ORM::for_table('friends', 'app')
                        ->use_id_column('user_id')
                        ->where_id_is($friend_id)
                        ->where('friend_id', $user_id)
                        ->find_one();
            if (!$relation1 || !$relation2) {
                throw new Exception("No friendship found between specified users.");
            }
            $relation1->status = $status;
            $relation2->status = $status;
            try {
                $relation1->save();
                $relation2->save();
            } catch (Exception $e) {
                throw $e;
            }
            return [$relation1, $relation2];
        }

        public static function deleteFriendship($user_id, $friend_id) {
            ORM::set_db(DB::factory('app'), 'app');
            $relation1 = ORM::for_table('friends', 'app')
                        ->use_id_column('user_id')
                        ->where_id_is($user_id)
                        ->where('friend_id', $friend_id)
                        ->find_one();
            $relation2 = ORM::for_table('friends', 'app')
                        ->use_id_column('user_id')
                        ->where_id_is($friend_id)
                        ->where('friend_id', $user_id)
                        ->find_one();
            if (!$relation1 || !$relation2) {
                throw new Exception("No friendship found between specified users.");
            }
            $relation1->delete();
            $relation2->delete();
            return TRUE;
        }

        /**
        *  End of Friends
        */

        /**
        *   Chats
        */

        public static function getChats($id, $limit = 20, $offset = 0) {
            ORM::set_db(DB::factory('app'), 'app');
            $res = ORM::for_table('chats', 'app')
                   ->select('id', 'chat_id')
                   ->select('user_id2', 'receiver_id')
                   ->select('last_active')
                   ->where('user_id1', $id)
                   ->limit($limit)
                   ->offset($offset)
                   ->find_many();
            return $res;
        }
        /**
        *   End of Chats
        */

        /**
        *   Users
        */
        public static function get($id, $show_favs = FALSE, $show_friends = FALSE, $show_groups = FALSE) {
            $user = new User($id, $show_favs, $show_friends, $show_groups);
            return $user->show();
        }

        public static function create($user_info) {
            $user = new User($user_info);
            $user->save();
            Mail::sendValidation($user->mail);
            return TRUE;
        }

        public static function update($identifier, $user_info) {
            if (!empty($user_info->id) && $identifier == $user_info->id) {
                $user = new User($identifier);
                if (!$user) {
                    throw new Exception("User not found.");
                }
                $_uinfo = (array)$user_info;
                foreach ($_uinfo as $key => $value) {
                    if (property_exists($user, $key)) {
                        $user->{$key} = mysqli_real_escape_string($value);
                    }
                }
                $user->save();
                return TRUE;
            }
            return FALSE;
        }

        public static function remove($identifier, $user_id) {
            if (!empty($user_id) && $identifier == $user_id) {
                $user = new User($identifier);
                if (!$user) {
                    throw new Exception("User not found.");
                }
                $user->delete();
                return TRUE;
            }
            return FALSE;
        }

        public static function login($request_body) {
            $user = new User($request_body->id);
            if ($user->checkPassword($request_body->password)) {
                $var_fields = [
                    "iat" => time(),
                    "id" => $user->id,
                ];
                return encodeJWT($var_fields);
            } else {
                throw new Exception("Error authentifying.");
            }
        }
    }
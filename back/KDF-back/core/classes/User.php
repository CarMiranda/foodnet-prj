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
        *   @param mixed id User identifier or empty if user to be created
        */
        public function __construct($id = NULL, $fetch_favorites = FALSE, $fetch_friends = FALSE, $fetch_groups = FALSE) {
            try {
                if (empty($id)) {
                    $this->from_request();
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
        private function from_request() {
            if (empty($_REQUEST['uname']) || empty($_REQUEST['mail']) || empty($_REQUEST['password']) || empty($_REQUEST['fname']) || empty($_REQUEST['lname'])) {
                throw new Exception();
            } else {
                $this->from_email($_REQUEST['mail']);
                if (!$this->orm) {
                    throw new Exception("User with specified mail already exists.");
                } else {
                    $this->orm->create();
                    $this->orm->uname = mysqli_real_escape_string($_REQUEST['uname']);
                    $this->orm->mail = mysqli_real_escape_string($_REQUEST['mail']);
                    $this->orm->password = password_hash($_REQUEST['password'], PASSWORD_BCRYPT);
                    $this->orm->reg_date = (isset($_REQUEST['regdate']) ? mysqli_real_escape_string($_REQUEST['regdate']) : time());
                    $this->orm->status = (isset($_REQUEST['status']) ? mysqli_real_escape_string($_REQUEST['status']) : NULL);
                    $this->orm->fname = mysqli_real_escape_string($_REQUEST['fname']);
                    $this->orm->address = mysqli_real_escape_string($_REQUEST["address"]);
                    $this->orm->code_postal = mysqli_real_escape_string($_REQUEST["code_postal"]);
                    $this->orm->lon = mysqli_real_escape_string($_REQUEST["lon"]);
                    $this->orm->lat = mysqli_real_escape_string($_REQUEST["lat"]);
                    $this->orm->lname = mysqli_real_escape_string($_REQUEST['lname']);
                    $this->orm->gender = (isset($_REQUEST['gender']) ? mysqli_real_escape_string($_REQUEST['gender']) : NULL);
                    $this->orm->dob = (isset($_REQUEST['dob']) ? mysqli_real_escape_string($_REQUEST['dob']) : NULL);
                    $this->orm->avatar = (isset($_REQUEST['avatar']) ? mysqli_real_escape_string($_REQUEST['avatar']) : NULL);
                    $this->orm->lang = (isset($_REQUEST['lang']) ? mysqli_real_escape_string($_REQUEST['lang']) : NULL);
                    $this->orm->last_seen = time();
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
            $this->orm = ORM::for_table('users', 'users')->where('uname', $uname)->find_one();
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
                        ->select('tags.id', 'id')
                        ->select('tags.name', 'name')
                        ->left_outer_join('favorites', 'favorites.tid = tags.id')
                        ->where('favorites.uid', $id)
                        ->find_many();
            $favs = [];
            foreach ($res as $row) {
                $favs[] = (object)$row->as_array();
            }
            return $favs;
        }

        public static function addFavorites($uid, $tids) {
            $db = DB::factory('app');
            $stmt = $db->prepare('INSERT INTO favorites (uid, tid) VALUES (' . $uid . ', :tid);');
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

        public static function updateFavorites($uid, $toAdd, $toDel) {
            $res = TRUE;
            if (!empty($toAdd)) {
                $res = $res && self::addFavorites($uid, $toAdd);
            }
            if (!empty($toDel)) {
                $res = $res && self::deleteFavorites($uid, $toDel);
            }
            return $res;
        }

        public static function deleteFavorites($uid, $tids) {
            $db = DB::factory('app');
            $stmt = $db->prepare('DELETE FROM favorites WHERE (uid = ' . $uid . ' AND tid = :tid);');
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
                        ->left_outer_join('users_x_groups', 'groups.id = users_x_groups.gid')
                        ->where('users_x_groups.uid', $id)
                        ->where('visibility', 1)
                        ->find_many();
            $groups = [];
            foreach ($res as $row) {
                $groups[] = (object)$row->as_array();
            }
            return $groups;
        }

        public static function addToGroup($id, $uid, $gid, $status = 0) {
            if (Group::isAdmin($id, $gid)) {
                ORM::set_db(DB::factory('app'), 'app');
                $relation = ORM::for_table('users_x_groups', 'app')->create();
                $relation->set([
                    'uid' => $uid,
                    'gid' => $gid,
                    'status' => $status
                ]);
                $relation->save();
                return TRUE;
            }
            return FALSE;
        }

        public static function updateOnGroup($id, $uid, $gid, $status) {
            if (Group::isAdmin($id, $gid)) {
                ORM::set_db(DB::factory('app'), 'app');
                $relation = ORM::for_table('users_x_groups', 'app')
                            ->use_id_column('uid')
                            ->where_id_is($uid)
                            ->where('gid', $gid)
                            ->find_one();
                if (!$relation) {
                    throw new Exception("User does not belong in group.");
                }
                $relation->status = $status;
                $relation->save();
                return TRUE;
            }
            return FALSE;
        }

        public static function removeFromGroup($id, $uid, $gid) {
            if ($id == $uid || Group::isAdmin($id, $gid)) {
                ORM::set_db(DB::factory('app'), 'app');
                $relation = ORM::for_table('users_x_groups', 'app')
                            ->use_id_column('uid')
                            ->where_id_is($uid)
                            ->where('gid', $gid)
                            ->find_one();
                if (!$relation) {
                    throw new Exception("User does not belong in group.");
                }
                $relation->delete();
            }
            return false;
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
                        ->left_outer_join(APP__DB_NAME . '.friends', 'users.id = friends.fid')
                        ->where('friends.uid', $id)
                        ->find_many();
            $friends = [];
            foreach ($res as $row) {
                $friends[] = (object)$row->as_array();
            }
            return $friends;
        }

        public static function addFriendship($uid, $fid) {
            ORM::set_db(DB::factory('app'), 'app');
            $relation1 = ORM::for_table('friends', 'app')->create();
            $relation1->set([
                'uid' => $uid,
                'fid' => $fid,
                'status' => 1
            ]);
            $relation2 = ORM::for_table('friends', 'app')->create();
            $relation2->set([
                'uid' => $fid,
                'fid' => $uid,
                'status' => 0
            ]);
            try {
                $relation1->save();
                $relation2->save();
            } catch (Exception $e) {
                throw $e;
            }
            return TRUE;
        }

        public static function updateFriendship($uid, $fid, $status) {
            ORM::set_db(DB::factory('app'), 'app');
            $relation1 = ORM::for_table('friends', 'app')
                        ->use_id_column('uid')
                        ->where_id_is($uid)
                        ->where('fid', $fid)
                        ->find_one();
            $relation2 = ORM::for_table('friends', 'app')
                        ->use_id_column('uid')
                        ->where_id_is($fid)
                        ->where('fid', $uid)
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
            return TRUE;
        }

        public static function deleteFriendship($uid, $fid) {
            ORM::set_db(DB::factory('app'), 'app');
            $relation1 = ORM::for_table('friends', 'app')
                        ->use_id_column('uid')
                        ->where_id_is($uid)
                        ->where('fid', $fid)
                        ->find_one();
            $relation2 = ORM::for_table('friends', 'app')
                        ->use_id_column('uid')
                        ->where_id_is($fid)
                        ->where('fid', $uid)
                        ->find_one();
            if (!$relation1 || !$relation2) {
                throw new Exception("No friendship found between specified users.");
            }
            $relation1->delete();
            $relation2->delete();
        }

        /**
        *  End of Friends
        */

        /**
        *   Chats
        */

        public static function getChats($id) {
            ORM::set_db(DB::factory('users'), 'users');
            $res = ORM::for_table("chats")
                   ->select('id', 'chat_id')
                   ->select('uid2', 'receiver_id')
                   ->where('uid1', $id)
                   ->find_many();
            $chats = [];
            foreach ($res as $row) {
                $chats[] = (object)$row->as_array();
            }
            return $chats;
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

        public static function create() {
            $user = new User();
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

        public static function remove($identifier, $uid) {
            if (!empty($uid) && $identifier == $uid) {
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
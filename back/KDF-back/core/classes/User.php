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
                if (emtpy($id)) {
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
                echo $e->getMessage();
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
                if (!empty($this->orm->id)) {
                    throw new Exception("User with specified mail already exists.");
                } else {
                    $this->orm->id = (isset($_REQUEST['id']) ? $_REQUEST['id'] : NULL);
                    $this->orm->uname = $_REQUEST['uname'];
                    $this->orm->mail = $_REQUEST['mail'];
                    $this->orm->password = password_hash($_REQUEST['password'], PASSWORD_BCRYPT);
                    $this->orm->reg_date = (isset($_REQUEST['regdate']) ? $_REQUEST['regdate'] : time());
                    $this->orm->status = (isset($_REQUEST['status']) ? $_REQUEST['status'] : NULL);
                    $this->orm->fname = $_REQUEST['fname'];
                    $this->orm->address = $_REQUEST["address"];
                    $this->orm->code_postal = $_REQUEST["code_postal"];
                    $this->orm->lon = $_REQUEST["lon"];
                    $this->orm->lat = $_REQUEST["lat"];
                    $this->orm->lname = $_REQUEST['lname'];
                    $this->orm->gender = (isset($_REQUEST['gender']) ? $_REQUEST['gender'] : NULL);
                    $this->orm->dob = (isset($_REQUEST['dob']) ? $_REQUEST['dob'] : NULL);
                    $this->orm->avatar = (isset($_REQUEST['avatar']) ? $_REQUEST['avatar'] : NULL);
                    $this->orm->lang = (isset($_REQUEST['lang']) ? $_REQUEST['lang'] : NULL);
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
        }

        /**
        *   Creates user instance from database. Look up is done by username.
        *
        *   @param string uname
        */
        private function from_username($uname = "") {
            ORM::set_db(DB::factory('users'), 'users');
            $this->orm = ORM::for_table('users', 'users')->where('uname', $uname)->find_one();
        }

        /**
        *   Creates user instance from database. Look up is done by email.
        */
        private function from_email($mail = "") {
            ORM::set_db(DB::factory('users'), 'users');
            $this->orm = ORM::for_table('users', 'users')->where('mail', $mail)->find_one();
        }

        /**
        *  End of Constructors
        */

        /**
        *
        */
        public function show() {
            $that = clone $this;
            unset($that->orm->mail);
            unset($that->orm->password);
            return json_encode($that);
        }

        private function checkPassword($pass) {
            return password_verify($pass, $this->orm->password);
        }

        public static function login($id, $request_body) {
            $user = new User($id);
            if ($user || $user->checkPassword($request_body->password)) {
                $var_fields = [
                    "iat" => time(),
                    "id" => $user->id,
                ];
                return encodeJWT([]);
            }
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
        public static function passwordReinit($mail = NULL) {
            if (!empty($mail)) {
                $user = new User($mail);
                $pass = passwordGeneration();
                $hashed_pass = password_hash($pass, PASSWORD_BCRYPT);
                $user->password = $hashed_pass;
                $user->save();
                sendReinitMail($mail, $pass);
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
                        ->select_many(['tags.id', 'tags.name'])
                        ->left_outer_join('favorites', 'favorites.tid = tags.id')
                        ->where('favorites.uid', $id)
                        ->find_many();
            return ORM2Array($res, ['id', 'name']);
        }

        public static function addFavorites($uid, $tids) {
            $db = DB::factory('app');
            $db->prepare('INSERT INTO favorites (uid, tid) VALUES (' . $uid . ', :tid);');
            if (is_array($tids)) {
                foreach ($tids as $tag_id) {
                    $db->execute([":tid" => $tag_id]);
                }
                return TRUE;
            } else if (!empty($tids)) {
                $db->execute([":tid" => $tids]);
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
            $db->prepare('DELETE FROM favorites WHERE (uid = ' . $uid . ' AND tid = :tid);');
            if (is_array($tids)) {
                foreach ($tids as $tag_id) {
                    $db->execute([":tid" => $tag_id]);
                }
                return TRUE;
            } else if (!empty($tids)) {
                $db->execute([":tid" => $tids]);
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
                        ->select_many(['groups.id', 'groups.name'])
                        ->left_outer_join('users_x_groups', 'groups.id = users_x_groups.gid')
                        ->where('users_x_groups.uid', $id)
                        ->find_many();
            $groups = ORM2Array($res, ['id', 'name']);
            return $groups;
        }

        public static function addToGroup($id, $uid, $gid, $status = 0) {
            if (Group::isAdmin($id, $gid)) {
                $db = DB::factory('app');
                return $db->query('INSERT INTO users_x_groups (uid, gid, status) VALUES (' . $uid . ', ' . $gid . ', ' . $status . ');');
            }
            return false;
        }

        public static function updateOnGroup($id, $uid, $gid, $status) {
            if (Group::isAdmin($id, $gid)) {
                $db = DB::factory('app');
                return $db->query('UPDATE users_x_groups SET status = ' . $status . ' WHERE (uid = ' . $uid . ' AND gid = ' . $gid . ');');
            }
            return false;
        }

        public static function removeFromGroup($id, $uid, $gid) {
            if ($id == $uid || Group::isAdmin($id, $gid)) {
                $db = DB::factory('app');
                return $db->query('DELETE FROM users_x_groups WHERE (uid = ' . $uid . ' AND gid = ' . $gid . ');');
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
            return ORM2Array($res, ['id', 'uname']);
        }

        public static function addFriendship($uid, $fid) {
            $db = DB::factory('app');
            return $db->query('INSERT INTO friends (uid, fid) VALUES (' . $uid . ', ' . $fid .'), (' . $fid . ', ' . $uid .');');
        }

        public static function updateFriendship($uid, $fid, $status) {
            $db = DB::factory('app');
            return $db->query('UPDATE friends SET status = ' . $status . ' WHERE (uid = ' . $uid . ' AND fid = ' . $fid . ') OR (uid = ' . $fid . ' AND fid = ' . $uid . ');');
        }

        public static function deleteFriendship($uid, $fid) {
            $db = DB::factory('app');
            return $db->query('DELETE FROM friends WHERE (uid = ' . $uid . ' AND fid = ' . $fid . ') OR (uid = ' . $fid . ' AND fid = ' . $uid . ');');
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
                   ->select('id, uid2')
                   ->where('uid1', $id)
                   ->find_many();
            return $res;
        }
        /**
        *   End of Chats
        */
    }
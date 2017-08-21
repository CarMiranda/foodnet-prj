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
                if ($id === NULL) {
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
                    $this->favorites = self::getFavorites($this->id);
                }
                
                if ($fetch_friends) {
                    $this->friends = self::getFriendships($this->id);
                }

                if ($fetch_groups) {
                    $this->groups = self::getGroups($this->id);
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
                $this->id = (isset($_REQUEST['id']) ? $_REQUEST['id'] : NULL);
                $this->uname = $_REQUEST['uname'];
                $this->mail = $_REQUEST['mail'];
                $this->password = password_hash($_REQUEST['password'], PASSWORD_BCRYPT);
                $this->reg_date = (isset($_REQUEST['regdate']) ? $_REQUEST['regdate'] : time());
                $this->status = (isset($_REQUEST['status']) ? $_REQUEST['status'] : NULL);
                $this->fname = $_REQUEST['fname'];
                $this->lname = $_REQUEST['lname'];
                $this->gender = (isset($_REQUEST['gender']) ? $_REQUEST['gender'] : NULL);
                $this->dob = (isset($_REQUEST['dob']) ? $_REQUEST['dob'] : NULL);
                $this->avatar = (isset($_REQUEST['avatar']) ? $_REQUEST['avatar'] : NULL);
                $this->lang = (isset($_REQUEST['lang']) ? $_REQUEST['lang'] : NULL);
                $this->last_seen = time();
                $this->auth_method = (isset($_REQUEST['auth_method']) ? $_REQUEST['auth_method'] : NULL);

                if (!empty($auth_method)) {

                }
                $this->fb_token = (isset($_REQUEST['fb_token']) ? $_REQUEST['fb_token'] : NULL);
                $this->gplus_token = (isset($_REQUEST['gplus_token']) ? $_REQUEST['gplus_token'] : NULL);
                $this->lin_token = (isset($_REQUEST['lin_token']) ? $_REQUEST['lin_token'] : NULL);
                $this->insta_token = (isset($_REQUEST['insta_token']) ? $_REQUEST['insta_token'] : NULL);
            }
        }

        /**
        *   Creates user instance from database. Look up is done by id.
        *
        *   @param integer id
        */
        private function from_id($id = 0) {
            ORM::set_db(DB::factory('users'), 'users');
            $user = ORM::for_table('users', 'users')->find_one($id);
            if ($user) {
                $this->from_orm($user);
            }
        }

        /**
        *   Creates user instance from database. Look up is done by username.
        *
        *   @param string uname
        */
        private function from_username($uname = "") {
            ORM::set_db(DB::factory('users'), 'users');
            $user = ORM::for_table('users', 'users')->where('uname', $uname)->find_one();
            if ($user) {
                $this->from_orm($user);
            }
        }

        /**
        *   Creates user instance from database. Look up is done by email.
        */
        private function from_email($mail = "") {
            ORM::set_db(DB::factory('users'), 'users');
            $user = ORM::for_table('users', 'users')->where('mail', $mail)->find_one();
            if ($user) {
                $this->from_orm($user);
            }
        }

        /**
        *   Fills the user instance with an ORM result set
        */
        private function from_orm($user) {
            $this->id = $user->id;
            $this->uname = $user->uname;
            $this->mail = $user->mail;
            $this->password = $user->password;
            $this->reg_date = $user->reg_date;
            $this->status = $user->status;
            $this->fname = $user->fname;
            $this->lname = $user->lname;
            $this->address = $user->address;
            $this->lat = $user->lat;
            $this->lon = $user->lon;
            $this->gender = $user->gender;
            $this->dob = $user->dob;
            $this->avatar = $user->avatar;
            $this->lang = $user->lang;
            $this->last_seen = $user->last_seen;
        }

        /**
        *  End of Constructors
        */

        /**
        *
        */
        public function show() {
            $that = clone $this;
            unset($that->mail);
            unset($that->password);
            unset($that->token);
            return json_encode($that);
        }

        public function checkPassword($pass) {
            return password_verify($pass, $this->password);
        }

        /**
        *
        */
        public static function passwordReinit($id = NULL) {
            if (!empty($id)) {
                $user = new User($id);
                $_mail = $user->mail;
                $_pass = passwordGeneration();
                $_hashed_pass = password_hash($_pass, PASSWORD_BCRYPT);
            }            
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
            $favorites = ORM2Array($res, ['id', 'name']);
            return $favorites;
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
                   ->where_raw('user_id1 = ? OR user_id2 = ?', [$id, $id])
                   ->find_many();
            return $res;
        }
        /**
        *   End of Chats
        */
    }
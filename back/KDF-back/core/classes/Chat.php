<?php

    class Reply {

        /**
        *   Get all replies from chat by id
        *   @return ORMResultSet Array containing the result set (whole chat)
        */
        public static function getAll($chat_id, $requester_id) {
            $res = ORM::for_table('replies', 'app')
                      ->where('chat_id', $chat_id)
                      ->find_many();
            return $res;
        }

        /**
        *
        *
        */
        public static function getLatest($chat_id, $requester_id) {
            ORM::set_db(DB::factory('app'), 'app');
            $chat = ORM::for_table('chats', 'app')->where('user_id1', $requester_id)->find_one($chat_id);
            $replies = ORM::for_table('replies', 'app')
                       ->where_gte('timestamp', $chat->last_active)
                       ->where_raw('deleted_by = 0 OR deleted_by = 2')
                       ->find_many();
            $chat->last_active = date("Y-m-d H:i:s");
            $chat->save();
            return $replies;
        }

        /**
        *
        *
        */
        public static function status($chat_id, $timestamp, $status) {
            ORM::set_db(DB::factory('app'), 'app');
            $reply = ORM::for_table('replies', 'app')
                        ->where('timestamp', $timestamp)
                        ->where_id_is('chat_id')
                        ->find_one($chat_id);
            $reply->status = $status;
            $reply->save();
            return $reply;
        }

        /**
        *   Add a reply to a chat
        *   @return ORM An ORM instance corresponding to the reply inserted
        */
        public static function addReply($chat_id, $sender_id, $timestamp, $body) {
            ORM::set_db(DB::factory('app'), 'app');
            $res = ORM::for_table('replies', 'app')->create();
            if (empty($chat_id)) {
                throw new Exception('No chat_id parameter supplied.');
            } else {
                $chat = ORM::for_table('chats', 'app')->find_one($chat_id);
                if (!$chat) {
                    throw new Exception("Chat with id {$chat_id} does not exist.");
                }
                $res->chat_id = $chat_id;
            }
            if (empty($sender_id)) {
                throw new Exception('No sender_id parameter supplied.');
            } else {
                $res->sender_id = $sender_id;
            }
            $res->timestamp = (empty($timestamp) ? date('Y-m-d H:i:s') : $timestamp);
            $res->body = (empty($body) ? "" : $body);
            $res->save();
            return $res;
        }

        /**
        *
        *
        */
        public static function updateReplies($chat_id, $user_id, $timestamp, $status, $deleted_by) {
            ORM::set_db(DB::factory('app'), 'app');
            $res = ORM::for_table('replies', 'app')->where('timestamp', $timestamp)->find_one($chat_id);
            $res->status = $status;
            $res->deleted_by = $deleted_by;
            $res->save();
            return $res;
        }

        public static function removeReplies() {
            
        }
    }

    class Chat {

        public static function get() {

        }

        public static function create($user_id1, $user_id2) {
            ORM::set_db(DB::factory('app'), 'app');
            $row1 = ORM::for_table('chats', 'app')->create();
            $row1->user_id1 = $user_id1;
            $row1->user_id2 = $user_id2;
            $row1->last_active = date('Y-m-d H:i:s');
            $row1->save();
            $row2 = ORM::for_table('chats', 'app')->create();
            $row2->chat_id = $row1->chat_id;
            $row2->user_id1 = $user_id2;
            $row2->user_id2 = $user_id1;
            $row2->last_active = 0;
            $row2->save();
            return TRUE;
        }

        public static function update() {

        }

        public static function remove() {

        }
    }
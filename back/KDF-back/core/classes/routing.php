<?php

    /**
    *   @desc
    *   Routing for the api. We use the variable $_REQUEST["path"] to get the required resources.
    *   You can check the htaccess file for more details.
    */
    
    define("ALLOWS_PUT", FALSE);
    define("ALLOWS_DELETE", FALSE);
    require_once "Response.php";
    $response = new Response();

    try {
        if (empty(rtrim($_REQUEST["path"], '/'))) {
            // URI is empty. Send a welcome message.
            $response->success("Welcome to KDF back-end API");
        } else {
            // Else, we want to interact with the api ressources
            $method = $_SERVER["REQUEST_METHOD"];
            $accepted_methods = ["GET", "POST"];
            if (ALLOWS_PUT) $accepted_methods[] = "PUT";
            if (ALLOWS_DELETE) $accepted_methods[] = "DELETE";
            if (!in_array($method, $accepted_methods)) {
                // Take care of unwanted requests
                throw new Exception("Unhandled header $method.");
            }
            $ressource = explode('/', rtrim($_REQUEST["path"], '/'));
            // Get version
            if (preg_match("/^v([0-9]+\.?)+$/", $ressource[0])) {
                $version = array_shift($ressource);
            }
            if ($ressource[0] == "users") {
                // We are requesting user ressources
                // Keep user info secure by checking authentication token whichever the user action
                $decoded_jwt = checkAuthToken();
                if ($decoded_jwt) {
                    $identifier = $decoded_jwt[1]->id;
                    switch ($ressource[1]) {
                        case "favorites" :
                            if ($method == "GET") {
                                // Get favorites of specified user
                                if (!empty($_REQUEST["id"])) {
                                    $result = User::getFavorites($_REQUEST["id"]);
                                } else {
                                    $result = User::getFavorites($identifier);
                                }
                                $error_msg = "Error fetching favorites.";
                            } else {
                                $request_body = parseRequestBody();
                                if (empty($request_body)) {
                                    throw new Exception("No tags specified in request.");
                                }
                                if (!empty($request_body->action)) {
                                    $method = $request_body->action;
                                }
                                if ($method == "POST") {
                                    // Create new favorite for specified user
                                    $result = User::addFavorites($identifier, $request_body->add_tags);
                                    $error_msg = "Error adding favorites.";
                                } else if ($method == "PUT") {
                                    // Update favorites (add and remove)
                                    $add_tags = $request_body->add_tags;
                                    $remove_tags = $request_body->remove_tags;
                                    $result = User::updateFavorites($identifier, $request_body->add_tags, $request_body->remove_tags);
                                    $error_msg = "Error updating favorites.";
                                } else if ($method == "DELETE") {
                                    $result = User::deleteFavorites($identifier, $request_body->remove_tags);
                                    $error_msg = "Error deleting favorites.";
                                }
                            }

                            // Send data
                            if ($result) {
                                $response->success($result);
                            } else {
                                throw new Exception($error_msg);
                            }
                        break; // DONE
                        case "groups" : 
                            if ($method == "GET") {
                                // Get groups of specified user
                                if (!empty($_REQUEST["id"])) {
                                    $result = User::getGroups($_REQUEST["id"]);
                                } else {
                                    $result = User::getGroups($identifier);
                                }
                                $error_msg = "Error fetching groups.";
                            } else {
                                $request_body = parseRequestBody();
                                if (empty($request_body)) {
                                    throw new Exception("No groups specified in request.");
                                }
                                if (!empty($request_body->action)) {
                                    $method = $request_body->action;
                                }
                                if ($method == "POST") {
                                    // Add user to specified group
                                    $result = User::addToGroup($identifier, $request_body->add_user, $request_body->group_id, $request_body->is_admin);
                                    $error_msg = "Error adding to group.";
                                } else if ($method == "PUT") {
                                    // Update user status in group
                                    $result = User::updateOnGroup($identifier, $request_body->upd_user, $request_body->group_id, $request_body->is_admin);
                                    $error_msg = "Error updating user status in group.";
                                } else if ($method == "DELETE") {
                                    $result = User::removeFromGroup($identifier, $request_body->rem_user, $request_body->group_id);
                                    $error_msg = "Error adding favorites.";
                                }
                            }

                            // Send data
                            if ($result) {
                                $response->success($result);
                            } else {
                                throw new Exception($error_msg);
                            }
                        break; // DONE
                        case "chats" :
                            if ($method == "GET") {
                                // Get chats of specified user
                                $result = User::getChats($identifier);
                                $error_msg = "Error fetching chats.";
                            }/* else {
                                $request_body = parseRequestBody();
                                if (empty($request_body)) {
                                    throw new Exception("No chat information specified in request.");
                                }
                                if ($method == "POST") {
                                    // Add reply
                                    $result = User::addReply($identifier, $request_body->receiver, $request_body->timestamp, $request_body->message_body);
                                    $error_msg = "Error adding reply.";
                                } else if ($method == "PUT") {
                                    // Update reply status
                                    $result = User::updateReply($identifier, $request_body->sender, $request_body->receiver, $request_body->timestamp, $request_body->status);
                                    $error_msg = "Error updating reply.";
                                } else if ($method == "DELETE") {
                                    // Delete chat reply
                                    $result = User::deleteReply($identifier, $request_body->sender, $request_body->receiver, $request_body->timestamp, $request_body->deleted_by);
                                    $error_msg = "Error deleting reply.";
                                }
                            }*/

                            // Send data
                            if ($result) {
                                $response->success($result);
                            } else {
                                throw new Exception($error_msg);
                            }
                        break; // DONE
                        case "friends" :
                            if ($method == "GET") {
                                // Get friends of specified user
                                if (!empty($_REQUEST["id"])) {
                                    $result = User::getFriendships($_REQUEST["id"]);
                                } else {
                                    $result = User::getFriendships($identifier);
                                }
                                $error_msg = "Error fetching friends.";
                            } else {
                                $request_body = parseRequestBody();
                                if (empty($request_body)) {
                                    throw new Exception("No friendship information specified in request.");
                                }
                                if (!empty($request_body->action)) {
                                    $method = $request_body->action;
                                }
                                if ($method == "POST") {
                                    // Create new friendship relation
                                    $result = User::addFriendship($identifier, $request_body->friend_id);
                                    $error_msg = "Error adding friendship.";
                                } else if ($method == "PUT") {
                                    // Update friendship
                                    $result = User::updateFriendship($identifier, $request_body->friend_id, $request_body->status);
                                    $error_msg = "Error updating friendship.";
                                } else if ($method == "DELETE") {
                                    $result = User::deleteFriendship($identifier, $request_body->friend_id);
                                    $error_msg = "Error removing friendship.";
                                }
                            }

                            // Send data
                            if ($result) {
                                $response->success($result);
                            } else {
                                throw new Exception($error_msg);
                            }
                        break; // DONE
                        default : 
                            if ($method == "GET") {
                                // Get user information
                                if (!empty($_REQUEST["id"])) {
                                    $result = User::get($_REQUEST['id'], $_REQUEST['showFavorites'] == 1, $_REQUEST['showFriends'] == 1, $_REQUEST['showGroups'] == 1);
                                } else {
                                    $result = User::get($identifier, $_REQUEST['showFavorites'] == 1, $_REQUEST['showFriends'] == 1, $_REQUEST['showGroups'] == 1);
                                }
                                $error_msg = "Error fetching user.";
                            } else {
                                $request_body = parseRequestBody();
                                if (empty($request_body)) {
                                    throw new Exception("Empty request body.");
                                }
                                if (!empty($request_body->action)) {
                                    $method = $request_body->action;
                                }
                                if ($method == "PUT") {
                                    // Change user information
                                    $result = User::update($identifier, $request_body->user_info);
                                } else if ($method == "DELETE") {
                                    // Delete user
                                    $result = User::remove($identifier, $request_body->user_id);
                                } else {
                                    throw new Exception("Unhandled user action.");
                                }
                            }

                            // Send data
                            if ($result) {
                                $response->success($result);
                            } else {
                                throw new Exception($error_msg);
                            }
                    }
                } else {
                    if ($method == "POST") {
                        $request_body = parseRequestBody();
                        if ($request_body->login) {
                            $result = User::login($request_body);
                        } else {
                            $result = User::create();
                        }

                        // Send data
                        if ($result) {
                            $response->success($result);
                        } else {
                            throw new Exception($error_msg);
                        }
                    } else if ($method == "PUT") {
                        $request_body = parseRequestBody();
                        if ($request_body->reinit) {
                            $result = User::passwordReinit($request_body->id);
                        }
                    } else {
                        throw new Exception("Bad users request.");
                    }
                }
            } else if ($ressource[0] == "posts") {
                $decoded_jwt = checkAuthToken();
                if (!$decoded_jwt) {
                    throw new Exception("No authorization header.");
                }
                $identifier = $decoded_jwt[1]->id;
                if ($method == "GET") {
                    switch ($ressource[1]) {
                        case "users" :
                            if (!empty($_REQUEST["id"])) {
                                $result = Post::getByUser($_REQUEST["id"], $_REQUEST["limit"]);
                            } else {
                                $result = Post::getByUser($identifier, $_REQUEST["limit"]);
                            }
                            $error_msg = "Error fetching user.";
                        break;
                        case "groups" :
                            $result = Post::getByGroup($_REQUEST["group_id"], $_REQUEST["limit"]);
                        break;
                        case "friends" :
                            $result = Post::getByFriends($identifier, $_REQUEST["limit"]);
                        break;
                        case "location" :
                            if ($_REQUEST["type"] == "region") {
                                $result = Post::getByRegion($_REQUEST["region"], $_REQUEST["limit"]);
                            } else if ($_REQUEST["type"] == "radius") {
                                $result = Post::getByRadius($_REQUEST["lat"], $_REQUEST["lon"], $_REQUEST["radius"], $_REQUEST["limit"]);
                            } else {
                                throw new Exception("Bad request.");
                            }
                        break;
                        case "tags" :
                            $result = Post::getByTags($_REQUEST["tags"], $_REQUEST["limit"]);
                        break;
                        default :
                            $result = Post::get($_REQUEST["limit"]);
                    }
                } else {
                    $request_body = parseRequestBody();
                    if ($method == "POST") {
                        $result = Post::create($request_body);
                        $error_msg = "Error creating post.";
                    } else if ($method == "PUT") {
                        $result = Post::update($request_body);
                        $error_msg = "Error updating post.";
                    } else if ($method == "DELETE") {
                        $result = Post::remove($request_body);
                        $error_msg = "Error creating post.";
                    }
                }

                if ($result) {
                    $response->success($result);
                } else {
                    throw new Exception($error_msg);
                }
            } else if ($ressource[0] == "groups") {
                $decoded_jwt = checkAuthToken();
                if (!$decoded_jwt) {
                    throw new Exception("No authorization header.");
                }
                $identifier = $decoded_jwt->id;
                if ($method == "GET") {
                    // Get specified group information
                    $result = Group::get($_REQUEST["id"]);
                } else {
                    $request_body = parseRequestBody();
                    if (!empty($request_body->action)) {
                        $method = $request_body->action;
                    }
                    if ($method == "POST") {
                        // Create new group
                        $result = Group::create($identifier, $request_body->group_info);
                    } else if ($method == "PUT") {
                        // Update group (name, status)
                        $result = Group::update($identifier, $request_body->group_info);
                    } else if ($method == "DELETE") {
                        // Remove group
                        $result = Group::delete($identifier, $request_body->group_info);
                    }
                }

                if ($result) {
                    $response->success($result);
                } else {
                    throw new Exception($error_msg);
                }
            } else if ($ressource[0] == "chats") {
                $decoded_jwt = checkAuthToken();
                if (!$decoded_jwt) {
                    throw new Exception("No authorization header.");
                }
                if ($method == "GET") {
                    // Get a chat or message by id
                    if (!empty($_REQUEST["receiver_id"])) {
                        if (!empty($_REQUEST["message_timestamp"])) {
                            $result = Chat::getReplyById($identifier, $_REQUEST["receiver_id"], $_REQUEST["message_timestamp"]);
                        } else {
                            $result = Chat::getReplyByUserId($identifier, $_REQUEST["receiver_id"]);
                        }
                    } else {
                        $result = Chat::getSome($_REQUEST["limit"]);
                    }
                } else {
                    $request_body = parseRequestBody();
                    if ($method == "POST") {
                        // Create a new chat
                        $result = Chat::create($identifier, $request_body);
                    } else if ($method == "PUT") {
                        // Update timestamp (last activity)
                        $result = Chat::update($identifier, $request_body);
                    } else if ($method == "DELETE") {
                        // Remove conversation
                        $result = Chat::remove($identifier, $request_body);
                    }
                }

                if ($result) {
                    $response->success($result);
                } else {
                    throw new Exception($error_msg);
                }
            } else if ($ressource[0] == "tags") {
                if ($method == "GET") {
                    // Get tags list
                } else if ($method == "POST") {
                    // Create new tags
                } else if ($method == "PUT") {
                    // Update existing tag
                } else if ($method == "DELETE") {
                    // Remove a tag
                } else {
                    throw new Exception("Unhandled tags action.");
                }
            } else {
                throw new Exception("Bad request. Ressource not found: {$ressource[0]}");
            }
        }
    } catch (Exception $e) {
        $response->exception($e);
    } finally {
        header('Content-type: text/json;charset=UTF-8');
        echo $response->json();
    }
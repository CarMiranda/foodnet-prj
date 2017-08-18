<?php
    
    require_once "/core/classes/Response.php";
    $response = new Response();

    try {
        if (empty(rtrim($_REQUEST["path"], '/'))) {
            $response->sucess();
            $response->setData("Welcome to KDF back-end API");
        } else {
            $method = $_REQUEST["REQUEST_METHOD"];
            $ressource = explode(rtrim($_REQUEST["path"], '/'));
            if (preg_match("/v[0-9]+\/.*/", $ressource[0])) {
                array_shift($ressource);
            }
            if ($ressource[0] == "users") {
                // Keep user info secure by checking authentication token whichever the user action
                $identifier = checkAuthToken();
                switch ($ressource[1]) {
                    case "favorites" :
                        if ($method == "GET") {
                            // Get favorites of specified user
                            $this->setData(User::getFavorites($identifier));
                        } else if ($method == "POST") {
                            // Create new favorite for specified user
                            $this->setData(User::addFavorites($identifier, parsePHP($_REQUEST["tags_id"])));
                        } else if ($method == "PUT") {
                            // Create new favorites and remove
                            $this->setData(User::updateFavorites($identifier, parsePHP($_REQUEST["tags_id"])));
                        } else if ($method == "DELETE") {
                            // Remove favorite
                            $this->setData(User::removeFavorites($identifier, parsePHP($_REQUEST["tags_id"])));
                        } else {
                            throw new Exception("Unhandled user action.");
                        }
                    break;
                    case "groups" : 
                        if ($method == "GET") {
                            // Get groups of specified user
                        } else if ($method == "POST") {
                            // Add a user to a group
                        } else if ($method == "PUT") {
                            // Change a user info for a group (admin)
                        } else if ($method == "DELETE") {
                            // Remove user from group
                        } else {
                            throw new Exception("Unhandled user action.");
                        }
                    break;
                    case "chats" :
                        if ($method == "GET") {
                            // Retrieve conversations for specified user
                        } else if ($method == "POST") {
                            // Nothing
                        } else if ($method == "PUT") {
                            // Nothing
                        } else if ($method == "DELETE") {
                            // Nothing
                        } else {
                            throw new Exception("Unhandled user action.");
                        }
                    break;
                    case "friends" :
                        if ($method == "GET") {
                            // Get all friendship relationships for users
                        } else if ($method == "POST") {
                            // Add new friendship relationship
                        } else if ($method == "PUT") {
                            // Change friendship status
                        } else if ($method == "DELETE") {
                            // Remove friendship
                        } else {
                            throw new Exception("Unhandled user action.");
                        }
                    break;
                    default : 
                        if ($method == "GET") {
                            // Get all user info by id
                        } else if ($method == "POST") {
                            if ($ressource[1] == "login") {
                                // Login
                            } else {
                                // Create user
                            }
                        } else if ($method == "PUT") {
                            // Change user information
                        } else if ($method == "DELETE") {
                            // Delete user
                        } else {
                            throw new Exception("Unhandled user action.");
                        }
                }
            } else if ($ressource[0] == "posts") {
                if ($method == "GET") {

                } else if ($method == "POST") {

                } else if ($method == "PUT") {

                } else if ($method == "DELETE") {

                } else {
                    throw new Exception("Unhandled user action.");
                }
            } else if ($ressource[0] == "groups") {
                if ($method == "GET") {
                    // Get all (visible) groups
                } else if ($method == "POST") {
                    // Create new group
                } else if ($method == "PUT") {
                    // Update group (name, status)
                } else if ($method == "DELETE") {
                    // Remove group
                } else {
                    throw new Exception("Unhandled user action.");
                }
            } else if ($ressource[0] == "chats") {
                if ($method == "GET") {
                    // Get a chat or message by id
                } else if ($method == "POST") {
                    // Create a new chat or reply
                } else if ($method == "PUT") {
                    // Nothing
                } else if ($method == "DELETE") {
                    // Remove conversation
                } else {
                    throw new Exception("Unhandled user action.");
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
                throw new Exception("Bad request.");
            }
        }
    } catch (Exception $e) {
        $response->setException($e);
    } finally {
        $response->json();
    }
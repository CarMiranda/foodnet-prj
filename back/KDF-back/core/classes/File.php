<?php

    class Image extends File {
        public static function upload($ressource_type = NULL, $filename = NULL) {
            $img_mime_types = [
                'jpg' => 'image/jpeg',
                'png' => 'image/png',
                'gif' => 'image/gif',
            ];
            $accepted_ressources = [
                'avatar',
                'post_img',
                'chat_img'
            ];

            if (empty($ressource_type)) {
                $_ressource = 'post_img';
            } else {
                if (!in_array($ressource_type, $accepted_ressources)) {
                    throw new Exception('Invalid ressource type in file upload.');
                } else {
                    $_ressource = $ressource_type;
                }
            }
            if (empty($_filename)) {
                $_filename = date("YmdHis") . '-' . $_ressource;
            } else {
                $_filename = trim($filename);
            }
            parent::upload($_ressource, $img_mime_types, $_filename);
            return $_filename;
        }
    }

    class File {

        public static function download() {
            
        }

        public static function upload($location, $accepted_mime_types, $filename) {
            header('Content-Type: text/plain; charset=utf-8');

            try {
                
                // Undefined | Multiple Files | $_FILES Corruption Attack
                // If this request falls under any of them, treat it invalid.
                if (
                    !isset($_FILES['upfile']['error']) ||
                    is_array($_FILES['upfile']['error'])
                ) {
                    throw new RuntimeException('Invalid parameters.');
                }

                // Check $_FILES['upfile']['error'] value.
                switch ($_FILES['upfile']['error']) {
                    case UPLOAD_ERR_OK:
                        break;
                    case UPLOAD_ERR_NO_FILE:
                        throw new RuntimeException('No file sent.');
                    case UPLOAD_ERR_INI_SIZE:
                    case UPLOAD_ERR_FORM_SIZE:
                        throw new RuntimeException('Exceeded filesize limit.');
                    default:
                        throw new RuntimeException('Unknown errors.');
                }

                // You should also check filesize here. 
                if ($_FILES['upfile']['size'] > 1000000) {
                    throw new RuntimeException('Exceeded filesize limit.');
                }

                // DO NOT TRUST $_FILES['upfile']['mime'] VALUE !!
                // Check MIME Type by yourself.
                $finfo = new finfo(FILEINFO_MIME_TYPE);
                if (false === $ext = array_search(
                    $finfo->file($_FILES['upfile']['tmp_name']),
                    $accepted_mime_types,
                    true
                )) {
                    throw new RuntimeException('Invalid file format.');
                }

                // You should name it uniquely.
                // DO NOT USE $_FILES['upfile']['name'] WITHOUT ANY VALIDATION !!
                // On this example, obtain safe unique name from its binary data.
                $format = './uploads/' . $location . '/%s.%s';
                if (!move_uploaded_file(
                    $_FILES['upfile']['tmp_name'],
                    sprintf($format,
                        $filename,
                        $ext
                    )
                )) {
                    throw new RuntimeException('Failed to move uploaded file.');
                }
            } catch (RuntimeException $e) {
                throw $e;
            }
        }
    }
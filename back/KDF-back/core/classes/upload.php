<?php

    header("Access-Control-Allow-Origin: *");
    $target_path = "/public_html/uploads";
    $target_path = $target_path . basename($_FILES['file']['name']);
    if (move_uploaded_file($_FILES['file']['tmp_name'], $target_path)) {
        echo "Upload and move success.";
    } else {
        echo $target_path;
        echo "Error uploading file.";
    }
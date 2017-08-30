<?php

    class Mail {
        public static function sendValidation($user_mail) {
            $_code = "";
            for ($i = 0; $i < 5; ++$i) {
                $digit = round(rand(0, 9));
                $_code .= $i;
            }
            $html_body = "<div><h1>Code de validation : " . $_code . "</h1></div>";
            $headers =  'From: carlos.miranda_lopez@insa-rouen.fr' . "\r\n" .
                        'Reply-To: carlos.miranda_lopez@insa-rouen.fr' . "\r\n" .
                        'X-Mailer: PHP/' . phpversion();
            mail($user_mail, "Code de validation KooDeFood", $html_body, $headers);
        }
    }
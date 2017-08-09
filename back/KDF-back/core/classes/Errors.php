<?php

    abstract class CustomException extends Exception {
        protected $message = 'Unkown error.';
        private $string;
        protected $code = 0;
        protected $file;
        protected $line;
        private $trace;
        private $previous;

        public function __construct($message = null, $code = 0, Exception $previous = null) {
            parent::__construct();
        }

    }
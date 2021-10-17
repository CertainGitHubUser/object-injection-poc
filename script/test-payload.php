<?php

class Token
{
    public $userData;

    public function login()
    {
        return $this->userData;
    }
}

class HackedObject
{
    public $token;
    public $message;
    public $fileName;

    function __construct($fileName)
    {
        $this->fileName = $fileName;
    }

    function __wakeup()
    {
        is_file($this->fileName);
        echo $this->token->login();
    }

    public function __destruct()
    {
        is_file($this->fileName);
        echo $this->message;
    }
}

$object = new HackedObject($argv[1] . '/any-suffix.any-format');

//file_exists($argv[1] . '/any-suffix.any-format');
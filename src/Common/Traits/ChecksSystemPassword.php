<?php


namespace App\Common\Traits;


trait ChecksSystemPassword
{
    private function checkSystemPassword(string $password)
    {
        if ($password !== getenv('MACHINE_PASSWORD')) {
            throw new \RuntimeException('Incorrect admin password.');
        }
    }
}
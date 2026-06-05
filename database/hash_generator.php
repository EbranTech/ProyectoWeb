<?php
// Use this script to generate the password hashes for your seed.sql
$passwords = [
    'admin' => 'admin123',
    'biblio' => 'biblio123'
];

foreach ($passwords as $user => $pass) {
    echo "$user: " . password_hash($pass, PASSWORD_DEFAULT) . PHP_EOL;
}

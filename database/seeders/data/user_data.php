<?php

return [

        1 => [
            "name" => "David Jumeau",
            "email" => "djumeau@gmail.com",
            "password" => bcrypt("12345678"),
            "role" => "admin",
            "avatar_file" => "user.jpg",
        ],

        2 => [
            "name" => "Christopher Jumeau",
            "email" => "cdfjumeau@gmail.com",
            "password" => bcrypt("12345678"),
            "role" => "guest",
            "avatar_file" => "user.jpg",
        ],

        3 => [
            "name" => "Connie Jumeau",
            "email" => "cjumeau@gmail.com",
            "password" => bcrypt("12345678"),
            "role" => "member",
            "avatar_file" => "user.jpg",
        ],

        4 => [
            "name" => "John Giesbrecht",
            "email" => "johnhgiesbrecht@gmail.com",
            "password" => bcrypt("12345678"),
            "role" => "elder",
            "avatar_file" => "user.jpg",
        ],

];
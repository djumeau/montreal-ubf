<?php

return [

        1 => [
            "name" => "David Jumeau",
            "email" => "djumeau@gmail.com",
            "password" => bcrypt("123456"),
            "privileges" => "admin"
        ],

        2 => [
            "name" => "Christopher Jumeau",
            "email" => "cdfjumeau@gmail.com",
            "password" => bcrypt("123456"),
            "privileges" => "guest"
        ],

        3 => [
            "name" => "Connie Jumeau",
            "email" => "cjumeau@gmail.com",
            "password" => bcrypt("123456"),
            "privileges" => "member"
        ],

        4 => [
            "name" => "John Giesbrecht",
            "email" => "johnhgiesbrecht@gmail.com",
            "password" => bcrypt("123456"),
            "privileges" => "elder"
        ],

];
<?php

return [

    1 => [
        "name" => "Sarah Thompson",
        "email" => "sarah.thompson@example.com",
        "user" => false,
        "inquiry" => "ministry",
        "message" => "Hi, I recently moved to Montreal and I'm curious to learn more about your ministry and what you do in the community.",
    ],

    2 => [
        "name" => "Marc Tremblay",
        "email" => "marc.tremblay@example.com",
        "user" => false,
        "inquiry" => "group_study",
        "message" => "I'd like to join a group Bible study. What days and times are available?",
    ],

    3 => [
        "name" => "Connie Jumeau",
        "email" => "cjumeau@gmail.com",
        "user" => true,
        "inquiry" => "one_to_one",
        "message" => "I'm interested in a one-to-one Bible study with a mentor. Could someone reach out to set this up?",
    ],

    4 => [
        "name" => "Alicia Fontaine",
        "email" => "alicia.fontaine@example.com",
        "user" => false,
        "inquiry" => "worship",
        "message" => "What time does the Sunday worship service start, and is there parking available nearby?",
    ],

    5 => [
        "name" => "John Giesbrecht",
        "email" => "johnhgiesbrecht@gmail.com",
        "user" => true,
        "inquiry" => "pastoral",
        "message" => "I'd like to speak with someone from the pastoral team about a personal matter. Please have someone contact me.",
    ],

    6 => [
        "name" => "David Cho",
        "email" => "david.cho@example.com",
        "user" => false,
        "inquiry" => "other",
        "message" => "I'm a student journalist writing about faith communities in Montreal. Would anyone be open to a short interview?",
    ],

    // 7 and 8 also have a follow-up ("management" => a manage_inquiries row): read, answered, by whom and a note.
    // "answered_by" is the email of the user who answered (see user_data.php); the seeder turns it into the user's id

    7 => [
        "name" => "David Jumeau",
        "email" => "djumeau@gmail.com",
        "user" => true,
        "inquiry" => "worship",
        "message" => "Is the 9h00 Sunday service held in English or in French, and is there a children's program during the service?",
        "management" => [
            "read_at" => "2026-10-01 09:15:00",
            "answered_at" => "2026-10-01 16:40:00",
            "answered_by" => "johnhgiesbrecht@gmail.com",
            "note" => "Replied by email with the service languages and the children's program details.",
        ],
    ],

    8 => [
        "name" => "David Jumeau",
        "email" => "djumeau@gmail.com",
        "user" => true,
        "inquiry" => "subscribe",
        "message" => "I would like to subscribe to the site to follow the online group Bible study. Please let me know what is needed.",
        "management" => [
            "read_at" => "2026-10-02 08:30:00",
            "answered_at" => null,
            "answered_by" => null,
            "note" => "Read, not answered yet. Check which online study he wants to follow before replying.",
        ],
    ],

];

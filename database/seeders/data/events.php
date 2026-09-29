<?php

// Placeholder events. Images are in storage/app/public/images/events/{category}/{start date}/,
// attachments in storage/app/private/documents/events/{category}/{start date}/ (e.g. conference/2026-11-20)
return [

        1 => [
                "title" => "Thanksgiving Fellowship Dinner",
                "images" => [
                        "desktop" => "2026-10_thanksgiving_dinner-desktop.jpg",
                        "mobile" => "2026-10_thanksgiving_dinner-mobile.jpg",
                        "square" => "2026-10_thanksgiving_dinner-square.jpg"],
                "category" => "event",
                "minimum_profile" => "guest",
                "start_date" => "2026-10-10 17:00:00",
                "has_end_date" => true,
                "end_date" => "2026-10-10 20:00:00",
                "recurring" => false,
                "location" => "Montreal UBF, 2627 rue Ryde, Montréal, QC, H3K 1R7",
                "featured_on_home_page" => true,
                "featured_on_events_page" => true,
                "description" => "Join us for a Thanksgiving meal, testimonies and songs of praise. Everyone is welcome, bring a friend!",
                "post_event" => null,

                "attachments" => [
                        ["type" => "document", "document_name" => "thanksgiving_dinner_flyer.pdf"],
                ]
        ],

        2 => [
                "title" => "GBS Online - Gospel of John",
                "images" => [
                        "desktop" => "2026-10_gbs_online_john-desktop.jpg",
                        "mobile" => "2026-10_gbs_online_john-mobile.jpg",
                        "square" => "2026-10_gbs_online_john-square.jpg"],
                "category" => "gbs_online",
                "minimum_profile" => "user",
                "start_date" => "2026-10-07 19:30:00",
                "has_end_date" => false,
                "end_date" => null,
                "recurring" => true,
                "location" => "Zoom (link sent after registration)",
                "featured_on_home_page" => false,
                "featured_on_events_page" => true,
                "description" => "A weekly online group Bible study through the Gospel of John. Question sheets are shared before each meeting.",
                "post_event" => null,

                "attachments" => [
                        ["type" => "document", "document_name" => "gbs_john_schedule.pdf"],
                ]
        ],

        3 => [
                "title" => "Fall Conference 2026",
                "images" => [
                        "desktop" => "2026-11_fall_conference-desktop.jpg",
                        "mobile" => "2026-11_fall_conference-mobile.jpg",
                        "square" => "2026-11_fall_conference-square.jpg"],
                "category" => "conference",
                "minimum_profile" => "guest",
                "start_date" => "2026-11-20 19:00:00",
                "has_end_date" => true,
                "end_date" => "2026-11-22 15:00:00",
                "recurring" => false,
                "location" => "Montreal UBF, 2627 rue Ryde, Montréal, QC, H3K 1R7",
                "featured_on_home_page" => true,
                "featured_on_events_page" => true,
                "description" => "Three days of messages, group Bible studies and fellowship. Meals are provided on Saturday and Sunday.",
                "post_event" => null,

                "attachments" => [
                        ["type" => "document", "document_name" => "fall_conference_schedule.pdf"],
                        ["type" => "media", "document_name" => "fall_conference_poster.png"],
                ]
        ],

];

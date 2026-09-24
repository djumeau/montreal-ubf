<?php

// Attachments are only listed when their file exists in storage/app/private/documents/series_{id}/{locale}/study_{id}/
return [

        1 => [
                "study_series_id" => 1,
                "book_id" => 43,
                "bible_passage" => "1:1-18",
                "title_en" => "In Him Was Life",
                "title_fr" => "En elle il y avait la vie",
                "image_links" => [ // Shared by EN and FR, in images/series_{id}/study_{id}/
                        "square" => "jn_01.01-18-square.jpg",
                        "desktop" => "jn_01.01-18-desktop.jpg",
                        "mobile" => "jn_01.01-18-mobile.jpg"],

                // Pure relational array structures easily ingested by loops
                "attachments" => [
                        ["locale" => "en_CA", "type" => "question_sheet", "filename" => "jn_01.01-18.q", "extension" => "docx"],
                        ["locale" => "en_CA", "type" => "lecture", "filename" => "jn_01.01-18.lec", "extension" => "docx"],
                        ["locale" => "en_CA", "type" => "question_sheet", "filename" => "jn_01.01-18.q", "extension" => "pdf"],
                        ["locale" => "en_CA", "type" => "lecture", "filename" => "jn_01.01-18.lec", "extension" => "pdf"],
                        ["locale" => "fr_CA", "type" => "question_sheet", "filename" => "jn_01.01-18.q.fr", "extension" => "docx"],
                        ["locale" => "fr_CA", "type" => "lecture", "filename" => "jn_01.01-18.lec.fr", "extension" => "docx"],
                        ["locale" => "fr_CA", "type" => "question_sheet", "filename" => "jn_01.01-18.q.fr", "extension" => "pdf"],
                        ["locale" => "fr_CA", "type" => "lecture", "filename" => "jn_01.01-18.lec.fr", "extension" => "pdf"],
                ]
        ],

        2 => [
                "study_series_id" => 1,
                "book_id" => 43,
                "bible_passage" => "1:19-34",
                "title_en" => "John the Baptist Testifies about Jesus",
                "title_fr" => "Jean-Baptiste rend témoignage à Jésus",
                "image_links" => [ // Shared by EN and FR, in images/series_{id}/study_{id}/
                        "square" => "jn_01.19-34-square.jpg",
                        "desktop" => "jn_01.19-34-desktop.jpg",
                        "mobile" => "jn_01.19-34-mobile.jpg"],

                // Pure relational array structures easily ingested by loops
                "attachments" => [
                        ["locale" => "en_CA", "type" => "question_sheet", "filename" => "jn_01.19-34.q", "extension" => "docx"],
                        ["locale" => "en_CA", "type" => "lecture", "filename" => "jn_01.19-34.lec", "extension" => "docx"],
                        ["locale" => "en_CA", "type" => "question_sheet", "filename" => "jn_01.19-34.q", "extension" => "pdf"],
                        ["locale" => "en_CA", "type" => "lecture", "filename" => "jn_01.19-34.lec", "extension" => "pdf"],
                        ["locale" => "fr_CA", "type" => "question_sheet", "filename" => "jn_01.19-34.q.fr", "extension" => "docx"],
                        ["locale" => "fr_CA", "type" => "lecture", "filename" => "jn_01.19-34.lec.fr", "extension" => "docx"],
                        ["locale" => "fr_CA", "type" => "question_sheet", "filename" => "jn_01.19-34.q.fr", "extension" => "pdf"],
                        ["locale" => "fr_CA", "type" => "lecture", "filename" => "jn_01.19-34.lec.fr", "extension" => "pdf"],
                ]
        ],

        3 => [
                "study_series_id" => 1,
                "book_id" => 43,
                "bible_passage" => "1:35-51",
                "title_en" => '"Come, and You Will See"',
                "title_fr" => "« Venez et voyez »",
                "image_links" => [ // Shared by EN and FR, in images/series_{id}/study_{id}/
                        "square" => "jn_01.35-51-square.jpg",
                        "desktop" => "jn_01.35-51-desktop.jpg",
                        "mobile" => "jn_01.35-51-mobile.jpg"],
        ],

        4 => [
                "study_series_id" => 1,
                "book_id" => 43,
                "bible_passage" => "2:1-12",
                "title_en" => "The First Sign of Jesus",
                "title_fr" => "Le premier signe de Jésus",
                "image_links" => [ // Shared by EN and FR, in images/series_{id}/study_{id}/
                        "square" => "jn_02.01-12-square.jpg",
                        "desktop" => "jn_02.01-12-desktop.jpg",
                        "mobile" => "jn_02.01-12-mobile.jpg"],
        ],

        5 => [
                "study_series_id" => 1,
                "book_id" => 43,
                "bible_passage" => "2:13-25",
                "title_en" => "Jesus is the Temple",
                "title_fr" => "Jésus est le temple",
                "image_links" => [ // Shared by EN and FR, in images/series_{id}/study_{id}/
                        "square" => "jn_02.13-25-square.jpg",
                        "desktop" => "jn_02.13-25-desktop.jpg",
                        "mobile" => "jn_02.13-25-mobile.jpg"],
        ],

        6 => [
                "study_series_id" => 1,
                "book_id" => 43,
                "bible_passage" => "3:1-21",
                "title_en" => "For God So Loved the World",
                "title_fr" => "Dieu a tant aimé le monde",
                "image_links" => [ // Shared by EN and FR, in images/series_{id}/study_{id}/
                        "square" => "jn_03.01-21-square.jpg",
                        "desktop" => "jn_03.01-21-desktop.jpg",
                        "mobile" => "jn_03.01-21-mobile.jpg"],
        ],

        7 => [
                "study_series_id" => 1,
                "book_id" => 43,
                "bible_passage" => "3:22-36",
                "title_en" => "Jesus Must Become Greater",
                "title_fr" => "Il faut que Jésus grandisse",
                "image_links" => [ // Shared by EN and FR, in images/series_{id}/study_{id}/
                        "square" => "jn_03.22-36-square.jpg",
                        "desktop" => "jn_03.22-36-desktop.jpg",
                        "mobile" => "jn_03.22-36-mobile.jpg"],
        ],

        // Studies 8-17: added for the existing question sheets; titles and images still to come

        8 => [
                "study_series_id" => 1,
                "book_id" => 43,
                "bible_passage" => "7:53-8:11",
                "attachments" => [
                        ["locale" => "en_CA", "type" => "question_sheet", "filename" => "jn_07.53-08.11.q", "extension" => "docx"],
                        ["locale" => "en_CA", "type" => "question_sheet", "filename" => "jn_07.53-08.11.q", "extension" => "pdf"],
                        ["locale" => "fr_CA", "type" => "question_sheet", "filename" => "jn_07.53-08.11.q.fr", "extension" => "docx"],
                        ["locale" => "fr_CA", "type" => "question_sheet", "filename" => "jn_07.53-08.11.q.fr", "extension" => "pdf"],
                ]
        ],

        9 => [
                "study_series_id" => 1,
                "book_id" => 43,
                "bible_passage" => "8:12-30",
                "attachments" => [
                        ["locale" => "en_CA", "type" => "question_sheet", "filename" => "jn_08.12-30.q", "extension" => "docx"],
                        ["locale" => "en_CA", "type" => "question_sheet", "filename" => "jn_08.12-30.q", "extension" => "pdf"],
                        ["locale" => "fr_CA", "type" => "question_sheet", "filename" => "jn_08.12-30.q.fr", "extension" => "docx"],
                        ["locale" => "fr_CA", "type" => "question_sheet", "filename" => "jn_08.12-30.q.fr", "extension" => "pdf"],
                ]
        ],

        10 => [
                "study_series_id" => 1,
                "book_id" => 43,
                "bible_passage" => "8:31-59",
                "attachments" => [
                        ["locale" => "en_CA", "type" => "question_sheet", "filename" => "jn_08.31-59.q", "extension" => "docx"],
                        ["locale" => "en_CA", "type" => "question_sheet", "filename" => "jn_08.31-59.q", "extension" => "pdf"],
                        ["locale" => "fr_CA", "type" => "question_sheet", "filename" => "jn_08.31-59.q.fr", "extension" => "docx"],
                        ["locale" => "fr_CA", "type" => "question_sheet", "filename" => "jn_08.31-59.q.fr", "extension" => "pdf"],
                ]
        ],

        11 => [
                "study_series_id" => 1,
                "book_id" => 43,
                "bible_passage" => "9:1-12",
                "attachments" => [
                        ["locale" => "en_CA", "type" => "question_sheet", "filename" => "jn_09.01-12.q", "extension" => "docx"],
                        ["locale" => "en_CA", "type" => "question_sheet", "filename" => "jn_09.01-12.q", "extension" => "pdf"],
                        ["locale" => "fr_CA", "type" => "question_sheet", "filename" => "jn_09.01-12.q.fr", "extension" => "docx"],
                        ["locale" => "fr_CA", "type" => "question_sheet", "filename" => "jn_09.01-12.q.fr", "extension" => "pdf"],
                ]
        ],

        12 => [
                "study_series_id" => 1,
                "book_id" => 43,
                "bible_passage" => "9:13-41",
                "attachments" => [
                        ["locale" => "en_CA", "type" => "question_sheet", "filename" => "jn_09.13-41.q", "extension" => "docx"],
                        ["locale" => "en_CA", "type" => "question_sheet", "filename" => "jn_09.13-41.q", "extension" => "pdf"],
                        ["locale" => "fr_CA", "type" => "question_sheet", "filename" => "jn_09.13-41.q.fr", "extension" => "docx"],
                        ["locale" => "fr_CA", "type" => "question_sheet", "filename" => "jn_09.13-41.q.fr", "extension" => "pdf"],
                ]
        ],

        13 => [
                "study_series_id" => 1,
                "book_id" => 43,
                "bible_passage" => "10:1-21",
                "attachments" => [
                        ["locale" => "en_CA", "type" => "question_sheet", "filename" => "jn_10.01-21.q", "extension" => "docx"],
                        ["locale" => "en_CA", "type" => "question_sheet", "filename" => "jn_10.01-21.q", "extension" => "pdf"],
                        ["locale" => "fr_CA", "type" => "question_sheet", "filename" => "jn_10.01-21.q.fr", "extension" => "docx"],
                        ["locale" => "fr_CA", "type" => "question_sheet", "filename" => "jn_10.01-21.q.fr", "extension" => "pdf"],
                ]
        ],

        14 => [
                "study_series_id" => 1,
                "book_id" => 43,
                "bible_passage" => "10:22-42",
                "attachments" => [
                        ["locale" => "en_CA", "type" => "question_sheet", "filename" => "jn_10.22-42.q", "extension" => "docx"],
                        ["locale" => "en_CA", "type" => "question_sheet", "filename" => "jn_10.22-42.q", "extension" => "pdf"],
                        ["locale" => "fr_CA", "type" => "question_sheet", "filename" => "jn_10.22-42.q.fr", "extension" => "docx"],
                        ["locale" => "fr_CA", "type" => "question_sheet", "filename" => "jn_10.22-42.q.fr", "extension" => "pdf"],
                ]
        ],

        15 => [
                "study_series_id" => 1,
                "book_id" => 43,
                "bible_passage" => "11:1-16",
                "attachments" => [
                        ["locale" => "en_CA", "type" => "question_sheet", "filename" => "jn_11.01-16.q", "extension" => "docx"],
                        ["locale" => "en_CA", "type" => "question_sheet", "filename" => "jn_11.01-16.q", "extension" => "pdf"],
                        ["locale" => "fr_CA", "type" => "question_sheet", "filename" => "jn_11.01-16.q.fr", "extension" => "docx"],
                        ["locale" => "fr_CA", "type" => "question_sheet", "filename" => "jn_11.01-16.q.fr", "extension" => "pdf"],
                ]
        ],

        16 => [
                "study_series_id" => 1,
                "book_id" => 43,
                "bible_passage" => "11:17-54",
                "attachments" => [
                        ["locale" => "en_CA", "type" => "question_sheet", "filename" => "jn_11.17-54.q", "extension" => "docx"],
                        ["locale" => "en_CA", "type" => "question_sheet", "filename" => "jn_11.17-54.q", "extension" => "pdf"],
                        ["locale" => "fr_CA", "type" => "question_sheet", "filename" => "jn_11.17-54.q.fr", "extension" => "docx"],
                        ["locale" => "fr_CA", "type" => "question_sheet", "filename" => "jn_11.17-54.q.fr", "extension" => "pdf"],
                ]
        ],

        17 => [
                "study_series_id" => 1,
                "book_id" => 43,
                "bible_passage" => "11:55-12:19",
                "title_en" => "See Your King is Coming!",
                "title_fr" => "Voici ton roi qui vient",
                "attachments" => [
                        ["locale" => "en_CA", "type" => "question_sheet", "filename" => "jn_11.55-12.19.q", "extension" => "docx"],
                        ["locale" => "en_CA", "type" => "question_sheet", "filename" => "jn_11.55-12.19.q", "extension" => "pdf"],
                        ["locale" => "fr_CA", "type" => "question_sheet", "filename" => "jn_11.55-12.19.q.fr", "extension" => "docx"],
                        ["locale" => "fr_CA", "type" => "question_sheet", "filename" => "jn_11.55-12.19.q.fr", "extension" => "pdf"],
                ]
        ],

];

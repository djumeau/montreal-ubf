<?php

// Placeholder events. Images are in storage/app/public/images/events/{category}/{start date}/,
// attachments in storage/app/private/documents/events/{category}/{start date}
// Documents have a locale (en_CA or fr_CA) and only show in that language; media have none and show in both
return [

        1 => [
                "title_en" => "GBS Online - Gospel of John",
                "title_fr" => "EBG en ligne - Évangile de Jean",
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
                "contact_name" => null,
                "contact_email" => null,
                "website_url" => null,
                "featured_on_home_page" => false,
                "featured_on_events_page" => true,
                "description_en" => "A weekly online group Bible study through the Gospel of John. Question sheets are shared before each meeting.",
                "description_fr" => "Une étude biblique de groupe hebdomadaire en ligne sur l'Évangile de Jean. Les feuilles de questions sont partagées avant chaque rencontre.",
                "post_event_summary_en" => null,
                "post_event_summary_fr" => null,

                "attachments" => [
                        ["type" => "document", "document_name" => "gbs_john_schedule.pdf", "locale" => "en_CA"],
                ]
        ],

        2 => [
                "title_en" => "Concordia Group Bible Study",
                "title_fr" => "Étude biblique de groupe à Concordia",
                "images" => null, // Default events images
                "category" => "gbs_in_person",
                "minimum_profile" => "guest",
                "bible_study_id" => 26, // A Kernel of Wheat / Un grain de blé (John 12:20-50)
                "start_date" => "2026-10-27 12:00:00",
                "has_end_date" => true,
                "end_date" => "2026-10-27 13:30:00",
                "recurring" => true, // Weekly
                "location" => "Concordia University - Webster Library",
                "contact_name" => null,
                "contact_email" => null,
                "website_url" => null,
                "featured_on_home_page" => false,
                "featured_on_events_page" => false,
                "description_en" => null,
                "description_fr" => null,
                "post_event_summary_en" => null,
                "post_event_summary_fr" => null,
        ],

        // Past event. Files go in images/events/conference/2026-07-23/ and documents/events/conference/2026-07-23/
        3 => [
                "title_en" => "CBFE 2026 - Jesus is the Answer",
                "title_fr" => "CBFE 2026 - Jésus est la réponse",
                "images" => [
                        "desktop" => "2026_cbfe_titre-desktop.jpg",
                        "mobile" => "2026_cbfe_titre-mobile.jpg",
                        "square" => "2026_cbfe_titre-square.jpg"],
                "category" => "conference",
                "minimum_profile" => "guest",
                "bible_study_id" => null,
                "start_date" => "2026-07-23 19:00:00",
                "has_end_date" => true,
                "end_date" => "2026-07-26 13:00:00",
                "recurring" => false,
                "location" => "CEGEP John Abbott College, Sainte-Anne-de-Bellevue, QC H9X 3L9",
                "contact_name" => null,
                "contact_email" => null,
                "website_url" => "https://franco2026.university-bible-fellowship.ca",
                "featured_on_home_page" => false,
                "featured_on_events_page" => true,

                "description_en" => "<p>This theme aims to address questions about life that are often asked (sometimes philosophically) by both non-believers and believers, providing biblical answers that point to Jesus. The messages will offer practical guidance on how to live your life in Christ, or present what it means to be a Christian (especially for newcomers).</p>

                <p>Particularly, we want to reflect on the following questions:</p>

                <ul>
                   <li>Why do we need Jesus? (Lk 15)</li>
                   <li>Why believe in Jesus? (Lk 6)</li>
                   <li>Why do we suffer? (Lk 9)</li>
                   <li>Why do we hope in Jesus? (1Pe 1:3-4)</li>
                </ul>",

                "description_fr" => "<p>Ce thème a pour but de répondre aux questions sur la vie souvent posées (parfois philosophiques) autant par les non-croyants que par les croyants, en fournissant des réponses bibliques qui pointent vers Jésus. Les messages donneront des conseils pratiques sur la façon de vivre sa vie en Christ, ou présenteront ce que signifie être chrétien (surtout pour les nouveaux venus).</p>

                <p>Notamment, nous voulons réfléchir sur les questions suivantes :</p>

                <ul>
                   <li>Pourquoi avons-nous besoin de Jésus ? (Luc 15)</li>
                   <li>Pourquoi croire en Jésus ? (Luc 6)</li>
                   <li>Pourquoi souffrons-nous ? (Luc 9)</li>
                   <li>Pourquoi mettre notre espérance en Jésus ? (1 Pierre 1.3-4)</li>
                </ul>",
                "post_event_summary_en" => null,
                "post_event_summary_fr" => null,

                "attachments" => [
                        ["type" => "document", "document_name" => "2026_cbfe_programme_fr.pdf", "locale" => "fr_CA"],
                        ["type" => "document", "document_name" => "2026_cbfe_programme_en.pdf", "locale" => "en_CA"],
                        // Media show sorted by file name
                        ["type" => "media", "document_name" => "2026-07_cbfe_groupe.jpg"],
                        ["type" => "media", "document_name" => "2026-07_cbfe_jour_02_00.jpg"],
                        ["type" => "media", "document_name" => "2026-07_cbfe_jour_02_01.jpg"],
                        ["type" => "media", "document_name" => "2026-07_cbfe_jour_02_02.jpg"],
                        ["type" => "media", "document_name" => "2026-07_cbfe_jour_02_03.jpg"],
                        ["type" => "media", "document_name" => "2026-07_cbfe_jour_03_00.jpg"],
                        ["type" => "media", "document_name" => "2026-07_cbfe_jour_03_01.jpg"],
                        ["type" => "media", "document_name" => "2026-07_cbfe_jour_03_02.jpg"],
                        ["type" => "media", "document_name" => "2026-07_cbfe_jour_03_03.jpg"],
                        ["type" => "media", "document_name" => "2026-07_cbfe_jour_04_00.jpg"],
                        ["type" => "media", "document_name" => "2026-07_cbfe_jour_04_01.jpg"],
                        ["type" => "media", "document_name" => "2026-07_cbfe_jour_04_02.jpg"],
                        ["type" => "media", "document_name" => "2026-07_cbfe_jour_04_03.jpg"],
                        ["type" => "media", "document_name" => "2026-07_cbfe_jour_04_04.jpg"],
                        ["type" => "media", "document_name" => "2026-07_tour_00.jpg"],
                        ["type" => "media", "document_name" => "2026-07_tour_01.jpg"],
                        ["type" => "media", "document_name" => "2026-07_tour_02.jpg"],
                        ["type" => "media", "document_name" => "2026-07_visitation_01.jpg"],
                        ["type" => "media", "document_name" => "2026-07_visitation_02.jpg"],
                ],
        ],

];

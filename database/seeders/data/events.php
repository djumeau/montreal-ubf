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
                "post_event_summary_en" => "<p>**By Philip Wong**</p>

<p><strong>'Lord, to whom shall we go? You have the words of eternal life.' — John 6:68</strong></p>

<p>The 2026 Francophone SBC, held July 16–19 at John Abbott College in Montreal, brought together 121 participants from Francophone ministries in France, Belgium, Switzerland, and Canada, along with supporting chapters from North America. The theme, **“Jesus Is the Answer,”** focused on four questions: Why do we need God? Why believe in Jesus? Why do we suffer? And why do we hope in Jesus?</p>

<p>Four messages explored these questions. Philip Wong (Montreal) shared from Luke 15 that Jesus seeks and saves those who are lost. David Jumeau (Montreal) taught from John 6:68 that Jesus alone has the words of eternal life and satisfies our deepest spiritual needs. Timothée L. (Brussels) shared from John 9 about Jesus as the Light of the World, revealing God’s glory even through suffering. Bruno Aussant (Paris) concluded from 1 Peter 1:3–4 that Jesus’ resurrection gives us a living hope that never fades.</p>

<p>Six life testimonies, mostly from young disciples, demonstrated how Jesus transforms lives. Four workshops encouraged participants in evangelism, prayer, spiritual growth, and finding rest in God.</p>

<p>God answered the conference’s three main prayer topics: that participants would find their ultimate answer in Jesus, that Francophone ministries would be united in God’s love, and that 120 people would attend and receive God’s vision for Francophone ministry. Despite only 65 registrations one week before the deadline, 121 participants attended by the final day. God also provided visas for two participants, protected a student’s lost passport, and supplied Laura to fill a missing role in the opening drama.</p>

<p>The conference highlighted the importance of the next generation. Half of the participants were youth and young adults who actively served through messages, testimonies, worship, prayer, workshops, and activities. God is raising a new generation of disciples for the Francophone mission.</p>

<p>The conference also celebrated the sending of **David and Connie Jumeau to Sherbrooke UBF** after many years of ministry in Montreal. We pray for God’s blessing as they shepherd students at the University of Sherbrooke and Bishop’s University.</p>

<p>More than 25 Francophone nations, including 21 in Africa, still need the Gospel. May God continue to expand the Francophone mission, raise shepherds and disciples, and proclaim Jesus—the answer—throughout all 29 Francophone nations.</p>",
                "post_event_summary_fr" => "<p>**Par Philip Wong**</p>

<p><strong>« Seigneur, vers qui irions-nous? Tu as les paroles de la vie éternelle. » — Jean 6.68</strong></p>

<p>Le conférence biblique francophone d'été 2026 s’est tenu du 16 au 19 juillet au Collège John Abbott à Montréal, a réuni 121 participants de ministères francophones de France, de Belgique, de Suisse et du Canada, ainsi que des sections de soutien d’Amérique du Nord. Le thème, **« Jésus est la réponse »**, s’articulait autour de quatre questions : Pourquoi avons-nous besoin de Dieu? Pourquoi croire en Jésus? Pourquoi souffrons-nous? Et pourquoi espérons-nous en Jésus?</p>

<p>Quatre messages ont exploré ces questions. Philip Wong (Montréal) a partagé, à partir de Luc 15, que Jésus cherche et sauve ceux qui sont perdus. David Jumeau (Montréal) a enseigné, à partir de Jean 6.68, que Jésus seul possède les paroles de la vie éternelle et comble nos besoins spirituels les plus profonds. Timothée L. (Bruxelles) a parlé, à partir de Jean 9, de Jésus comme Lumière du monde, révélant la gloire de Dieu même à travers la souffrance. Bruno Aussant (Paris) a conclu, à partir de 1 Pierre 1.3-4, que la résurrection de Jésus nous donne une espérance vivante qui ne s’éteint jamais.</p>

<p>Six témoignages de vie, principalement ceux de jeunes disciples, ont montré comment Jésus transforme les vies. Quatre ateliers ont encouragé les participants à l’évangélisation, à la prière, à la croissance spirituelle et à trouver le repos en Dieu.</p>

<p>Dieu a exaucé les trois principaux sujets de prière de la conférence : que les participants trouvent leur réponse ultime en Jésus, que les ministères francophones soient unis dans l’amour de Dieu, et que 120 personnes y assistent et reçoivent la vision de Dieu pour le ministère francophone. Bien qu’il n’y ait eu que 65 inscriptions une semaine avant la date limite, 121 participants étaient présents le dernier jour. Dieu a également pourvu aux visas de deux participants, a protégé le passeport perdu d’un étudiant et a fait en sorte que Laura vienne combler un rôle vacant dans la pièce d’ouverture.</p>

<p>La conférence a mis en évidence l’importance de la prochaine génération. La moitié des participants étaient des jeunes et des jeunes adultes qui ont activement pris part aux messages, les témoignages, l'adoration, de la prière, d’ateliers et d’activités. Dieu suscite une nouvelle génération de disciples pour la mission francophone.</p>

<p>La conférence a également célébré l’envoi de **David et Connie Jumeau au ministère de Sherbrooke** après de nombreuses années à Montréal. Nous prions pour que Dieu les bénisse alors qu’ils accompagnent les étudiants de l’Université de Sherbrooke et de l’Université Bishop’s.</p>

<p>Plus de 25 pays francophones, dont 21 en Afrique, ont encore besoin de l’Évangile. Que Dieu continue d’étendre la mission francophone, de former des bergers et des disciples, et de proclamer Jésus — la réponse — dans l’ensemble des 29 pays francophones.</p>",

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

        // Upcoming event. Files go in images/events/event/2026-12-13/ and documents/events/event/2026-12-13/
        4 => [
                "title_en" => "Christmas 2026",
                "title_fr" => "Noël 2026",
                "images" => [
                        "desktop" => "2026_christmas-desktop.jpg",
                        "mobile" => "2026_christmas-mobile.jpg",
                        "square" => "2026_christmas-square.jpg"],
                "category" => "event",
                "minimum_profile" => "guest",
                "bible_study_id" => null,
                "start_date" => "2026-12-13 11:00:00", // Sunday. TODO: confirm start time
                "has_end_date" => false, // TODO: set true and add "end_date" if it has an end time
                "end_date" => null,
                "recurring" => false,
                "location" => "Montreal UBF, 2627 rue Ryde, Montréal, QC, H3K 1R7", // TODO: confirm
                "contact_name" => null,
                "contact_email" => null,
                "website_url" => null,
                "featured_on_home_page" => false,
                "featured_on_events_page" => false,
                "description_en" => null,
                "description_fr" => null,
                "post_event_summary_en" => null,
                "post_event_summary_fr" => null,

                "attachments" => [],
        ],

];

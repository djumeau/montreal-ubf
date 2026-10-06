<?php

// Main topics come before their subtopics: "parent_id" is the id of an entry above (null = a main topic)
return [

    1 => [
        "parent_id" => null,
        "topic_en" => "Pray for our Sunday worship service, that many people in Montreal may come and hear the word of God.",
        "topic_fr" => "Prions pour notre culte du dimanche, afin que beaucoup de gens de Montréal viennent entendre la parole de Dieu.",
        "category" => "local",
        "min_role" => "guest",
        "url" => null,
        "answered" => false,
    ],

    2 => [
        "parent_id" => null,
        "topic_en" => "Pray for the fall Bible conference: for the messengers, the Bible study leaders and everyone who is invited.",
        "topic_fr" => "Prions pour la conférence biblique d'automne : pour les messagers, les responsables d'études bibliques et toutes les personnes invitées.",
        "category" => "conferences",
        "min_role" => "guest",
        "url" => "https://www.montrealubf.org/events",
        "answered" => false,
    ],

    3 => [
        "parent_id" => 2,
        "topic_en" => "Pray for the conference preparation team: registration, transportation and meals.",
        "topic_fr" => "Prions pour l'équipe de préparation de la conférence : inscriptions, transport et repas.",
        "category" => "conferences",
        "min_role" => "user",
        "url" => null,
        "answered" => false,
    ],

    4 => [
        "parent_id" => null,
        "topic_en" => "Pray for our missionaries serving on university campuses around the world, for their health and their visas.",
        "topic_fr" => "Prions pour nos missionnaires qui servent sur les campus universitaires à travers le monde, pour leur santé et leurs visas.",
        "category" => "world_missions",
        "min_role" => "user",
        "url" => null,
        "answered" => true,
    ],

    5 => [
        "parent_id" => null,
        "topic_en" => "Pray for the recovery of a member of our congregation who is having surgery this month.",
        "topic_fr" => "Prions pour le rétablissement d'un membre de notre assemblée qui sera opéré ce mois-ci.",
        "category" => "health",
        "min_role" => "member",
        "url" => null,
        "answered" => false,
    ],

];

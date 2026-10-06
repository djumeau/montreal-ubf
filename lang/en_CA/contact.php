<?php

return [

    "title" => "Contact",
    "conference_image_alt" => "Franco Tour Montreal Conference, July 2026",
    "inquiries" => "Inquiries",
    "subtitle" => "Get in touch!",
    "instructions" => "Fill out the form below and we will get back to you soon.",
    "name" => "Name*",
    "email" => "Email*",
    "phone" => "Phone",
    "subject" => "Subject",
    "message" => "Message*",
    "send" => "Send",
    "status_received" => "Thanks! We've received your message and will be in touch soon.",

    "inquiring_about" => "Inquiry",

    // Notice above the Send button; followed by a link to the Privacy Policy
    "privacy_note" => "Your name and email address are used only to confirm that we received your message and to answer it. To learn more, read our",

    // Message filled in when arriving from a study of the Bible Study Schedule
    "prefill_in_person" => "Please provide more information on the location of the Bible study.",
    "prefill_online" => "I want to attend :title.",
    "prefill_service" => "Please provide more information.",
    "prefill_study" => "Bible study: :title",
    "prefill_when" => "When: :when",
    "prefill_where" => "Where: :where",

    // Copy sent to the church when the form is submitted, with the full message
    "email_church_subject" => "New message: :inquiry (:name)",
    "email_church_greeting" => "New message from the contact page",
    "email_church_from" => "From: :name (:email)",
    "email_church_message" => "Message (:inquiry):",
    "email_church_action" => "Manage Inquiries",

    // Confirmation sent to the visitor when the form is submitted; subject and opening line per inquiry heading
    "email_greeting" => "Hello :name,",
    "email_subjects" => [
        "ministry" => "Thank you for your interest in our ministry",
        "group_study" => "Your interest in Group Bible Study",
        "one_to_one" => "Your interest in One-to-One Bible Study",
        "worship" => "Your question about our Worship Service",
        "subscribe" => "Your request to subscribe to the site",
        "other" => "We received your message",
        "pastoral" => "Your message to the Pastoral Team",
    ],
    "email_intros" => [
        "ministry" => "Thank you for your interest in our ministry. Someone from our church will write back to tell you more about us.",
        "group_study" => "Thank you for your interest in our group Bible studies. We will write back with the details of the study.",
        "one_to_one" => "Thank you for your interest in one-to-one Bible study. We will write back to connect you with a mentor.",
        "worship" => "Thank you for your interest in our worship service. We will write back with the information you need.",
        "subscribe" => "Thank you for asking to subscribe to the site. We will review your request and write back soon.",
        "other" => "Thank you for writing to us. We will get back to you soon.",
        "pastoral" => "Your message has been passed on to the pastoral team, who will get back to you soon.",
    ],

];
<?php

return [

    "background" => "Brief Background",
    "hello" => "Hello",
    "unauthorized" => "Unauthorized action.",
    "consent" => [
        "title" => "Respecting your privacy",
        "content_1" => "We use necessary cookies to remember your language and keep you signed in. Your name and email are collected only to manage your account or to answer a message you send us. Accepting also allows Google Maps to be displayed on event pages.",
        "content_2" => "To learn more, read our ",
        "content_3" => "Privacy Policy",
        "accept" => "Accept All",
        "reject" => "Reject All",
        "preferences" => "Cookie preferences",
    ],
    "privacy" => [
        "title" => "Privacy Policy",
        "last_updated" => "Last updated: :date",
        'intro' => 'Welcome to our web site. We are committed to processing your personal information transparently and securely, in compliance with Quebec\'s Law 25. We keep what we collect to a minimum, and we do not use advertising or audience measurement tools. This policy describes what the site actually does with your information.',

        // Each section: a title, a paragraph and, optionally, a bulleted list ('items') shown under the paragraph
        'sections' => [
            [
                'title' => '1. The Personal Information We Collect',
                'text' => 'You can browse the public pages (studies, schedule, events, prayer topics, giving) without an account and without giving us any personal information. We only collect information in the following cases:',
                'items' => [
                    'Account: accounts are not open to public registration; they are created by an Elder or an Administrator. An account holds your full name, your email address, your role (which determines the content you can see), your password in encrypted (hashed) form and, if you add one, a profile picture.',
                    'Contact form: your name, your email address, the subject of your inquiry, your message, the date it was sent and whether you were signed in when you sent it. When we handle your message, we also record when it was read and answered, by whom, and an internal follow-up note.',
                    'Cookies: only first-party, necessary cookies. One remembers your language for 30 days; the others keep you signed in and protect forms against forged requests, and expire when you sign out or after a period of inactivity. They are set whatever your choice on the consent banner, because the site cannot work without them.',
                    'Consent choice: your answer on the consent banner is saved in your own browser (not on our servers) for 30 days, after which we ask again. You can change it at any time with the "Cookie preferences" link at the bottom of every page.',
                    'Technical data: the session that keeps you signed in may record your IP address and browser type, and our hosting provider keeps standard server logs (IP address, pages requested, errors) to keep the site running and secure.',
                ],
            ],
            [
                'title' => '2. Why Your Information is Used',
                'text' => 'We use your information only to:',
                'items' => [
                    'verify your identity when you sign in and give you access to the content that matches your role (for example, study documents or events reserved for members);',
                    'send essential account emails: a welcome message with a temporary password when your account is created, and a new temporary password if an Elder or an Administrator resets it;',
                    'confirm by email that we received a message sent through the contact form (your own message is not repeated in that confirmation), and reply to it;',
                    'display the site in the language you chose;',
                    'protect the site against spam and abuse. The contact form uses a hidden field, a timing check and a limit on the number of messages sent in a short time; no third-party verification service (CAPTCHA) is involved.',
                ],
            ],
            [
                'title' => '3. Who Can See Your Information',
                'text' => 'Access is limited to the people who need it. Only Elders and Administrators can see the list of accounts (name, email address and role) and the messages sent through the contact form. They can create an account, change its role, reset its password or delete it; they never see your password. Your profile picture is kept in a private folder and is not shown on public pages. Prayer requests and pastoral inquiries sent through the contact form are read only by Elders and Administrators and are not published on the site.',
            ],
            [
                'title' => '4. Data Storage and Retention',
                'text' => 'Your account and the messages sent through the contact form are stored with our web hosting provider, which also sends the site\'s emails; its servers may be located outside Quebec. A copy of each contact form message, with your name and email address, is also delivered to our church inbox, a Gmail account provided by Google, whose servers are located outside Quebec.',
                'items' => [
                    'Account: kept for as long as your account remains active. Deleting it permanently removes your profile and your profile picture.',
                    'Contact form messages: kept, with their copy in our inbox, only for as long as needed to reply and follow up on your inquiry, then deleted by an Elder or an Administrator. These messages are not attached to an account: deleting your account does not delete them, but you can ask us to do so (see section 7).',
                    'Language cookie and consent choice: 30 days.',
                ],
            ],
            [
                'title' => '5. Third-Party Services and External Links',
                'text' => 'We do not sell, rent or share your personal information with advertisers or analytics companies, and the site loads no advertising, tracking or social media scripts. Your information is only passed to the service providers we need to run the site: our web hosting provider and, for contact form messages, Google (Gmail), as described in section 4.',
                'items' => [
                    'Google Maps: event pages can display a map. It is only loaded if you choose "Accept All" on the consent banner; otherwise it stays disabled and nothing is requested from Google.',
                    'Online giving: this site does not process donations and never receives or stores your payment details. The giving page links to Zeffy, which collects and processes your donation on its own site under its own privacy policy.',
                    'External links: some pages link to other services (Google Maps, Zoom, Zeffy, BibleGateway, startbiblestudy.org, ubf.org, Facebook, Instagram, X). Nothing is sent to them until you follow the link; they may then collect information such as your IP address under their own privacy policies.',
                ],
            ],
            [
                'title' => '6. How We Protect Your Information',
                'text' => 'Passwords are stored in encrypted (hashed) form and cannot be read by anyone, including us. Temporary passwords are sent by email; we recommend replacing yours with one of your own from your profile as soon as you sign in. Profile pictures and documents reserved for members are kept in private storage and are served only to signed-in accounts allowed to see them. Management pages are restricted by role, and forms are protected against forged requests.',
            ],
            [
                'title' => '7. Your Legal Rights Under Law 25',
                'text' => 'You keep control of your information. Once signed in, you can use your profile to correct your name and email address, change your password, replace your profile picture, or permanently delete your account yourself. You can also ask us to:',
                'items' => [
                    'give you access to the information we hold about you, and a copy in a commonly used electronic format;',
                    'correct information that is inaccurate or incomplete;',
                    'delete your account or the messages you sent us through the contact form;',
                    'stop using your information, by withdrawing your consent.',
                ],
            ],
            [
                'title' => '8. Changes to This Policy',
                'text' => 'We update this policy whenever the way the site handles personal information changes; the date at the top of the page shows the latest version. When the policy changes, the consent banner is shown again so you can review it and renew your choice.',
            ],
            [
                'title' => '9. Contact Our Privacy Officer',
                'text' => 'To exercise your rights or ask a question, write to our designated Privacy Officer below. We reply to written requests within 30 days. If you are not satisfied with our reply, you may file a complaint with the Commission d\'accès à l\'information du Québec (cai.gouv.qc.ca).'
            ],

        ], // End Sections

        "contact" => "Contact",
        "name" => "David Jumeau (Developer)",
        "email" => "montrealubf@gmail.com"

    ], // End Privacy

];

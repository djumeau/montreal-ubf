<?php

return [

    "background" => "Brève introduction",
    "hello" => "Salut",
    "unauthorized" => "Action non autorisée.",
    "consent" => [
        "title" => "Respect de votre vie privée",
        "content_1" => "Nous utilisons des témoins (cookies) nécessaires pour mémoriser votre langue et maintenir votre connexion. Votre nom et votre courriel sont recueillis uniquement pour gérer votre compte ou répondre à un message que vous nous envoyez. En acceptant, vous permettez aussi l'affichage de Google Maps sur les pages d'événements.",
        "content_2" => "Pour en savoir plus, consultez notre",
        "content_3" => "Politique de confidentialité",
        "accept" => "Tout accepter",
        "reject" => "Tout refuser",
        "preferences" => "Préférences de témoins",
    ],
    "privacy" => [
        "title" => "Politique de confidentialité",

        "last_updated" => "Dernière mise à jour : le :date",

        'intro' => 'Bienvenue sur notre site web. Nous nous engageons à traiter vos renseignements personnels de manière transparente et sécurisée, en conformité avec la Loi 25 du Québec. Nous limitons la collecte au strict minimum et n\'utilisons aucun outil de publicité ou de mesure d\'audience. Cette politique décrit ce que le site fait réellement de vos renseignements.',

        // Chaque section : un titre, un paragraphe et, au besoin, une liste à puces ('items') affichée sous le paragraphe
        'sections' => [
            [
                'title' => '1. Les renseignements personnels que nous recueillons',
                'text' => 'Vous pouvez consulter les pages publiques (études, horaire, événements, sujets de prière, dons) sans compte et sans nous fournir de renseignements personnels. Nous recueillons des renseignements uniquement dans les cas suivants :',
                'items' => [
                    'Compte : l\'inscription n\'est pas ouverte au public; les comptes sont créés par un ancien ou un administrateur. Un compte contient votre nom complet, votre adresse courriel, votre rôle (qui détermine le contenu que vous pouvez voir), votre mot de passe sous forme chiffrée (hachée) et, si vous en ajoutez une, une photo de profil.',
                    'Formulaire de contact : votre nom, votre adresse courriel, le sujet de votre demande, votre message, la date d\'envoi et le fait que vous étiez connecté ou non au moment de l\'envoi. Lorsque nous traitons votre message, nous notons aussi quand il a été lu et répondu, par qui, ainsi qu\'une note de suivi interne.',
                    'Témoins (cookies) : uniquement des témoins d\'origine essentiels. L\'un mémorise votre langue pendant 30 jours; les autres maintiennent votre connexion et protègent les formulaires contre les requêtes falsifiées, et expirent à la déconnexion ou après une période d\'inactivité. Ils sont déposés quel que soit votre choix sur la bannière de consentement, car le site ne peut pas fonctionner sans eux.',
                    'Choix de consentement : votre réponse sur la bannière de consentement est enregistrée dans votre propre navigateur (et non sur nos serveurs) pendant 30 jours, après quoi nous vous la redemandons. Vous pouvez la modifier en tout temps à l\'aide du lien « Préférences de témoins » au bas de chaque page.',
                    'Données techniques : la session qui maintient votre connexion peut enregistrer votre adresse IP et le type de votre navigateur, et notre hébergeur conserve des journaux de serveur habituels (adresse IP, pages demandées, erreurs) afin d\'assurer le fonctionnement et la sécurité du site.',
                ],
            ],
            [
                'title' => '2. Pourquoi vos renseignements sont utilisés',
                'text' => 'Nous utilisons vos renseignements uniquement pour :',
                'items' => [
                    'vérifier votre identité à la connexion et vous donner accès au contenu correspondant à votre rôle (par exemple, les documents d\'étude ou les événements réservés aux membres);',
                    'vous envoyer les courriels indispensables liés à votre compte : un message de bienvenue avec un mot de passe temporaire à la création du compte, et un nouveau mot de passe temporaire si un ancien ou un administrateur le réinitialise;',
                    'vous confirmer par courriel la réception d\'un message envoyé par le formulaire de contact (votre message n\'est pas repris dans cette confirmation) et y répondre;',
                    'afficher le site dans la langue de votre choix;',
                    'protéger le site contre le pourriel et les abus. Le formulaire de contact utilise un champ caché, une vérification du délai d\'envoi et une limite du nombre de messages envoyés sur une courte période; aucun service de vérification tiers (CAPTCHA) n\'est utilisé.',
                ],
            ],
            [
                'title' => '3. Qui peut voir vos renseignements',
                'text' => 'L\'accès est limité aux personnes qui en ont besoin. Seuls les anciens et les administrateurs peuvent voir la liste des comptes (nom, adresse courriel et rôle) et les messages envoyés par le formulaire de contact. Ils peuvent créer un compte, en changer le rôle, en réinitialiser le mot de passe ou le supprimer; ils ne voient jamais votre mot de passe. Votre photo de profil est conservée dans un dossier privé et n\'apparaît pas sur les pages publiques. Les demandes de prière et les demandes pastorales envoyées par le formulaire de contact sont lues uniquement par les anciens et les administrateurs et ne sont pas publiées sur le site.',
            ],
            [
                'title' => '4. Hébergement et conservation des données',
                'text' => 'Votre compte et les messages envoyés par le formulaire de contact sont conservés chez notre hébergeur web, qui envoie aussi les courriels du site; ses serveurs peuvent être situés à l\'extérieur du Québec. Une copie de chaque message du formulaire de contact, avec votre nom et votre adresse courriel, est aussi transmise à la boîte de réception de notre église, un compte Gmail fourni par Google, dont les serveurs sont situés à l\'extérieur du Québec.',
                'items' => [
                    'Compte : conservé tant que votre compte demeure actif. Sa suppression efface définitivement votre profil et votre photo de profil.',
                    'Messages du formulaire de contact : conservés, avec leur copie dans notre boîte de réception, seulement le temps nécessaire pour répondre à votre demande et en assurer le suivi, puis supprimés par un ancien ou un administrateur. Ces messages ne sont pas rattachés à un compte : la suppression de votre compte ne les efface pas, mais vous pouvez nous demander de le faire (voir la section 7).',
                    'Témoin de langue et choix de consentement : 30 jours.',
                ],
            ],
            [
                'title' => '5. Services tiers et liens externes',
                'text' => 'Nous ne vendons, ne louons ni ne partageons vos renseignements personnels avec des annonceurs ou des entreprises d\'analyse, et le site ne charge aucun script de publicité, de suivi ou de réseaux sociaux. Vos renseignements ne sont transmis qu\'aux fournisseurs de services nécessaires au fonctionnement du site : notre hébergeur web et, pour les messages du formulaire de contact, Google (Gmail), comme l\'indique la section 4.',
                'items' => [
                    'Google Maps : les pages d\'événements peuvent afficher une carte. Elle n\'est chargée que si vous choisissez « Tout accepter » sur la bannière de consentement; sinon, elle demeure désactivée et rien n\'est demandé à Google.',
                    'Dons en ligne : ce site ne traite aucun don et ne reçoit ni ne conserve jamais vos renseignements de paiement. La page des dons renvoie à Zeffy, qui recueille et traite votre don sur son propre site, selon sa propre politique de confidentialité.',
                    'Liens externes : certaines pages contiennent des liens vers d\'autres services (Google Maps, Zoom, Zeffy, BibleGateway, startbiblestudy.org, ubf.org, Facebook, Instagram, X). Rien ne leur est transmis tant que vous ne suivez pas le lien; ils peuvent ensuite recueillir des renseignements, comme votre adresse IP, selon leurs propres politiques de confidentialité.',
                ],
            ],
            [
                'title' => '6. Comment nous protégeons vos renseignements',
                'text' => 'Les mots de passe sont conservés sous forme chiffrée (hachée) et ne peuvent être lus par personne, pas même par nous. Les mots de passe temporaires sont envoyés par courriel; nous vous recommandons de remplacer le vôtre par un mot de passe personnel, à partir de votre profil, dès votre première connexion. Les photos de profil et les documents réservés aux membres sont conservés dans un espace privé et ne sont remis qu\'aux comptes connectés autorisés à les voir. Les pages de gestion sont restreintes selon le rôle, et les formulaires sont protégés contre les requêtes falsifiées.',
            ],
            [
                'title' => '7. Vos droits légaux en vertu de la Loi 25',
                'text' => 'Vous gardez la maîtrise de vos renseignements. Une fois connecté, vous pouvez utiliser votre profil pour corriger votre nom et votre adresse courriel, changer votre mot de passe, remplacer votre photo de profil ou supprimer vous-même votre compte de façon définitive. Vous pouvez aussi nous demander :',
                'items' => [
                    'd\'accéder aux renseignements que nous détenons à votre sujet et d\'en recevoir une copie dans un format électronique courant;',
                    'de corriger des renseignements inexacts ou incomplets;',
                    'de supprimer votre compte ou les messages que vous nous avez envoyés par le formulaire de contact;',
                    'de cesser d\'utiliser vos renseignements, en retirant votre consentement.',
                ],
            ],
            [
                'title' => '8. Modifications de cette politique',
                'text' => 'Nous mettons cette politique à jour chaque fois que la façon dont le site traite les renseignements personnels change; la date indiquée en haut de la page correspond à la version la plus récente. Lorsque la politique change, la bannière de consentement s\'affiche de nouveau afin que vous puissiez la consulter et renouveler votre choix.',
            ],
            [
                'title' => '9. Responsable de la protection des renseignements personnels',
                'text' => 'Pour exercer vos droits ou poser une question, écrivez à notre responsable ci-dessous. Nous répondons aux demandes écrites dans un délai maximal de 30 jours. Si notre réponse ne vous satisfait pas, vous pouvez porter plainte auprès de la Commission d\'accès à l\'information du Québec (cai.gouv.qc.ca).'
            ],

        ], // End Sections

        "contact" => "Contact",
        "name" => "David Jumeau (développeur)",
        "email" => "montrealubf@gmail.com"

    ], // End Privacy

];

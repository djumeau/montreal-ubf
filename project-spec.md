## Francophone Conferences Specifications

## Problem (Core Idea)

A website to introduce the 2026 Francophone Bible Conference event with an overview, resources for bible questionnaires, a program, information to register, the location of the venue with pictures. It must be bilingual.

## Users

- **Newcomers**:
  A fast way to retrieve information especially the program and the bible questionnaires

- **Admin**:
  Uploads bible questionnaires.

## Features

Here is a list of features for the site

A. **Navbar**
The navbar uses a frosted glass look, with the UBF logo (see image), and should contain links to:

- Home
- Resources
- Venue
- Register
- World icon with clickable language selector next to it. It toggles the language settings between "EN" and "FR".
- Login (Admin)

B. **Header**

A section header should be a dark blue to black gradient with the title: "Jésus est la réponse" with "Jésus" in large text with "est la réponse" should be underneath.

Underneath the title should have a button "Register".

C. **About**

A section that will provide the purpose of the conference. Use the following text:

"Ce thème a comme but de joindre des questions sur la vie souvent posées (parfois philosophiques) autant par les non-croyants et les croyants, en fournissant des réponses bibliques qui pointent vers Jésus. Les messages donneront des ou les pratiques sur comment vivre sa vie en Christ, ou présenteront ce que ça veut dire être un chrétien (pour les nouveaux)."

D. Countries Represented

A section that will display the various country flags participating:

- Belgium
- Canada
- France
- Switzerland

Along with guests from the USA. (Yes, there are French-speaking communities there!)

E. **Registration**

A section that provides the QR code and a link to the Google form for registration.

F. **Footer**

Place a map of John Abbott College along with contact emails to reach us.

G. **Admin**

This is a separate page where administrator will provide uploads for:

- Bible questionnaires in both languages EN and FR with access to word .docx or .pdf files.
- It will be a simple authentication process by providing a password. (suggest "JesusAnswer26")
- The dashboard will provide the opportunity to change the password and confirm the change
- Default admin email will be directed to "djumeau@gmail.com"
- Photo uploads will be used on the "Venue" Page. Is there a way to copy and convert the uploaded photo into a thumbnail?

H. **Venue**

- This separate page provides images from John Abbott College located at St. Anne-de-Bellevue, QC
- Underneath will contain thumbnails of images.
- Clicking an image will provide thumbnails that show a modal.

## Data

No database would be used. Will modify a JSON text file that will provide Bible questionnaire titles in EN and FR and links to download their files.

**Document**

- id
- title - EN
- title - FR
- Bible Passage - EN
- Bible Passage - FR
- contentType (text | file)
- fileURL - EN
- fileURL - FR
- description
- createdAt
- updatedAt

## Tech Stack

- Framework Laravel with Blade using Herd
- One codebase/repo for less overhead
- TypeScript for type safety

**CSS Frameworks**
Tailwind CSS v4 with ShadCN UI

## UI/UX

**General**

- Modern, minimal, developer-focused
- Dark mode by default, light mode optional
- Clean typography, generous whitespace
- Subtle borders and shadows
- Reference: Notion, Linear, Raycast
- Syntax highlighting for codeblocks

**Layout**

- Navbar: Sticky navbar with frosted transparent background with logo and dark text as links
- Main: Grid of sections

**Type Colors & Icons**

- Command Color: #f97316 (orange)
- Command Icon: Terminal
- Note Color: #ffb405 (light orange)
- Note Icon: StickyNote
- File Color: #1d398e (slate gray)
- File Icon: File
- Image Color: #4874ec (blue)
- Image Icon: Image
- Link Color: #109ab9 (blue-green)
- Link Icon: Link

**Responsive**

- Desktop-first but mobile usable

**Micro-interactions:**

- Smooth transitions
- Loading skeletons

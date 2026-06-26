<x-layout>

    <x-slot name="title">À propos de nous</x-slot>

    <h1 class='flex justify-center text-center font-bold text-3xl'>À propos de nous</h1>

    <br/>

    <h2 class='flex justify-center text-center font-bold text-2xl'>History</h2>

    <x-text-image :toggleLeft="true" 
    img="./images/history/lee-barry.jpg"
    alt="Dr. Samuel Lee and Missionary Sarah Barry"
    :imageSize="75">University Bible Fellowship begain in South Korea in 1961 under Dr. Samuel Lee (a presybterian pastor) and
        Missionary Sarah Barry.</x-text-image>

    <x-text-image :toggleLeft="false" 
    img="./images/history/bible-reading-class-ubf-korea.jpg"
    alt="Bible Reading Class"
    :imageSize="80">Originally, they served South Korean students through a Bible reading club.</x-text-image>

    <x-text-image :toggleLeft="true" 
    img="./images/history/first-cis-conference.jpg"
    alt="1993 CIS Conference"
    :imageSize="75">The ministry grew to various cities within the country and in the 1970s expanded to Germany, America,
        Latin America, and the world. UBF currently has chapters in 96 countries.</x-text-image>


    <x-text-image :toggleLeft="false" 
    img="./images/history/cdn-campus-mission-1982.jpeg"
    alt="Prayer Circle"
    :imageSize="80">Canadian ministry began in 1982 in Winnipeg Manitoba and has since expanded to several cities across the country.</x-text-image>

    <x-text-image :toggleLeft="true" 
    img="./images/ministry/20240825-presentation-euro-report.jpg"
    alt="Presentation in Montreal"
    :imageSize="80">The Montreal ministry began in 1991. The church is composed of laymen, families and young adults.</x-text-image>

    <p></p>

    <br/>

    <h2 class='flex justify-center text-center font-bold text-2xl'>Mission and Vision</h2>

    <br/>

    <p>Our main mission statement is student-oriented to bring the gospel to campus
        students. We believe that our students are our future leaders. Once coming to believe in
        Jesus Christ as Lord and Saviour, they can be influential in all aspects of life and to transmit the good news
        of the gospel to their peers, the nation, and for the whole world.</p>

    <p>For more inforation about the ministry, visit <a href='https://ubf.org/about/origin' class='underline' target='_blank'>ubf.org</a>.</p>

    <br/>

    <h2 class='flex justify-center text-center font-bold text-2xl'>Statement of Faith</h2>

    <br/>

    <h3 class='flex justify-left text-left font-bold text-xl'>We believe:</h3>

    <ul class='list-disc list-outside p-5 space-y-4'>
        <li>There is one God in three Persons: God the Father, God the Son, and God the Holy Spirit.</li>
        <li>God created the heavens and the earth and all other things in the universe: that He is the Sovereign Ruler
            of all things; that the Sovereign God reveals Himself; we believe in his redemptive work and in his final
            judgment.</li>
        <li>The Bible is inspired by God; that it is the truth; that it is the final authority in faith and practice.
        </li>
        <li>Since the fall of Adam, all people have been under the bondage and power of sin and are deserving of the
            judgment and wrath of God.</li>
        <li>Jesus Christ, who is God and man, through his atoning, sacrificial death on the cross for our sins and his
            resurrection, is the only way of salvation; he alone saves us from sin and judgment and purifies us from the
            contamination of the world caused by sin.</li>
        <li>Jesus Christ rose from the dead, ascended into heaven and sits at the right hand of God the Father.</li>
        <li>Regeneration is by the work of the Holy Spirit, and that it is necessary if one is to enter the kingdom of
            God. We believe that God sent his Holy Spirit to empower his church to witness to Jesus to the ends of the
            earth.</li>
        <li>We are made righteous by grace alone, through faith alone.</li>
        <li>The Holy Spirit works in the heart of every believer to lead him.</li>
        <li>The church is the body of Christ and that all Christians are members of it.</li>
        <li>Jesus will come again in glory to judge the living and the dead.</li>
    </ul>

</x-layout>

# Janus
The content manager for projects


From the /var/www/html/personal_projects/godlike/html/Janus/ directory:

    make up

    That starts all 6 services (MariaDB, Redis, RabbitMQ, backend PHP-FPM, frontend Angular dev server, Nginx). Then:

    - Frontend: http://janus.com → Angular admin UI (login page)
    - Backend API: http://backend.janus.com → Symfony JSON API

    Prerequisites:
    1. Docker and Docker Compose must be running
    2. A .env file must exist (copy from .env.example and fill in secrets)
    3. Your /etc/hosts (or system DNS) must resolve janus.com and backend.janus.com to 127.0.0.1

    First run only:
    make migrate   # Run Doctrine DB migrations to create all tables

    To stop everything:
    make down

    Since you're on WSL2, the hosts file to edit is C:\Windows\System32\drivers\etc\hosts on Windows (requires admin), adding:
    127.0.0.1  janus.com
    127.0.0.1  backend.janus.com


A content import manager is essentially the creator, organizer, and gatekeeper of your digital universe, so giving it a godlike name is a brilliant idea. It needs to sound powerful, omniscient, and capable of bringing order out of chaos.

Here are some godlike name concepts for your project, broken down by their mythological "superpowers" and how they relate to managing content:
The Transporters & Gatekeepers

Since your project is an import manager, names associated with moving things between realms or opening doorways fit perfectly.

    Project Janus: The Roman god of gates, doorways, and transitions. He looks to both the past and the future. Perfect for a system that takes old content and transitions it into a new platform.

    Project Bifröst: The burning rainbow bridge in Norse mythology that connects different realms. A great metaphor for an importer that bridges the gap between external data and your database.

    Project Hermes: The Greek messenger of the gods who could travel freely between the mortal and divine worlds. Ideal for a fast, reliable data transporter.

The Scribes & Record Keepers

These names focus on the "content" and "pages" aspect of your manager.

    Project Thoth: The Egyptian god of writing, wisdom, and magic. He maintained the universe through his records. It sounds punchy and fits a master content management system.

    Project Mnemosyne: (Pronounced ne-MOS-uh-nee) The Greek goddess of memory and the mother of the Muses. A highly sophisticated name for a system that acts as the memory bank of all your content.

    Project Metatron: In some traditions, the highest of the angels and the celestial scribe who records everything in the Book of Life. It has a great, imposing, sci-fi/godlike ring to it.

The Creators & Architects

If your manager doesn't just import, but also builds and structures the pages, these are great fits.

    Project Demiurge: A concept from Platonic philosophy referring to the artisan-like figure responsible for fashioning and maintaining the physical universe. Perfect for a page builder/importer.

    Project Genesis: The ultimate "creator" name, signifying the origin or mode of formation of your content.

    Project Atlas: The Titan who held up the sky. If your import manager is going to be the foundational backbone carrying the weight of massive amounts of content, this is a strong choice.

    Project Pantheon: Meaning "all the gods." A great name if your manager oversees a massive collection of different pages and content types, bringing them all under one roof.

My personal recommendation: Project Thoth or Project Janus both have great, punchy ring to them while perfectly matching the exact utility of an import manager.

Would you like me to help brainstorm some taglines or feature names based on one of these mythology themes?

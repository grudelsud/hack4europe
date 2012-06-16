hack4europe
===========

overview
--------

this repo contains the code developed during the hack4europe hackathon in leuven, belgium in june 2012 and it is released open source without any warranty.

it is a simple web application that fetches content from RSS feeds and extracts named entities from content using the Stanford NER package. these entities are then used to query the europeana API and show search results on the frontend.

from a technical POV, it is implemented using the following libraries / frameworks:

- data backend: codeigniter (you will need to download the full installation from the official website, this repo only provides the code for models/views/controllers and additional libraries)
- named entity extraction: Stanford NER wrapped in a play! framework application (you will need to download play! then create your own application and overwrite the elements with the code provided)
- frontend mvc: developed with backbone.js and twitter bootstrap, all the code is provided in this repo

folder structure
----------------

here's what you will find in the subfolders:

- /ner contains a play framework application, stripped down of all the play libraries, so it's code only
- /web contains the code required for codeigniter to show a web frontend, again it's code only
- /db is a dump of what shown during the hackathon
- /screenshots contains a few .png files of how the application should look like if everything goes fine

ready, set, go!
---------------

create an alias for your local host called hack4europe.net (e.g. for linux / mac machines, insert the following '127.0.0.1 hack4europe.net' in your /etc/hosts file)

download and install [play](http://playframework.org), create a new java application with command `play new your_app_name` then overwrite the content of your new application with everything you find in folder /ner then launch the application with `play ~run~. you should be able to run of the stanford NER with the simple test form shown at hack4europe.net:9000 and see its json output.

download and install [codeigniter](http://codeigniter.com), create a virtualhost called hack4europe.net and make it point to the fresh codeigniter installation. then copy all the content of folder /web/application in your codeigniter and tweak the config parameters where needed (e.g. /web/application/config/database.php)

import the database from folder /db, it contains a lot of useless stuff for the purpose of this demo, but also a set of meaningful fashion-related sources, already pre-scraped using diffbot

this should be all, open your browser and point it to hack4europe.net for the web frontend and hack4europe.net:9000 for the ner system. it should work without huge headaches, but if you need some help, you can drop me a message on twitter: [@grudelsud](http://twitter.com/#!/grudelsud)

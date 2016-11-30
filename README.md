# ACLS theme for Concrete 5.7 

This is created as a guide for Guy Thomas.

To run this, you should have docker and docker-compose installed. If you are not familiar with it it basically creates some kind of "mini-virtual" machines but without the resource requirements of an actual VM (these images are around 300-400MB and barely consume any extra memory). I hope you found these useful for yourself or maybe for future contracts.

My original intent for this setup was to develop more rapidly without constantly uploading the content to an FTP and in the meantime to avoid the "pollution" of my own system with services that I don't intend to use in the future (database engines, etc).

# Technical details 

This is NOT a vanilla installation of Concrete5 but an application that assumes
the existence of a database.

To start the environment type: 
```
docker-compose up
```

What this does is:
 - Starts 3 docker containers: 
   - data: Stores persistent data, namely our application dir and our DB script
   - db: a "stock" MariaDb container that hosts the database service
   - web: a custom built container that is based on a concrete5 image. It hosts the apache webserver with everything preinstalled to host a concrete5 webapp
 - The DB container executes the script at `db-script/aclsnat_c5.sql`, creating our database.
 - The web container mounts the content of the `src` directory into the `/var/www/html/application/` directory inside the web container. This means that if you edit the content of the `/src/` folder, you see the changes instantly

If everything runs without a problem, visiting `localhost` in your browser should show you the basic page of the barebone ACLS National page.

In case you want to use different database information, try not to forget to check if the user/pass/db data is matching between `docker-compose.yml` and the config file at `src/config/database.php` 

## TODO:
 - Fix the issue that after startup, concrete overrides bootstrap/app.php file with a default one
 - If you want to distribute this as a clean state setup, you have to recreate the aclsnat_c5.sql file because it contains some minimal modifications from me if I remember correctly

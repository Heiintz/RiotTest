# Installation
Go to the docker folder and run the command :
```
make up
```

3 dockers will be launched:
- riotMariadb: Database
- riotPhp: Php / Laravel
- riotNginx: Nginx server


# Fill the database
Two possible solutions:

## Install via dump.sql file

Launch the command :
```
mysql -urito -ppassword -h localhost -P 3307 riot < dump.sql
```

## Install via migrations

Launch the command :
```
php artisan migrate
```
The following tables will be created:
- users
- rotation
- failed_jobs
- migrations
- oauth_access_tokens
- oauth_auth_codes
- oauth_clients
- oauth_personal_access_clients
- oauth_refresh_tokens

These tables are used for authentication via Passport as well as rotations.

Run the command to install Passport :
```
php artisan passport:install
```
Once installed, take the information given by the command and put it in the .env file :
```
PASSWORD_CLIENT_ID="the given password client id"
PASSWORD_CLIENT_SECRET="the client secret password given"
```

Then run the command :
```
php artisan db:seed
```
This will create the default user (trynda@riot.fr).

# Routes
The different routes available can be imported into Postman via the file `docker/RIOT.postman_collection.json`

# Authentification
Route : `http://localhost:8088/api/v1/login`

This route is used to authenticate using the following information:
- email: `trynda@riot.fr`
- password: `password`

## Return of the request
```
{
    "userEmail": "trynda@riot.fr",
    "token": <GET_THE_TOKEN>,
    "expiresIn": 86400,
    "message": "You have been logged in"
}
```

# Get Platform
Route : `http://localhost:8088/api/v1/get-platform`

This route allows to retrieve the status of the different services.

## Headers
You have to add in the header:
```
Authorization: Bearer  <GET_THE_TOKEN>
```

Without this header, you will not be allowed to access the route.

## Return of the request
```
{
    "status": 200,
    "data": {
        "maintenances": [
            {
                "title": "Update Available",
                "created_at": "2022-04-25T19:42:09.789016+00:00",
                "updated_at": "2022-04-27T11:00:00+00:00",
                "author": "Riot Games",
                "message": "A new update is available! Make sure you have the latest version to crossplay with PC players!",
                "maintenance_status": "scheduled",
                "platform": [
                    "android",
                    "ios"
                ],
                "incident_severity": null
            }
        ],
        "incidents": []
    }
}
```

# Get Champion
Route : `http://localhost:8088/api/v1/get-champion`

This route allows you to retrieve the rotation of the champions of the week.

Rotations are renewed every Tuesday at 2 a.m.

If a rotation has already been saved, we return the data from the database without calling Riot's API.

## Headers
You have to add in the header:
```
Authorization: Bearer  <GET_THE_TOKEN>
```

Without this header, you will not be allowed to access the route.

## Return of the request
First rotation backup request:
```
{
    "status": 200,
    "data": {
        "freeChampionIds": [
            1,
            10,
            13,
            45,
            58,
            83,
            99,
            101,
            110,
            115,
            120,
            121,
            122,
            163,
            235,
            236
        ],
        "freeChampionIdsForNewPlayers": [
            222,
            254,
            427,
            82,
            131,
            147,
            54,
            17,
            18,
            37
        ],
        "maxNewPlayerLevel": 10
    }
}
```

If a rotation request has already been saved:
```
{
    "status": 200,
    "message": "Rotation of the week already saved on 2022-04-29 10:11:03",
    "data": {
        "freeChampionIds": [
            1,
            10,
            13,
            45,
            58,
            83,
            99,
            101,
            110,
            115,
            120,
            121,
            122,
            163,
            235,
            236
        ],
        "freeChampionIdsForNewPlayers": [
            222,
            254,
            427,
            82,
            131,
            147,
            54,
            17,
            18,
            37
        ],
        "maxNewPlayerLevel": 10
    }
}
```
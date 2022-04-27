## Installation
```
composer install
npm install
npm run dev
php artisan key:generate (si besoin de regénéré la clé)
```

## Remplir la base de données

```
php artisan migrate
php artisan seed
```

## Gestion de l'authentification:

```
php artisan passport:install
```

Prenez les infos données par la commande et mettez les dans le fichier .env:
```
PASSWORD_CLIENT_ID="le password client id donné"
PASSWORD_CLIENT_SECRET="le password client secret donné"
```

## Export

Pour faire fonctionner les exports (SQL => INTO OUTFILE), il faut entrer dans votre docker de la base de données et créer le folder de la variable EXPORT_FOLDER du .env

``` 
use Illuminate\Support\Facades\Artisan;
Artisan::call('export:FacturationOrange'); 
```
Permet d'appeler un export. Sera utile pour rappeler un export sans chercher la requete en base

# Création d'un utilisateur

Pour créer un utilisateur en base, il faut se rendre dans 
```
/var/www/html/projectbobby/
```

Lancer la commande tinker
```
php artisan tinker
```

Création dans la table users users_group_id à 1 = Finance, 2 = Opérationnel
```
DB::table('users')->insert(['name'=>'Test','email'=>'test@ifterritoires.fr','password'=>bcrypt('123456'), 'users_group_id'=>1]);
```

Récupérer l'ID de la table users ainsi créé puis lancer
```
DB::table('users_permissions')->insert([['users_id'=> USERS_ID,'permissions_id'=>1],['users_id'=> USERS_ID,'permissions_id'=>2],['users_id'=> USERS_ID,'permissions_id'=>3]]);
```

- permissions_id à 1 = Admin
- permissions_id à 2 = Supervisor
- permissions_id à 3 = Ticketing

Cela va créer les permissions pour l'utilisateur. Dans cet example, les 3 permissions seront créées. 

Si on veut créer une seule permission, il faut lancer 
```
DB::table('users_permissions')->insert(['users_id'=> USERS_ID,'permissions_id'=>1]);
```
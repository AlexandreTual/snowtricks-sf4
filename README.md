# snowtricks-sf4
Création d'un site web sur le snowboard dans le cadre de la formation PHP/ Symfony OC

Je tiens à remercier **Gauthier Burgeat** (mon mentor chez OpenclassRoom) qui m'a accompagné tout au long du projet en distillant ses conseils et en faisant preuve de patience.

###Auteur:


###Technologies:
Snowtricks requière PHP => 7.01 

Codacy [![Codacy Badge](https://api.codacy.com/project/badge/Grade/0519557ec74747b3887395a062943540)](https://app.codacy.com/app/AlexandreTual/snowtricks-sf4?utm_source=github.com&utm_medium=referral&utm_content=AlexandreTual/snowtricks-sf4&utm_campaign=Badge_Grade_Dashboard)

###Librairies
[fzaninotto/Faker
](https://github.com/fzaninotto/Faker/blob/master/readme.md#fakerproviderdatetime) utilisé pour créer des données fictives afin de travailler plus rapidement.

###Installation
Pour installer ce projet veuillez suivre les indications en tapant dans votre terminal les commandes suivantes:
-  Cloner le projet:
```sh
git clone https://github.com/AlexandreTual/snowtricks-sf4.git
```

- Mettre a jour les dépendances du projet :
```sh
composer update
```

Ouvrez le fichier .env, du projet et modifiez la ligne 27 pour mettre vos identifiants et le nom que vous souhaitez pour la base de données. Si vous utilisez un autre SGBDR veuillez vous référer à la documentation de [symfony](https://symfony.com/doc/current/doctrine.html)
```yaml
DATABASE_URL=mysql: //db_user:db_password@127.0.0.1:3306/db_name
```
modifiez également la ligne 34 pour que l'application puisse envoyer les emails. Si vous utilisez un autre système que gmail veuillez vous référer à la documentation de [Symfony](https://symfony.com/doc/4.1/email.html)
```yaml
MAILER_URL=gmail: //username:password@localhost
```
- Création de la base de donnée:
```sh
php bin/console doctrine:database:create
```

- Mise à jour des tables
```sh 
php bin/console doctrine:schema:update --force
```

- Insertion de données fictives:
```sh 
php bin/console doctrine:fixtures:load
```

Vous pouvez à présent travailler sur le projet

php bin/console doctrine:database:create

php bin/console doctrine:schema:update --force

php bin/console cache:clear

php bin/console make:user

php bin/console make:entity

php bin/console make:entity --with-uuid (crée un uuid)

php bin/console make:migration

php bin/console doctrine:migrations:migrate

php bin/console doctrine:migrations:execute DoctrineMigrations\\Version20230513115741 --up 
(ou --down)

php/bin console doctrine:migrations:list

php bin/console doctrine:migrations:status
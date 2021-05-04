# Usage

You'll need the [Symfony CLI](https://symfony.com/download), yarn and docker-compose

```bash
symfony composer install
yarn install --force 
docker-compose up -d
symfony run -d yarn encore dev --watch
symfony console doctrine:migration:migrate
symfony console app:poll
symfony serve
```

Then browse to http://localhost:8000
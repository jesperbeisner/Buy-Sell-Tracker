# Buy/Sell-Tracker

Small application for a friend who wants to track some prices in an online game.

## Setup

1. Create the database
```php
php bin/console doctrine:database:create
```

2. Load the migrations
```php
php bin/console doctrine:migrations:migrate
```
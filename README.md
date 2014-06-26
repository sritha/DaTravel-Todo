symfony2-todo
=============

[![Build Status](https://github.com/sritha/DaTravel-Todo.git)](https://github.com/sritha/DaTravel-Todo.git)

### Installation

Then download Composer (for PHP package management) to your bin dir.

```bash
mkdir ~/bin
cd ~/bin
curl -s https://getcomposer.org/installer | php
~/bin/composer.phar --version
```

You're now clear to checkout the project.

```bash
git clone https://github.com/sritha/DaTravel-Todo.git
cd DaTravel-Todo
```

You'll need to update permissions so that the app can write to the file system. If you aren't using Zend Server CE then
replace `daemon` below with your Apache Web Server User as described [here](http://symfony.com/doc/current/book/installation.html).

```bash
rm -rf app/cache/*
rm -rf app/logs/*
sudo chmod +a "daemon allow delete,write,append,file_inherit,directory_inherit" app/cache app/logs
sudo chmod +a "`whoami` allow delete,write,append,file_inherit,directory_inherit" app/cache app/logs
```

Now go ahead and install the application dependencies with Composer.

```bash
~/bin/composer.phar install
```

Now Create Database Using Doctrine

```bash
php app/console doctrine:database:create
```

Now Create Database Using Doctrine

```bash
php app/console doctrine:database:create
```

Generate Entities

```bash
php app/console doctrine:generate:entities
```

Update Schema

```bash
php app/console doctrine:schema:update --force
```


All Done .. 







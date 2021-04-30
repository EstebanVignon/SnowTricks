# Project 6 for OpenClassrooms - PHP & Symfony Path

[![Codacy Badge](https://app.codacy.com/project/badge/Grade/2de7de719bd3414c8b338663fb3a604a)](https://www.codacy.com/gh/EstebanVignon/SnowTricks/dashboard?utm_source=github.com&amp;utm_medium=referral&amp;utm_content=EstebanVignon/SnowTricks&amp;utm_campaign=Badge_Grade)

UML diagrams are inside _UML folder in de root

All pictures are downloaded from "Envato Elements" or "Twenty20" and a license is applied.

## Development environment
- PHP 7.4
- Laragon 4.0.16
- MySql 5.7
- Mail catcher : mailtrap.io
- Symfony 5.2.6
- Tailwind 2.0.3

## Personal constraints on the project
- Using design pattern in ADR instead of MVC
- Using the CSS Tailwind framework rather than Boostrap
- Do not use the AbstractController
- Do not use jQuery - Pure Javascript
- Prefer the use of DTOs rather than entity-based mapping

## Installation guide
1. Clone or download the GitHub repository in the desired folder:
```
    git clone https://github.com/EstebanVignon/SnowTricks.git
```
2. Configure your environment variables such as the connection to the database or your SMTP server or email address in 
   the `.env.local` file which should be created at the root of the project by making a copy of the `.env` file.


3. Download and install the back-end dependencies of the project:
```
    composer install
```
4. Download and install the front-end dependencies of the project with NPM:
```
    npm install
```
5. Create an asset build (using Webpack Encore) with NPM:
```
    npm run build
```
6. Create the database if it does not already exist, type the command below in the project directory:
```
    php bin/console doctrine:database:create
```
7. Create the different tables of the database by applying the migrations:
```
    php bin/console doctrine:migrations:migrate
```
8. Install fixtures to have a mock data demo - DEMO USER : username & password = root
```
    php bin/console doctrine:fixtures:load
```
9. Congratulations the project is installed correctly, you can now start using it as you like!

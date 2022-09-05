# Introduction

The assignment was made using `PHP` with `Slim` framework, `GuzzleHttp`, `phpdotenv` and `PHPUnit` 

Please execute `composer install` and `composer run start` after cloning the project

To generate the dumps containing json data, execute `composer run dumps`

Dumps will be created in `/dumps` folder which will come with dumps already.

## Install

    composer install

## Run the app

    composer run start

## Run the tests

    composer run tests

## Generate dumps

    composer run dumps

# Things to Improve

- Logging
- More test cases testing when values are empty or invalid
- Possibly use generators to improve performance when fetching orders for example

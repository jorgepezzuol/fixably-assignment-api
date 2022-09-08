# Introduction

The assignment was made using `PHP` with `Slim` framework, `GuzzleHttp`, `phpdotenv` and `PHPUnit`.

To show the final results based on the 5 tasks, it will produce `.json` dumps inside `/dumps` folder.

Related repo: https://github.com/jorgepezzuol/fixably-assignment-react

## Setup

- rename `.env.local` to `.env` and paste a valid assignment code, example: `FIXABLY_API_CODE=11111111`
- execute `composer install`
- execute `composer run start`

## Install

    composer install

## Run the app

    composer run start

## Run the tests

    composer run tests

## Generate dumps

Dumps will be created in `/dumps` folder which will come with dumps already.

    composer run dumps

## Urls

You can also see the results via browser.

- Task 2: [List statistics of orders by status](http://localhost:8888/orders)

- Task 3: [Lists all orders with an iPhone device and assigned](http://localhost:8888/orders/assigned)

- Task 4: [Invoices report](http://localhost:8888/orders/create/issue)

- Task 5: [Create order with an issue](http://localhost:8888/reports)

# Things to Improve

- Logging
- More test cases when values are empty or invalid
- Possibly use generators or multi curl to improve performance when fetching orders for example

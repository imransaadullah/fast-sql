# SecureSQLGenerator

SecureSQLGenerator is a PHP class designed to provide a secure and flexible way to generate SQL queries. It supports various SQL operations, parameter binding to prevent SQL injection, transactions, and caching for improved performance.

## Installation

You can install SecureSQLGenerator using Composer. Add the following to your `composer.json` file:

```json
{
    "require": {
        "progrmanial/fast-sql": "dev-master"
    }
}
```

## Then run:

```bash
composer install
```

## Usage

```php
<?php

use Your\Namespace\SecureSQLGenerator;
use PDO;

// Create a PDO instance for database connection
$pdo = new PDO('mysql:host=localhost;dbname=your_database', 'username', 'password');

// Create an instance of SecureSQLGenerator
$sqlGenerator = new SecureSQLGenerator($pdo);

// Example: SELECT operation
$result = $sqlGenerator
    ->select(['id', 'name'])
    ->from('users')
    ->where(['status' => 'active'])
    ->execute();

// Example: INSERT operation
$sqlGenerator
    ->insert('products', ['name' => 'Product A', 'price' => 29.99])
    ->execute();

// Example: JOIN operation
$result = $sqlGenerator
    ->select(['users.id', 'users.name', 'orders.total'])
    ->from('users')
    ->innerJoin('orders', ['users.id' => 'orders.user_id'])
    ->execute();

// ... (more examples)

```

## Features

- **Secure:** Uses parameter binding to prevent SQL injection.
- **Flexible:** Supports various SQL operations, including SELECT, INSERT, UPDATE, DELETE, and JOIN.
- **Transaction Management:** Provides methods for starting, committing, and rolling back transactions.
- **Caching:** Optionally caches query results for improved performance.
- **Set Operations:** Supports INTERSECT, UNION, and EXCEPT set operations.

## Contributing

If you find any issues or have suggestions for improvements, feel free to open an issue or submit a pull request.

## License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.
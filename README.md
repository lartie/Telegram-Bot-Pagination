# Telegram Bot Pagination

[![Latest Stable Version](https://poser.pugx.org/lartie/telegram-bot-pagination/v/stable)](https://packagist.org/packages/lartie/telegram-bot-pagination)
[![Total Downloads](https://poser.pugx.org/lartie/telegram-bot-pagination/downloads)](https://packagist.org/packages/lartie/telegram-bot-pagination)
[![Latest Unstable Version](https://poser.pugx.org/lartie/telegram-bot-pagination/v/unstable)](https://packagist.org/packages/lartie/telegram-bot-pagination)
[![License](https://poser.pugx.org/lartie/telegram-bot-pagination/license)](https://packagist.org/packages/lartie/telegram-bot-pagination)
[![composer.lock](https://poser.pugx.org/lartie/telegram-bot-pagination/composerlock)](https://packagist.org/packages/lartie/telegram-bot-pagination)

- [Installation](#installation)
    - [Composer](#composer)
    - [Configuration](#configuration)
- [Usage](#usage)
    - [Test Data](#test-data)
    - [How To Use](#how-to-use)
    - [Result](#result)
- [License](#license)

## Installation

### Composer
```bash
composer require "lartie/telegram-bot-pagination:^1.0.0"
```

## Usage

### Test Data
```php
$items = range(1, 100); 
$command = 'testCommand'; // optional. Default: pagination
$selectedPage = 10; // optional. Default: 1
```

### How To Use
```php

$cqPagination = new CallbackQueryPagination($items, $command);
$cqPagination->setMaxButtons(6);
$cqPagination->setWrapSelectedButton('< #VALUE# >');
    
$pagination = $cqPagination->pagination($selectedPage); //$cqPagination->setSelectedPage($selectedPage);

```

### Result
```php
if (!empty($paginate['keyboard'])) {
    $paginate['keyboard'][0]['callback_data']; // testCommand?currentPage10=&nextPage=1
    $paginate['keyboard'][1]['callback_data']; // testCommand?currentPage10=&nextPage=9
    ...
    
    $response = [
        'reply_markup' => json_encode([
            'inline_keyboard' => [
                $paginate['keyboard'],
            ],
        ]),
    ];
}
```

## Code Quality

Run the PHPUnit tests with PHPUnit. 

    phpunit tests/


## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

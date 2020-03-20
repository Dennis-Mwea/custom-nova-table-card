# Nova Custom Table Card

## Simple Nova Card for Custom Tables

Simple card table with data of you choice.

It can be useful as latest order list or latest posts, users, ...

Empty table:
![Nova Custom Empty Table Card](https://github.com/Dennis-Mwea/custom-nova-table-card/blob/master/EmptyTable.png)

Table with data:
![Nova Custom Table Card](https://github.com/Dennis-Mwea/custom-nova-table-card/blob/master/TableWithData.png)

 ## Installation

You can install the package in to a Laravel app that uses [Nova](https://nova.laravel.com) via composer:

```bash
composer require dytechltd/custom-table
```

You must register the card with NovaServiceProvider.

```php
// in app/Providers/NovaServiceProvder.php

// ...
public function cards()
{
    return [
        // ...

        // all the parameters are required excelpt title
        new \Dytechltd\CustomTable\CustomTable(
            array $header, array $data, string $title
        ),
    ];
}
```

Example of use:

```php
// ...
public function cards()
{
    return [
        // ...

        // all the parameters are required
        new \Dytechltd\CustomTable\CustomTable(
            [
                new \Dytechltd\CustomTable\Table\Cell('Order Number'),
                (new \Dytechltd\CustomTable\Table\Cell('Price'))->class('text-right'),
            ], // header
            [
                (new \Dytechltd\CustomTable\Table\Row(
                    new \Dytechltd\CustomTable\Table\Cell('2018091001'),
                    (new \Dytechltd\CustomTable\Table\Cell('20.50'))->class('text-right')->id('price-2')
                ))->viewLink('/resources/orders/1'),
                (new \Dytechltd\CustomTable\Table\Row(
                    new \Dytechltd\CustomTable\Table\Cell('2018091002'),
                    (new \Dytechltd\CustomTable\Table\Cell('201.25'))->class('text-right')->id('price-2')
                )),
            ], // data
            'Orders' //title
        ),
    ];
}
```

or:

```php
// ...
public function cards()
{
    return [
        // ...

        // all the parameters are required except title
        (new \Dytechltd\CustomTable\CustomTableCard)
            ->header([
                new \Dytechltd\CustomTable\Table\Cell('Order Number'),
                (new \Dytechltd\CustomTable\Table\Cell('Price'))->class('text-right'),
            ])
            ->data([
                (new \Dytechltd\CustomTable\Table\Row(
                    new \Dytechltd\CustomTable\Table\Cell('2018091001'),
                    (new \Dytechltd\CustomTable\Table\Cell('20.50'))->class('text-right')->id('price-2')
                ))->viewLink('/resources/orders/1'),
                (new \Dytechltd\CustomTable\Table\Row(
                    new \Dytechltd\CustomTable\Table\Cell('2018091002'),
                    (new \Dytechltd\CustomTable\Table\Cell('201.25'))->class('text-right')->id('price-2')
                )),
            ])
            ->title('Orders')
            ->refresh(5), // If you need refresh your card data (in seconds)
    ];
}
```

or:

You can create your own class which will extend \Mako\CustomTableCard\CustomTableCard in Nova/Cards directory on example.

In this separate class you are able to fetch data from models in nice clean way.

```php
<?php

namespace App\Nova\Cards;

use App\Models\User;

class UnverifiedUsers extends \Dytechltd\CustomTable\CustomTableCard
{
    public function __construct()
    {
        $header = collect(['ID', 'NAME', 'EMAIL', 'PHONE NUMBER', 'INTRODUCER', 'VERIFIED', 'LAST LOGIN AT']);
        
        $this->title('Unverified Users');
        $users = User::whereNull('email_verified_at')
            ->get();

        $this->header($header->map(function ($value) {
            return new Cell($value);
        })->toArray());

        $this->data(collect($users)->map(function ($user) {
            return (new Row(
                new Cell($user->id),
                new Cell($user->name),
                new Cell($user->email),
                new Cell($user->phone_number),
                new Cell($user->introducer ? $user->introducer->name : '--'),
                new Cell($user->email_verified_at),
                new Cell($user->last_login_at)
            ))->viewLink("resources/users/{$user->id}");
        })->toArray());
    }
}
```

Then register your custom class inside cards in NovaServiceProvider.php
```php
protected function cards()
{
    return [
        ......
        new \App\Nova\Cards\UnverifiedUsers
     ];
 }
```

Note: If you don't specify view, show icon will not be visible.


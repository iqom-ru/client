iQom.ru API client
==================

Клиент для работы с API iqom.ru


## Установка

```
composer require iqom/iqom-client
```


## Использование

```php
use Iqom\IqomClient\IqomClient;
use Iqom\IqomClient\IqomRequest;

/**
* Здесь:
* https — протокол подключения
* v1 - версия протокола API
* API_KEY - ваш ключ доступа
* api.iqom.com — домен API
* timeout - таймаут для подключения
*/
$iqom = new IqomClient('https://v1:API_KEY@api.iqom.com/?timeout=5');
$response = $iqom->makeRequest(
    IqomRequest::makeTestRequest()
);

echo sprintf(
    "Request id: %s\nStatus: %s\nError: %s\nData:\n",
    $response->getId(),
    $response->getStatus(),
    $response->getError()
)
var_dump($response->getData());
```

## Перевод

### IqomRequest::makeTranslationWriteRequest

Необходима для отправки имеющихся переводов текстов на сервер. При этом:
- если в отправляемом массиве есть перевод, а на сервере его нет, он на сервере создаётся
- если есть и там, и там, на сервере он перезаписывается
- если в отправлении нет перевода, а на сервере он есть, он просто остаётся

Вызов:
```php
IqomRequest::makeTranslationWriteRequest(string $data)
```

Структура данных:
```php
$data = [
    'ru' => [ // Локали
        'messages' => [ // Домены
            'hello.name' => 'Привет, %друг%!',  // Метка => сообщене
            'logout' => 'logout'                // Пока ещё не переведено
        ],
        'forms' => [ // Домены
            'hello.name' => 'hello.name'        // Пока ещё не переведено
                                                // logout вообще отсутствует
        ]   
    ],
    'en' => [ // Локали
                                                // Аналогично для локали en
    ]
]
```

### IqomRequest::makeTranslationReadRequest

Необходим для получения имеющихся переводов текстов с сервера.

Вызов:
```php
IqomRequest::makeTranslationReadRequest(array $domains, array $locales)
```

Структура данных:
```php
// Список доменов
$domains = [
    'messages',
    'forms'
];

// Список локалей
$locales = [
    'ru',
    'en'
];

// Ответ с сервера
[
    'ru' => [ // Локали
        'messages' => [ // Домены
            'hello.name' => 'Привет, %друг%!',  // Метка => сообщене
            'logout' => 'logout'                // Пока ещё не переведено
        ],
        'forms' => [ // Домены
            'hello.name' => 'hello.name'        // Пока ещё не переведено
        ]   
    ],
    'en' => [ // Локали
                                                // Аналогично для локали en
    ]
]
```

### IqomRequest::makeTranslationDeleteRequest

Необходим для удаления лишних ключей с сервера

Вызов:
```php
IqomRequest::makeTranslationDeleteRequest(array $data)
```

Структура данных:
```php
[
    'ru' => [ // Локали
        'messages' => [ // Домены
            'hello.name',
            'logout'
        ],
        'forms' => [    // Домены
            'hello.name'
        ]   
    ],
    'en' => [ // Локали
                        // Аналогично для локали en
    ]
]
```

# freegpt
Use chatgptdemo.info API to action with ChatGPT from PHP for free!

## How to use

There are two ways to use freegpt library:

### The simple way (in just two lines of code)

1. At first, create your php script and import freegpt.php like this:
```php
require_once 'freegpt.php'
```

2. Then, call the askGPT function from the freegpt and get the result:
```php
$result = freegpt::askGPT("Hello, World!");
// or, if you already have user and/or chat id(s):
$result = freegpt::askGPT("Hello, World!", $user_id, $chat_id);
```

Done!

### A little bit harder way ()

1. Same as the previous way, you also need to create your own script and import freegpt.php like this:
```php
require_once 'freegpt.php';
```

2. Then, create new instance of the freegpt class:
```php
$freegpt = new freegpt();
// or, if you already have user and/or chat id(s):
$freegpt = new freegpt($user_id, $chat_id);
```

3. After this, you can call the askGPT with history saving:
```php
$freegpt->askGPT("Hello, World!");
```

Done!

## A little bit more functional

- Create the new user and chat id on the chatgptdemo.info:
```php
$user_id = freegpt::create_user_id();
$chat_id = freegpt::create_chat_id($user_id);
```

- Get and set user and chat id of the freegpt class instance:
```php
$freegpt::$user_id = $my_cool_user_id;
$freegpt::$chat_id = $my_cool_chat_id;
```

- This script also stores all another urls from the chatgptdemo.info in the constants, so you can find and edit them in the top of the script:
```php
define('FREEGPT_BASE_URL', 'https://chatgptdemo.info/chat/');

define('FREEGPT_NEW_CHAT_URL', FREEGPT_BASE_URL . 'new_chat');
define('FREEGPT_GET_CHAT_URL', FREEGPT_BASE_URL . 'get_chat');
define('FREEGPT_UPDATE_CHAT_NAME_URL', FREEGPT_BASE_URL . 'update_chat_name');
define('FREEGPT_DELETE_CHAT_URL', FREEGPT_BASE_URL . 'delete_chat');
define('FREEGPT_GET_USER_CHAT_URL', FREEGPT_BASE_URL . 'get_user_chat');
define('FREEGPT_UPDATE_MESSAGES_URL', FREEGPT_BASE_URL . 'update_messages');
define('FREEGPT_UPDATE_SHARE_CHAT_URL', FREEGPT_BASE_URL . 'update_share_chat');
define('FREEGPT_CHAT_API_STREAM_URL', FREEGPT_BASE_URL . 'chat_api_stream');
```

## Limitations

Because of limitations from the freegptdemo.info API, you can ask only about 10-15 questions per day. After this, you will start getting `401 Unauthorized` and `429 Too Many Requests` http errors.

You can try to use proxy to bypass this, but I didn't test it yet.

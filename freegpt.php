<?php

define('FREEGPT_BASE_URL', 'https://chatgptdemo.info/chat/');

define('FREEGPT_NEW_CHAT_URL', FREEGPT_BASE_URL . 'new_chat');
define('FREEGPT_GET_CHAT_URL', FREEGPT_BASE_URL . 'get_chat');
define('FREEGPT_UPDATE_CHAT_NAME_URL', FREEGPT_BASE_URL . 'update_chat_name');
define('FREEGPT_DELETE_CHAT_URL', FREEGPT_BASE_URL . 'delete_chat');
define('FREEGPT_GET_USER_CHAT_URL', FREEGPT_BASE_URL . 'get_user_chat');
define('FREEGPT_UPDATE_MESSAGES_URL', FREEGPT_BASE_URL . 'update_messages');
define('FREEGPT_UPDATE_SHARE_CHAT_URL', FREEGPT_BASE_URL . 'update_share_chat');
define('FREEGPT_CHAT_API_STREAM_URL', FREEGPT_BASE_URL . 'chat_api_stream');

class freegpt {
    public static $user_id = null;
    public static $chat_id = null;

    /**
     * Initialize a new instance of the freegpt class.
     *
     * @param string|null $user_id The user ID. If not provided, it is created automatically.
     */
    public function __construct($user_id = null, $chat_id = null) {
        self::$user_id = $user_id ?? self::create_user_id();
        self::$chat_id = $chat_id ?? self::create_chat_id(self::$user_id);
    }

    /**
     * Generate a new user ID by fetching data from the base URL and parsing the HTML.
     *
     * @return string The user ID.
     * @throws Exception If fetching or parsing fails.
     */
    public static function create_user_id() {
        try {
            $html = file_get_contents(FREEGPT_BASE_URL);
            $dom = new DOMDocument();
            @$dom->loadHTML($html);
            $userIdElement = $dom->getElementById('USERID');
            if ($userIdElement) {
                return $userIdElement->textContent;
            } else {
                throw new Exception("USERID element not found.");
            }
        } catch (Exception $e) {
            throw new Exception("[FREEGPT] Failed to create user id: " . $e->getMessage());
        }
    }

    /**
     * Generate a new chat ID using a POST request with the given user ID.
     *
     * @param string $user_id The user ID.
     * @return string The chat ID.
     * @throws Exception If the request fails or the response is invalid.
     */
    public static function create_chat_id($user_id) {
        $response = self::make_post_request(FREEGPT_NEW_CHAT_URL, ['user_id' => $user_id]);
        if (!$response) {
            throw new Exception("[FREEGPT] Failed to create chat id");
        }

        $data = json_decode($response, true);
        if (isset($data['id_'])) {
            return $data['id_'];
        } else {
            throw new Exception("[FREEGPT] Failed to get chat id from json");
        }
    }

    /**
     * Ask GPT a question and return the response.
     *
     * @param string $question The question to ask.
     * @param string|null $user_id Optional user ID.
     * @param string|null $chat_id Optional chat ID.
     * @return string|null The response from the GPT or null if failed.
     * @throws Exception If an unexpected finish reason is encountered.
     */
    public static function askGPT($question, $user_id = null, $chat_id = null) {
        $user_id = $user_id ?? (self::$user_id ?? self::create_user_id());
        $chat_id = $chat_id ?? (self::$chat_id ?? self::create_chat_id($user_id));

        $response = self::make_post_request(FREEGPT_CHAT_API_STREAM_URL, [
            'chat_id' => $chat_id,
            'question' => $question,
            'timestamp' => time(),
        ]);

        if (!$response) {
            return null;
        }

        $answer = "";
        $chunks = explode("\n\n", $response);
        foreach ($chunks as $chunk) {
            $data = json_decode(substr($chunk, 6), true);
            if (isset($data['choices'][0]['delta']['content'])) {
                $answer .= $data['choices'][0]['delta']['content'];
            } else if (isset($data['choices'][0]['finish_reason']) && $data['choices'][0]['finish_reason'] != "stop") {
                throw new Exception("[FREEGPT] Unexpected finish reason: " . $data['choices'][0]['finish_reason']);
            }
        }

        return $answer;
    }

    /**
     * Make a POST request and return the server's response.
     *
     * @param string $url The target URL.
     * @param array $data The data to send.
     * @return string|false The response or false on failure.
     */
    private static function make_post_request($url, $data) {
        $options = [
            'http' => [
                'header' => "Content-Type: application/json\r\n",
                'method' => 'POST',
                'content' => json_encode($data),
            ],
        ];

        $context = stream_context_create($options);
        return file_get_contents($url, false, $context);
    }
}

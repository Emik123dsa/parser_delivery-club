<?php

return [
    "0" => "X-User-Authorization: " .
    file_get_contents(__DIR__ . DS . "token.txt"),

    "1" => "Sec-Fetch-Dest: empty",
    "2" => "Sec-Fetch-Mode: cors",
    "3" => "Sec-Fetch-Site: same-site",
    "4" => "User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Safari/537.36",
    "5" => "Cookie: " . file_get_contents(__DIR__ . DS . ".." . DS . ".." . DS . '..' . DS . "thief" . DS . "cookies.txt"),
    "7" => "Accept: application/json, text/plain, */*",
    "8" => "Accept-Language: ru-RU,ru;q=0.9,en-US;q=0.8,en;q=0.7",
];
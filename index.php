<head>
    <meta charset="utf-8">
    <title>Страничка</title>
</head>
<body>
<?php
/**
 * Created by PhpStorm.
 * User: Валерий
 * Date: 29.07.2016
 * Time: 23:23
 */
if (isset($_COOKIE['token']) && $_COOKIE['version'] == '1') {
    $token = $_COOKIE['token'];
    $myId = $_COOKIE['myId'];
} else {
    header('Location: https://oauth.vk.com/authorize?client_id=5567992&display=popup&redirect_uri=http://test0302.zzz.com.ua/vk&scope=offline,audio&response_type=code&v=5.53');
    $code = $_GET['code'];

    $request_params = array(
        'client_id' => '5567992',
        'client_secret' => 'ZZkEb7JalwZe6oU8mXcd',
        'redirect_uri' => 'http://test0302.zzz.com.ua/vk',
        'code' => $code
    );
    $get_params = http_build_query($request_params);
    $result = json_decode(file_get_contents('https://oauth.vk.com/access_token?' . $get_params));
    $token = $result->access_token;
    setcookie("token", $token, time() + 3600 * 24 * 30);
    $myId = $result->user_id;
    setcookie("myId", $myId, time() + 3600 * 24 * 30);
    setcookie("version", '1', time() + 3600 * 24 * 30);
}

if (isset($_GET['code'])) {
    header('Location: http://test0302.zzz.com.ua/vk');
    exit();
}

$request_params = array(
    'user_id' => $myId,
    'fields' => 'first_name, last_name',
    'access_token' => $token
);
$get_params = http_build_query($request_params);
$result = json_decode(file_get_contents('https://api.vk.com/method/users.get?' . $get_params));

$audio_params = array(
    'owner_id' => $myId,
    'v' => '5.53',
    'access_token' => $token
);
$get_params = http_build_query($audio_params);
$audio = json_decode(file_get_contents('https://api.vk.com/method/audio.get?' . $get_params));
$audioCount = $audio->response->count;


if ($token) {
    echo "<h3>Твой id: $myId </h3>";
    $name = $result->response[0]->first_name . " " . $result->response[0]->last_name;
    echo "<h3>Ты <span style='color:red'>$name</span></h3>";
    echo "<h3>Песен найдено: <span style='color:blue'>$audioCount</span></h3> <br>";
    for ($i = 0; $i < $audioCount; $i++) {
        $mp3Title = $audio->response->items[$i]->artist." - ".$audio->response->items[$i]->title;
        $mp3 =  $audio->response->items[$i]->url;
        echo "<p>  $mp3Title </p>";
        echo "<audio controls><source src='$mp3' type='audio/mpeg'></audio>";
    }
}


?>


</body>

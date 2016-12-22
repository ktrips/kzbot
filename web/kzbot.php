<?php

$json_string = file_get_contents('php://input');
$json_object = json_decode($json_string);

foreach ($json_object->events as $event) {
    if('message' == $event->type){
        api_post_request($event->replyToken, $event->message->text);
    }else if('beacon' == $event->type){
        api_post_request($event->replyToken, 'BEACONイベント!!');
    }
}


function api_post_request($token, $message) {
    $url = 'https://api.line.me/v2/bot/message/reply';
    $channel_access_token = 'Qw0I9IoxJ9S6QZRYXu7MF/onE3fSaGXjOZ5X9o8NjJUXqgDmbgj7pE8e8GY2RPzX7qJnOeVNkR+lm7SVpeaJroSiaV5clYnZ7fGadSn0j8OyqSp3prt7MjeWET4NB+N1LcnVCxe0A4IefmvRgjyQVgdB04t89/1O/w1cDnyilFU=';
    $headers = array(
        'Content-Type: application/json',
        "Authorization: Bearer {$channel_access_token}"
    );
    $post = array(
        'replyToken' => $token,
        'messages' => array(
            array(
                'type' => 'text',
                'text' => $message
            )
        )
    );

    $curl = curl_init($url);
    curl_setopt($curl, CURLOPT_POST, true);
    curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($post));
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_exec($curl);
}

//ドコモの雑談APIから雑談データを取得
//function chat($text) {
    // docomo chatAPI
    //$api_key = '5752424f45756b376e484969564c7562354b3852784c6b45526a4a4c646b766f4251312e4b555a49475a37';
    //$api_key = '【docomoのAPI Keyを使用する】';
    //$api_url = sprintf('https://api.apigw.smt.docomo.ne.jp/dialogue/v1/dialogue?APIKEY=%s', $api_key);
    //$req_body = array('utt' => $text);

    //$headers = array(
    //    'Content-Type: application/json; charset=UTF-8',
    //);
    //$options = array(
    //    'http'=>array(
    //        'method'  => 'POST',
    //        'header'  => implode("\r\n", $headers),
    //        'content' => json_encode($req_body),
    //        )
    //    );
    //$stream = stream_context_create($options);
    //$res = json_decode(file_get_contents($api_url, false, $stream));

    //return $res->utt;
//}

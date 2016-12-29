<?php
//require_once __DIR__.'/vendor/autoload.php';
$json_string = file_get_contents('php://input');
$json_object = json_decode($json_string);

foreach ($json_object->events as $event) {
    // Redis connection
    //$redis = new Predis\Client(getenv('REDIS_URL'));
    //$from = $event->message->from;
    //$event_message = $event->message->text;
    //$context = $redis->get($from);
    //$response = chat($text, $context);
    // save context to Redis
    //$redis->set($from, $response->context);
    
    //$res_content = $event->message;
    //$event_message = $response;
    $docomo_message = chat($text);
    if('message' == $event->type){
        api_post_request($event->replyToken, $docomo_message);//$event->message->text);
    }else if('beacon' == $event->type){
        api_post_request($event->replyToken, 'BEACONが近くに来たよ！');
    }
}

//docomo返信
//$message = chat($text);

function api_post_request($token, $message) {
    $url = 'https://api.line.me/v2/bot/message/reply';
    $channel_access_token = getenv('LINE_CHANNEL_ACCESS_TOKEN');
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
function chat($text) {
    // docomo chatAPI
    $api_key = getenv('DOCOMO_API_KEY');
    //$api_key = '【docomoのAPI Keyを使用する】';
    $api_url = sprintf('https://api.apigw.smt.docomo.ne.jp/dialogue/v1/dialogue?APIKEY=%s', $api_key);
    $req_body = array('utt' => $text);
        //'context' => $context);
    //$req_body['context'] = $text;

    $headers = array(
        'Content-Type: application/json; charset=UTF-8',
    );
    $options = array(
        'http'=>array(
            'method'  => 'POST',
            'header'  => implode("\r\n", $headers),
            'content' => json_encode($req_body),
            )
        );
    $stream = stream_context_create($options);
    $res = json_decode(file_get_contents($api_url, false, $stream));

    return $res->utt;
}

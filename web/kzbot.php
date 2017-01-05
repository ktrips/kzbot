<?php
//require_once('/app/vendor/autoload.php');
//use jp3cki\docomoDialogue\Dialogue;
//require_once __DIR__.'/vendor/autoload.php';
//use Symfony\Component\HttpFoundation\Request;
//date_default_timezone_set('Asia/Tokyo');
// メッセージ受信
$json_string = file_get_contents('php://input');
$json_object = json_decode($json_string);
// アカウント情報設定

$proxy    = getenv('FIXIE_URL');
$redisUrl = getenv('REDIS_URL');
$gvision  = getenv('GOOGLE_VISION_KEY');

//$content      = $json_object->result{0}->content;
//$text         = $content->text;
//$from         = $content->from;
//$message_id   = $content->id
//$content_type = $content->contentType;

// $contextの設定
//$redis   = new Predis\Client($redisUrl);
//$context = $redis->get($from);
//$dialog = new Dialogue($docomoApiKey);

//Docomo  送信パラメータの準備
//$dialog->parameter->reset();
//$dialog->parameter->utt = $text;
//$dialog->parameter->t = 20;
//$dialog->parameter->context = $context;
//$dialog->parameter->mode = $mode;

//$ret = $dialog->request();

foreach ($json_object->events as $event) {
    // get context from Redis
    //$redis = new Predis\Client(getenv('REDIS_URL'));
    //$redis = new Predis\Client(getenv('HEROKU_REDIS_GREEN_URL'));
    $content=$event->message;
    $type  = $event->message->type;
    $from  = $event->message->from;
    $message= $event->message->text;
    
    //$context = $redis->get($from);
    // chat API
    //$response = dialogue($message, $context);
    // save context to Redis
    //$redis->set($from, $response->context);

    //$res_content = $content; //$msg['content'];
    //$res_content['text'] = $response;
    
    $docomo_message = chat($text);
    if('message' == $event->type){
        api_post_request($event->replyToken, $docomo_message);//$event->message->text);
    }else if('beacon' == $event->type){
        api_post_request($event->replyToken, 'BEACONが近くに来たよ！');
    }
}

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
    //curl_setopt($curl, CURLOPT_PROXY, getenv('FIXIE_URL'));
    curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($post));
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_exec($curl);
}

//ドコモの雑談APIから雑談データを取得
function chat($text) {
    //$api_key = '【docomoのAPI Keyを使用する】';
    $api_key  = getenv('DOCOMO_API_KEY');
    $api_url = sprintf('https://api.apigw.smt.docomo.ne.jp/dialogue/v1/dialogue?APIKEY=%s', $api_key);
    $req_body = array('utt' => $text);

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

function dialogue($text, $context) {
    //$api_key = '【docomoのAPI Keyを使用する】';
    $api_key  = getenv('DOCOMO_API_KEY');
    $api_url  = sprintf('https://api.apigw.smt.docomo.ne.jp/dialogue/v1/dialogue?APIKEY=%s', $api_key);
    $req_body = array(
        'utt' => $text,
        'context' => $context,
    );
    $req_body['context'] = $text;
    
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

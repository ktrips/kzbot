<?php

#require_once('/app/vendor/autoload.php');
#use jp3cki\docomoDialogue\Dialogue;

// アカウント情報設定
$accessToken = getenv('LINE_CHANNEL_ACCESS_TOKEN');
$proxy         = getenv('FIXIE_URL');
$docomoApiKey  = getenv('DOCOMO_API_KEY');
$redisUrl      = getenv('REDIS_URL');

// メッセージ受信
#$json_string  = file_get_contents('php://input');
#$json_object  = json_decode($json_string);
#$content      = $json_object->result{0}->content;
#$text         = $content->text;
#$from         = $content->from;
#$message_id   = $content->id;
#$content_type = $content->contentType;

// $contextの設定
#$redis   = new Predis\Client($redisUrl);
#$context = $redis->get($from);

#$dialog = new Dialogue($docomoApiKey);

//Docomo  送信パラメータの準備
#$dialog->parameter->reset();
#$dialog->parameter->utt = $text;
#$dialog->parameter->t = 20;
#$dialog->parameter->context = $context;
#$dialog->parameter->mode = $mode;

#$ret = $dialog->request();

#if ($ret === false) {
#    $text = "通信に失敗しました";
#}

#$text = $ret->utt;
#$redis->set($from, $ret->context);


//ユーザーからのメッセージ取得
$json_string = file_get_contents('php://input');
$jsonObj = json_decode($json_string);

$type = $jsonObj->{"events"}[0]->{"message"}->{"type"};
//メッセージ取得
$text = $jsonObj->{"events"}[0]->{"message"}->{"text"};
//ReplyToken取得
$replyToken = $jsonObj->{"events"}[0]->{"replyToken"};

//メッセージ以外のときは何も返さず終了
if($type != "text"){
    exit;
}

//docomo返信
$response = chat($text);

//返信データ作成
$response_format_text = [
    "type" => "text",
    "text" => $response
    ];

$post_data = [
    "replyToken" => $replyToken,
    "messages" => [$response_format_text]
    ];

$ch = curl_init("https://api.line.me/v2/bot/message/reply");
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($post_data));
curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    'Content-Type: application/json; charser=UTF-8',
    'Authorization: Bearer ' . $accessToken
    ));
$result = curl_exec($ch);
curl_close($ch);

#$docomoApiKey  = getenv('DOCOMO_API_KEY');

//ドコモの雑談APIから雑談データを取得
function chat($text) {
    // docomo chatAPI
    #$api_key = '【docomoのAPI Keyを使用する】';
    $docomoApiKey  = getenv('DOCOMO_API_KEY');
    $api_url = sprintf('https://api.apigw.smt.docomo.ne.jp/dialogue/v1/dialogue?APIKEY=%s', $docomoApiKey);
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

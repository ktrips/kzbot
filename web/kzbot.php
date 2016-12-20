<?php

require_once('/app/vendor/autoload.php');
use jp3cki\docomoDialogue\Dialogue;

// アカウント情報設定
$channelId     = getenv('LINE_CHANNEL_ID');
$channelSecret = getenv('LINE_CHANNEL_SECRET');
$mid           = getenv('LINE_CHANNEL_MID');
$proxy         = getenv('FIXIE_URL');
$docomoApiKey  = getenv('DOCOMO_API_KEY');
$redisUrl      = getenv('REDIS_URL');

// メッセージ受信
$json_string  = file_get_contents('php://input');
$json_object  = json_decode($json_string);
$content      = $json_object->result{0}->content;
$text         = $content->text;
$from         = $content->from;
$message_id   = $content->id;
$content_type = $content->contentType;

// $contextの設定
$redis   = new Predis\Client($redisUrl);
$context = $redis->get($from);

$dialog = new Dialogue($docomoApiKey);

//Docomo  送信パラメータの準備
$dialog->parameter->reset();
$dialog->parameter->utt = $text;
$dialog->parameter->t = 20;
$dialog->parameter->context = $context;
$dialog->parameter->mode = $mode;

$ret = $dialog->request();

if ($ret === false) {
    $text = "通信に失敗しました";
}

$text = $ret->utt;
$redis->set($from, $ret->context);

$post = <<< EOM
{
    "to":["{$from}"],
    "toChannel":1383378250,
    "eventType":"138311608800106203",
    "content":{
        "toType":1,
        "contentType":1,
        "text": "{$text}"
    }
}
EOM;

$headers = array(
    "Content-Type: application/json",
    "X-Line-ChannelID: {$channelId}",
    "X-Line-ChannelSecret: {$channelSecret}",
    "X-Line-Trusted-User-With-ACL: {$mid}"
);

$url = "https://trialbot-api.line.me/v1/events";

$curl = curl_init($url);
curl_setopt($curl, CURLOPT_POST, true);
curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
curl_setopt($curl, CURLOPT_POSTFIELDS, $post);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
//プロキシ経由フラグ
curl_setopt($curl, CURLOPT_HTTPPROXYTUNNEL, 1);
//プロキシ設定
curl_setopt($curl, CURLOPT_PROXY, $proxy);
$output = curl_exec($curl);

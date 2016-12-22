$accessToken = 'Qw0I9IoxJ9S6QZRYXu7MF/onE3fSaGXjOZ5X9o8NjJUXqgDmbgj7pE8e8GY2RPzX7qJnOeVNkR+lm7SVpeaJroSiaV5clYnZ7fGadSn0j8OyqSp3prt7MjeWET4NB+N1LcnVCxe0A4IefmvRgjyQVgdB04t89/1O/w1cDnyilFU=';
 
//ユーザーからのメッセージ取得
$json_string = file_get_contents('php://input');
$jsonObj = json_decode($json_string);
 
$type = $jsonObj--->{"events"}[0]->{"message"}->{"type"};
$text = $jsonObj->{"events"}[0]->{"message"}->{"text"};
$replyToken = $jsonObj->{"events"}[0]->{"replyToken"};
 
 
//ドコモの雑談データ取得
$response = chat($text);
 
$response_format_text = [
    "type" => "text",
    "text" =>  $response
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
 
 
//ドコモの雑談APIから雑談データを取得
function chat($text) {
    // docomo chatAPI
    $api_key = '5752424f45756b376e484969564c7562354b3852784c6b45526a4a4c646b766f4251312e4b555a49475a37';
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

<?php
$accessToken = getenv('LINE_CHANNEL_ACCESS_TOKEN');
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
//返信データ作成
if ($text == 'はい') {
  $response_format_text = [
    "type" => "template",
    "altText" => "あなた様の夢を教えて下さい",
    "template" => [
      "type" => "buttons",
      "thumbnailImageUrl" => "https://" . $_SERVER['SERVER_NAME'] . "/img1.jpg",
      "title" => "あなた様の夢を教えて下さい",
      "text" => "いつかこんな一戸建ての家を建てたい",
      "actions" => [
          [
            "type" => "message",
            "label" => "資金プランを見てみる",
            "text" => "資金プラン"
          ],
          [
            "type" => "postback",
            "label" => "とりあえず電話する",
            "data" => "action=pcall&itemid=123"
          ],
          [
            "type" => "uri",
            "label" => "詳しく見る",
            "uri" => "https://" . $_SERVER['SERVER_NAME'] . "/"
          ],
          [
            "type" => "message",
            "label" => "もっと大それた夢？",
            "text" => "次も見てみる"
          ]
      ]
    ]
  ];
} else if ($text == 'いいえ' or $text == 'NObo') {
  $response_format_text = [
    "type" => "template",
    "altText" => "またお声がけください",
    "template" => [
	"thumbnailImageUrl" => "https://" . $_SERVER['SERVER_NAME'] . "/IMG_1434.PNG",
        "type" => "confirm",
        "text" => "またお声がけください！",
    ]
  ];
} else if ($text == 'お部屋') {
  $response_format_text = [
    "type" => "template",
    "altText" => "お部屋の状況をチェックしますか？（はいKObo!／いいえNObo!）",
    "template" => [
	"thumbnailImageUrl" => "https://" . $_SERVER['SERVER_NAME'] . "/IMG_1439.PNG",
        "type" => "confirm",
        "text" => "お部屋の状況をチェックしますか？",
        "actions" => [
            [
              "type" => "message",
              "label" => "KObo!",
              "text" => "KObo",
	      "uri" => "https://" . $_SERVER['SERVER_NAME'] . "/"
            ],
            [
              "type" => "message",
              "label" => "NObo!",
              "text" => "NObo"
            ]
        ]
    ]
  ];
} else if ($text == 'KObo') {
  $response_format_text = [
    "type" => "template",
    "altText" => "お部屋の状況",
    "template" => [
      "type" => "buttons",
      "thumbnailImageUrl" => "https://" . $_SERVER['SERVER_NAME'] . "/IMG_1431.PNG",
      "title" => "お部屋の何をチェックしたいですか？",
      "text" => "お部屋のチェック",
      "actions" => [
          [
            "type" => "uri",
            "label" => "部屋の温度、湿度",
            "text" => "temp",
	    "uri" => "https://us.wio.seeed.io/v1/node/GroveTempHumD1/temperature?access_token=eecdb61def9790e172d1ad2a63aed257"
          ],
          [
            "type" => "uri",
            "label" => "エア・クオリティ",
            "text" => "air",
	    "uri" => "https://us.wio.seeed.io/v1/node/GroveAirqualityA0/quality?access_token=eecdb61def9790e172d1ad2a63aed257"
	  ],
          [
            "type" => "uri",
            "label" => "誰かいる？",
            "text" => "human",
	    "uri" => "https://us.wio.seeed.io/v1/node/GrovePIRMotionD0/approach?access_token=eecdb61def9790e172d1ad2a63aed257"
  	  ],
          [
            "type" => "message",
            "label" => "ワンちゃんの状況",
            "text" => "dog",
	    "uri" => "https://" . $_SERVER['SERVER_NAME'] . "/"
          ]
      ]
    ]
  ];
} else if ($text == 'temp' or $text == 'air') {
  $response_format_text = [
    "type" => "template",
    "altText" => "エアコンつけますか？（はいAircon／いいえNoair）",
    "template" => [
	"thumbnailImageUrl" => "https://" . $_SERVER['SERVER_NAME'] . "/IMG_1439.PNG",
        "type" => "confirm",
        "text" => "エアコンつけますか？",
        "actions" => [
            [
              "type" => "uri",
              "label" => "エアコンつける",
              "text" => "Aircon",
	      "uri" => "https://" . $_SERVER['SERVER_NAME'] . "/"
            ],
            [
              "type" => "message",
              "label" => "つけない",
              "text" => "Noair"
            ]
        ]
    ]
  ];
} else if ($text == 'human') {
  $response_format_text = [
    "type" => "template",
    "altText" => "誰かいる！",
    "template" => [
      "type" => "buttons",
      "thumbnailImageUrl" => "https://" . $_SERVER['SERVER_NAME'] . "/IMG_1431.PNG",
      "title" => "誰かいるみたい！",
      "text" => "そこにいるのはどなた？？",
      "actions" => [
          [
            "type" => "uri",
            "label" => "話しかけてみる",
            "text" => "speak",
	    "uri" => "https://us.wio.seeed.io/v1/node/GroveTempHumD1/temperature?access_token=eecdb61def9790e172d1ad2a63aed257"
          ],
          [
            "type" => "uri",
            "label" => "威嚇音を出す！",
            "text" => "alarm",
	    "uri" => "https://us.wio.seeed.io/v1/node/GroveAirqualityA0/quality?access_token=eecdb61def9790e172d1ad2a63aed257"
	  ],
          [
            "type" => "uri",
            "label" => "友達になる",
            "text" => "friend",
	    "uri" => "https://us.wio.seeed.io/v1/node/GrovePIRMotionD0/approach?access_token=eecdb61def9790e172d1ad2a63aed257"
  	  ],
          [
            "type" => "message",
            "label" => "知らんぷり。。",
            "text" => "Noaction",
	    "uri" => "https://" . $_SERVER['SERVER_NAME'] . "/"
          ]
      ]
    ]
  ];
} else if ($text == '資金プラン') {
  $response_format_text = [
    "type" => "template",
    "altText" => "今、おいくつですか？",
    "template" => [
      "type" => "buttons",
      "thumbnailImageUrl" => "https://" . $_SERVER['SERVER_NAME'] . "/IMG_1431.PNG",
      "title" => "年齢を教えて下さい",
      "text" => "今、おいくつですか？",
      "actions" => [
          [
            "type" => "message",
            "label" => "20代？",
            "text" => "20"
          ],
          [
            "type" => "message",
            "label" => "30代？",
            "text" => "30"
          ],
          [
            "type" => "message",
            "label" => "40代？",
            "text" => "40"
          ],
          [
            "type" => "message",
            "label" => "50代以上？",
            "text" => "50"
          ]
      ]
    ]
  ];
} else if ($text == '20') {
  $response_format_text = [
    "type" => "template",
    "altText" => "若いっていいですね！",
    "template" => [
	"thumbnailImageUrl" => "https://" . $_SERVER['SERVER_NAME'] . "/IMG_1439.PNG",
        "type" => "confirm",
        "text" => "若いっていいですね！がんばって働いてください！",
	"actions" => [
            [
              "type" => "message",
              "label" => "はい",
              "text" => "はい"
            ],
            [
              "type" => "message",
              "label" => "いいえ",
              "text" => "いいえ"
            ]
        ]
    ]
  ];
} else if ($text == '次も見てみる') {
  $response_format_text = [
    "type" => "template",
    "altText" => "こんな夢でしょうか？",
    "template" => [
      "type" => "carousel",
      "columns" => [
          [
            "thumbnailImageUrl" => "https://" . $_SERVER['SERVER_NAME'] . "/img2-1.jpg",
            "title" => "ささやかな夢",
            "text" => "年に一回は海外旅行にいく余裕を持つ",
            "actions" => [
              [
                  "type" => "postback",
                  "label" => "資金プランを見てみる",
                  "data" => "action=rsv&itemid=111"
              ],
              [
                  "type" => "postback",
                  "label" => "電話する",
                  "data" => "action=pcall&itemid=111"
              ],
              [
                  "type" => "uri",
                  "label" => "詳しく見る（ブラウザ起動）",
                  "uri" => "https://" . $_SERVER['SERVER_NAME'] . "/"
              ]
            ]
          ],
          [
            "thumbnailImageUrl" => "https://" . $_SERVER['SERVER_NAME'] . "/img2-2.jpg",
            "title" => "堅実な夢",
            "text" => "お子様に素晴しい教育環境を用意する",
            "actions" => [
              [
                  "type" => "postback",
                  "label" => "資金プランを見てみる",
                  "data" => "action=rsv&itemid=222"
              ],
              [
                  "type" => "postback",
                  "label" => "電話する",
                  "data" => "action=pcall&itemid=222"
              ],
              [
                  "type" => "uri",
                  "label" => "詳しく見る（ブラウザ起動）",
                  "uri" => "https://" . $_SERVER['SERVER_NAME'] . "/"
              ]
            ]
          ],
          [
            "thumbnailImageUrl" => "https://" . $_SERVER['SERVER_NAME'] . "/img2-3.jpg",
            "title" => "はたまたこんな夢",
            "text" => "50歳でアーリーリタイアして田舎暮らし",
            "actions" => [
              [
                  "type" => "postback",
                  "label" => "資金プランを見てみる",
                  "data" => "action=rsv&itemid=333"
              ],
              [
                  "type" => "postback",
                  "label" => "電話する",
                  "data" => "action=pcall&itemid=333"
              ],
              [
                  "type" => "uri",
                  "label" => "詳しく見る（ブラウザ起動）",
                  "uri" => "https://" . $_SERVER['SERVER_NAME'] . "/"
              ]
            ]
          ]
      ]
    ]
  ];
} else if ($text == 'お金') {
  $response_format_text = [
    "type" => "template",
    "altText" => "こんにちは あなた様の夢をお聞かせ下さい。（はい／いいえ）",
    "template" => [
	"thumbnailImageUrl" => "https://" . $_SERVER['SERVER_NAME'] . "/IMG_1439.PNG",
        "type" => "confirm",
        "text" => "こんにちは あなた様の夢をお聞かせ下さい。",
        "actions" => [
            [
              "type" => "message",
              "label" => "はい",
              "text" => "はい"
            ],
            [
              "type" => "message",
              "label" => "いいえ",
              "text" => "いいえ"
            ]
        ]
    ]
  ];
}
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

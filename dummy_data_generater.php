<?php

$list =
[
        [
                'name' => '赤富士・青富士ペアグラス',
                'description' => 'ビールを注ぐと泡が富士山に積もった雪のイメージを演出してくれます。',
                'image' => '../public/img/fujisan-glass.png',
        ],
        [
                'name' => 'Hand Drawing クロスラインペアカップ',
                'description' => '薄く軽く、繊細な口当たりは熟練した職人の手造りだからこそ。日常使い>にもビールやワインを楽しむおもてなしのシーンにもテーブルに流れる色のリズムを生み出します。',
                'image' => '../public/img/cross-line-glass.png',
        ],
        [
                'name' => '彩り 花小皿5枚組',
                'description' => '五色あられのような彩りが愛らしく、取り皿として使いやすく、コースターにもなる、汎用性の高いサイズ感です。',
                'image' => '../public/img/irodori-kozara.png',
        ],
        [
                'name' => '青い湖 ペアカップ',
                'description' => '粗め土を混ぜた白い土に、優しい青い硝子釉の濃淡が見え隠れしています。ほど良い大きさのカップはお茶タイムに好評です。',
                'image' => '../public/img/kirara-pair-cup.png',
        ],
        [
                'name' => '輪花 大皿',
                'description' => '伝統や様式ではなく、日用品に宿る「用の美」にこだわる作家が集い、現代陶器を追及している益子焼。',
                'image' => '../public/img/rinka-ozara.png'
        ]
];

$result = [];
foreach($list as $item) {
        $item['key'] = sha1($item['name']);
        $result[] = $item;
}

echo json_encode($result);

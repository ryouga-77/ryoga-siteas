<?php
// エラーが出ないか心配なら、開発中は以下のコメントを外す
// error_reporting(E_ALL);
// ini_set('display_errors', 1);

// ★★★ 必須設定：ここを自分の情報に書き換える！ ★★★
$to = 'ryouga0309ma@gmail.com'; // 受信したいメールアドレス
$site_name = 'りょうがのサイト';

// --------------------------------------------------------------------------------
// フォームデータの取得とサニタイズ（安全な形式への変換）
// --------------------------------------------------------------------------------
$name    = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);
$email   = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL); // メールアドレス形式かチェック
$message = filter_input(INPUT_POST, 'message', FILTER_SANITIZE_STRING);

// 必須項目のチェック
if (empty($name) || empty($email) || empty($message)) {
    // データが足りない場合はエラー画面を表示
    echo 'エラー: 必須項目が入力されていません。';
    exit;
}

// --------------------------------------------------------------------------------
// 送信するメールの内容を作成
// --------------------------------------------------------------------------------

// 件名
$subject = "[{$site_name}へのお問い合わせ] - {$name} 様より";

// 本文
$body = <<<EOD
{$site_name}に新しいお問い合わせがありました。
送信日時: ' . date('Y/m/d H:i:s') . '
--------------------------------------------------

■ お名前:
{$name}

■ メールアドレス:
{$email}

■ メッセージ:
{$message}

--------------------------------------------------
EOD;

// ヘッダー
$headers = "From: " . mb_encode_mimeheader($name) . " <{$to}>\r\n"; // 送信元をサイト管理者にするが、名前は入力者に
$headers .= "Reply-To: {$email}\r\n"; // 返信先を入力者のアドレスに設定
$headers .= "MIME-Version: 1.0\r\n"; 
$headers .= "Content-Type: text/plain; charset=UTF-8\r\n"; 

// --------------------------------------------------------------------------------
// ★★★ メール送信処理 ★★★
// --------------------------------------------------------------------------------

// mb_languageとmb_internal_encodingの設定（日本語環境に必須）
mb_language("Japanese");
mb_internal_encoding("UTF-8");

$success = mb_send_mail($to, $subject, $body, $headers);

// --------------------------------------------------------------------------------
// 送信後の画面表示
// --------------------------------------------------------------------------------
if ($success) {
    // 送信成功画面
    echo <<<HTML
    <!DOCTYPE html>
    <html lang="ja">
    <head>
        <meta charset="UTF-8">
        <title>送信完了 | {$site_name}</title>
    </head>
    <body>
        <h2>🎉 お問い合わせありがとうございました！🎉</h2>
        <p>{$site_name}より、折り返しご連絡いたします。</p>
        <p><a href="index.html">ホームへ戻る</a></p>
    </body>
    </html>
HTML;
} else {
    // 送信失敗画面
    echo 'ごめんなさい！メールの送信に失敗しました。サーバーのメール設定をご確認ください。';
}

?>
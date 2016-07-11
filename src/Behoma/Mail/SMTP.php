<?php
namespace Behoma\Mail;

class Smtp
{
    private $server = 'localhost';
    private $port = '25';
    private $authUser = '';
    private $authPassword = '';
    private $canSocketPooling = false;
    private $recentLog = array();
    private $poolingConnection = null;

    /**
     * Smtp constructor.
     * @param string $server
     * @param string $port
     * @param string $authUser
     * @param string $authPassword
     * @param bool $canSocketPooling
     */
    public function __construct($server = null, $port = null, $authUser = null, $authPassword = null, $canSocketPooling = null)
    {
        $server                  and $this->server = $server;
        $port                    and $this->port = $port;
        $authUser                and $this->authUser = $authUser;
        $authPassword            and $this->authPassword = $authPassword;
        isset($canSocketPooling) and $this->canSocketPooling = $canSocketPooling;
    }


    /**
     * ソケットをopenする
     * @return resource
     */
    public function open()
    {
        if ($this->poolingConnection) return $this->poolingConnection;

        $this->poolingConnection = fsockopen($this->server, $this->port);

        if ($this->authUser && $this->authPassword) {
            fputs($this->poolingConnection, "EHLO " . $this->server . "\r\n");
            $this->recentLog['EHLO'] = fgets($this->poolingConnection, 250);

            fputs($this->poolingConnection, "AUTH LOGIN\r\n");
            $this->recentLog['AUTH LOGIN'] = fgets($this->poolingConnection, 334);

            fputs($this->poolingConnection, base64_encode($this->authUser) . "\r\n");
            $this->recentLog['AUTH USER'] = fgets($this->poolingConnection, 334);

            fputs($this->poolingConnection, base64_encode($this->authPassword) . "\r\n");
            $this->recentLog['AUTH PASS'] = fgets($this->poolingConnection, 235);

        } else {
            fputs($this->poolingConnection, "HELO " . $this->server . "\r\n");
            $this->recentLog['HELO'] = fgets($this->poolingConnection, 128);
        }

        return $this->poolingConnection;
    }

    /**
     * ひとつ前の送信ログを取得する
     * @param ログ名称 - HELO, MAIL FROM, RCPT TO, DATA, SUBJECT BODY, SENT
     * @return string
     */
    public function getRecentLog($name)
    {
        return $this->recentLog[$name];
    }

    /**
     * ひとつ前の送信ログを全て取得する
     * @return string[]
     */
    public function getLogs()
    {
        return $this->recentLog;
    }

    /**
     * メールを送る
     * @param array (
     *   'to' => 送信先メールアドレス
     *   'form' => 送信元メールアドレス
     *   'subject' => '件名',
     *   'body' => '本文',
     *   'header' => array ( その他のヘッダ ),
     *   );
     * @return bool true / false
     */
    public function send($params)
    {
        $sock = $this->open();
        $this->recentLog = array();

        // 送信者指定
        fputs($sock, "MAIL FROM:" . $params['envelope_from'] . "\r\n");
        $this->recentLog['MAIL FROM'] = fgets($sock, 128);

        //宛先指定
        if ($params['envelope_to']) fputs($sock, "RCPT TO:" . $params['envelope_to'] . "\r\n");
        else                          fputs($sock, "RCPT TO:" . $params['to'] . "\r\n");
        $this->recentLog['RCPT TO'] = fgets($sock, 128);

        //DATAを送信後、ピリオドオンリーの行を送るまで本文。
        fputs($sock, "DATA\r\n");
        $this->recentLog['DATA'] = fgets($sock, 128);
        if ($params['header']) fputs($sock, trim($params['header']) . "\r\n");
        if ($params['to']) fputs($sock, 'To: ' . trim($params['to']) . "\r\n");
        if ($params['subject']) fputs($sock, 'Subject: ' . trim($params['subject']) . "\r\n");
        fputs($sock, "\r\n");

        //本文送信
        fputs($sock, $params['body'] . "\r\n");
        $this->recentLog['SUBJECT BODY'] = fgets($sock, 128);

        //ピリオドのみの行を送信。
        fputs($sock, "\r\n.\r\n");
        $this->recentLog['SENT'] = fgets($sock, 128);

        //成功すると250 OK～と返してくるので
        if (!preg_match("#^250#", $this->recentLog['SENT'])) return false;

        // ソケット閉じる
        if (!$this->canSocketPooling) $this->close();
        else                            fputs($sock, "\r\n");
        return true;
    }

    /**
     *  ソケット閉じる
     */
    public function close()
    {
        if ($this->poolingConnection) {
            fputs($this->poolingConnection, "QUIT\r\n");
            fclose($this->poolingConnection);
            $this->poolingConnection = null;
        }
    }
}
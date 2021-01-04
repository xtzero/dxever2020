<?php

require_once __DIR__ . '/qiniu_sdk/autoload.php';

define('QINIU_ACCESS_KEY', 'ofPkZ7cE-iVag4_1-SRTJA_dmeDlpeIDIO4E--el');
define('QINIU_SECRET_KEY', '_9vaO2kfPR-K_yKxW4kxQCVZ1EcnzcCC5-epLajR');
define('QINIU_BUCKET_NAME', 'xtpet');


class Qiniu
{
    protected $auth = null;

    public function __construct()
    {
        $this->auth();
    }

    private function auth()
    {
        $this->auth = new \Qiniu\Auth(QINIU_ACCESS_KEY, QINIU_SECRET_KEY);
    }

    public function upload()
    {
        if (!$_FILES || !$_FILES['files']) {
            echo '没有文件';
            die();
        }
        $file = $_FILES['files'];
        $upToken = $this->auth->uploadToken(QINIU_BUCKET_NAME);
        $uploadManager = new \Qiniu\Storage\UploadManager();
        $filePath = $file['tmp_name'];
        $key = 'dxever/upload/'.md5(implode('|', [
            time(),
            $file['name']
        ])) . '.' . explode('.', $file['name'])[count(explode('.', $file['name'])) - 1];
        list($ret, $err) = $uploadManager->putFile($upToken, $key, $filePath);
        if ($err) {
            echo json_encode($err);
            exit();
        } else {
            echo json_encode($ret);
            exit();
        }
    }
}

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods:GET,POST,PUT,DELETE');
header('Access-Control-Allow-Headers:Origin, Content-Type, Cookie, Accept');
header('Access-Control-Allow-Credentials:true');

(new Qiniu())->upload();

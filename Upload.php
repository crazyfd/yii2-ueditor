<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace crazyfd\ueditor;

use Yii;
use yii\base\Action;
use yii\base\InvalidConfigException;
use yii\helpers\Json;


class Upload extends Action
{
    public $currentPath;
    public $config;
    public $action;
    public $callback;


    public function init()
    {
        $this->currentPath = dirname(__FILE__);
        Yii::$app->request->enableCsrfValidation = false;
        Yii::$app->request->enableCookieValidation = true;
        parent::init();
    }

    /**
     * Runs the action.
     */
    public function run()
    {
        $action = Yii::$app->getRequest()->get('action', null);
        switch ($action) {
            case 'config':
                $result = Json::encode($this->config());
                break;
            /* 上传图片 */
            case 'uploadimage':
            /* 上传涂鸦 */
            case 'uploadscrawl':
            /* 上传视频 */
            case 'uploadvideo':
            /* 上传文件 */
            case 'uploadfile':
                $result = $this->fileUpload();
                break;
            /* 抓取远程文件 */
            case 'catchimage':
                $result = $this->saveRemote();
                break;
            default:
                $result = Json::encode(array('state' => '请求地址出错'));
                break;
        }
        /* 输出结果 */
        $callback = Yii::$app->getRequest()->get('callback', null);
        if ($callback) {
            if (preg_match("/^[\w_]+$/", $callback)) {
                $result = htmlspecialchars($callback) . '(' . $result . ')';
            } else {
                $result = Json::encode(array('state' => 'callback参数不合法'));
            }
        }
        return $result;
    }

    protected function fileUpload($config=[])
    {
        //待完善
        $ak = Yii::$app->params['qiniu']['ak'];
        $sk = Yii::$app->params['qiniu']['sk'];
        $domain = Yii::$app->params['qiniu']['domain'];
        $bucket = Yii::$app->params['qiniu']['bucket'];
        $qiniu = new \crazyfd\qiniu\Qiniu($ak, $sk,$domain, $bucket);

        foreach($_FILES as $file){
            $ext = strtolower(strrchr($file['name'], '.'));
            $key = date('Y/m/').uniqid().mt_rand(1000,9999).$ext;
            $qiniu->uploadFile($file['tmp_name'],$key);
        }
        $url = $qiniu->getLink($key);
        return Json::encode(['state'=>'SUCCESS','url'=>$url,"size"=>$file['size'],"original"=>$key,"name"=>$key]);
    }

    protected function saveRemote($config=[])
    {
        //待完善
    }

    private function config()
    {
        return Json::decode(preg_replace("/\/\*[\s\S]+?\*\//", "", file_get_contents($this->currentPath . '/config.json')), true);
    }
}

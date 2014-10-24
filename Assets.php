<?php
namespace crazyfd\ueditor;

use Yii;

class Assets extends \yii\web\AssetBundle
{
    public $depends = ['yii\web\JqueryAsset'];

    private function getJs()
    {
        return [
            'ueditor.config.js',
            YII_DEBUG ? 'ueditor.all.js' : 'ueditor.all.min.js',
        ];
    }

    public function init()
    {
        $this->sourcePath = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'assets';
        if (empty($this->js)) {
            $this->js = $this->getJs();
        }
        parent::init();
    }
}
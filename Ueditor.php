<?php

namespace crazyfd\ueditor;
use Yii;
use yii\web\View;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\widgets\InputWidget;
use yii\helpers\Url;
/**
 * Ueditor Widget
 *
 */
class Ueditor extends InputWidget
{
    public $id;

    /**
     * UE 参数
     * @var array
     */
    public $jsOptions = [];


    public $readyEvent;

    public $width;
    public $height;
    public $autoHeightEnable = false;

    /**
     * Initializes the widget.
     */
    public function init()
    {
        parent::init();

        if (empty($this->id)) {
            $this->id = $this->hasModel() ? Html::getInputId($this->model, $this->attribute) : $this->getId();
        }
        if (empty($this->name)) {
            $this->name = $this->hasModel() ? Html::getInputName($this->model, $this->attribute) : $this->id;
        }
        if (empty($this->value) && $this->hasModel() && $this->model->hasAttribute($this->attribute)) {
            $this->value = $this->model->getAttribute($this->attribute);
        }

        if(!$this->autoHeightEnable){
            if(empty($this->width)){
                $this->width = '100%';
            }
            if(empty($this->height)){
                $this->height = '350';
            }
        }

        $options = [
            'id' => $this->id,
            'style' => 'width:100%;height:350px'
        ];
        $this->options = array_merge($options,$this->options);

        $jsOptions = [
            'serverUrl' => Url::to(['ueditor','type'=>'ueditor']),
            'autoHeightEnable' => $this->autoHeightEnable,
            'autoFloatEnable' => true,
            'textarea'=>$this->name
        ];
        $this->jsOptions = array_merge($jsOptions,$this->jsOptions);
    }

    public function run()
    {
        Assets::register($this->view);
        $this->registerScripts();
        $this->options['type'] = 'text/plain';
        $content = Html::tag('script', $this->value, $this->options);
        return $content;
    }

    public function registerScripts()
    {
        $token= Yii::$app->request->getCsrfToken();
        $tokenName= Yii::$app->request->csrfParam;
        $jsonOptions = Json::encode($this->jsOptions);
        $script = "var ue = UE.getEditor('{$this->id}', " . $jsonOptions . ");";
        if(!$this->autoHeightEnable){
            //$script.= 'UE.setHeight('.$this->height.');';
        }
        $script .= "ue.ready(function() {ue.execCommand('serverparam', {\"{$tokenName}\": \"{$token}\"});});";
        if ($this->readyEvent) {
            $script .= ".ready(function(){{$this->readyEvent}})";
        }
        $script .= ';';
        $this->view->registerJs($script, View::POS_READY);
    }

}
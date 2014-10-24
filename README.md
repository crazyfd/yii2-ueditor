yii2-ueditor
=================================
* @author crazyfd <crazyfd@qq.com>
* @version 1.0
* @desc  百度HTML编辑器
html editer for Yii Framework 2


How to install?
To use this widget, you may insert the following code:
--------------------------------

Get it via [composer](http://getcomposer.org/) by adding the package to your `composer.json`:

```json
{
  "require": {
    "crazyfd/yii2-ueditor": "dev-master"
  }
}
```
```php
php composer.phar update
```

Usage
-----

```php
<?php 
<?= $form->field($model, 'content')->widget(Ueditor::className(),[]) ?>
```

<?php

/**
 * Created by PhpStorm.
 * User: Yuchen Yao
 * Date: 16/03/2019
 * Time: 5:37 PM
 */

/* @var $this \yii\web\View */
$this->title = 'Sign up';
$this->params['breadcrumbs'][] = ['label' => 'registration', 'url' => ['signin']];
$this->params['breadcrumbs'][] = $this->title;

use yii\bootstrap\ActiveForm;
use yii\helpers\Html; ?>
<head>
    <title><?= HTML::encode($this->title) ?></title>
</head>
<body>
<?php $form = ActiveForm::begin([
    'id' => 'Registration',
    'layout' => 'horizontal',
    'fieldConfig' => [
        'template' => "{label}\n<div class=\"col-lg-3\">{input}</div>\n<div class=\"col-lg-8\">{error}</div>",
        'labelOptions' => ['class' => 'col-lg-1 control-label'],
    ],
]); ?>
<?= $form->field($model, 'FirstName')->textInput(); ?>
<?= $form->field($model, 'LastName')->textInput(); ?>
<?= $form->field($model, 'email')->textInput(); ?>
<?= $form->field($model, 'password')->passwordInput(); ?>
<?= $form->field($model, 'passwordVerify')->passwordInput() ?>

<div class="form-group">
    <div class="col-lg-offset-1 col-lg-11">
        <?= HTML::submitButton('Sign up', ['class' => 'btn btn-primary']) ?>
        <p>Already have an account? <a href="index.php?r=registration/signin">Sign in</a></p>
    </div>
</div>

<?php $form = ActiveForm::end() ?>
</body>
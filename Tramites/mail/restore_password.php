<?php
use yii\helpers\Url;
?>
<p>Hola, <?=$model->getUsername()?></p>
<p>A través de este enlace podrás restablecer tu contraseña:</p>
<a href="<?=Url::to(['site/reset-password?token='.$model->password_reset_token], true)?>" class="btn btn-success">Restablecer Contraseña</a>
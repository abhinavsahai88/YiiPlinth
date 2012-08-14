<?php
$this->pageTitle=Yii::app()->name . ' - Login';
?>

<h1>Login</h1>

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>$tcFormName,
	'enableClientValidation'=>true,
	'clientOptions'=>array(
		'validateOnSubmit'=>true,
	),
)); ?>

	<fieldset>
		<p class="note">Fields with <span class="required">*</span> are required.</p>

		<div class="field">
			<?php echo $form->labelEx($toModel,'username'); ?>
			<?php echo $form->textField($toModel,'username'); ?>
			<?php echo $form->error($toModel,'username'); ?>
			<p class="hint">
				Hint: Your user name is your email address.
			</p>
		</div>

		<div class="field">
			<?php echo $form->labelEx($toModel,'password'); ?>
			<?php echo $form->passwordField($toModel,'password'); ?>
			<?php echo $form->error($toModel,'password'); ?>
		</div>

		<div class="field">
			<?php echo CHtml::link("Forgot your password?",array('PasswordReset')) ?>
		</div>

		<div class="buttons">
			<?php
			 echo CHtml::htmlButton('Login', array('type'=>'submit')); 
			 ?>
		</div>

		<?php if(Yii::app()->user->hasFlash('formMessage')): ?> 
			<div class="flash-success">
			    <?php echo Yii::app()->user->getFlash('formMessage'); ?>
			</div>
		<?php endif; ?>

	</fieldset>

<?php $this->endWidget(); ?>


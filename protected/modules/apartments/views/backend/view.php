<?php

$this->breadcrumbs=array(
	$model->getStrByLang('title'),
);

$this->menu = array(
	array('label'=>tt('Manage apartments'), 'url'=>array('admin')),
	array('label'=>tt('Add apartment'), 'url'=>array('create')),
	array('label'=>tt('Update apartment'), 'url'=>array('update', 'id' => $model->id)),
	array('label'=>tt('Delete apartment'), 'url'=>'#',
		'linkOptions'=>array(
			'submit'=>array('delete','id'=>$model->id),
			'confirm'=>tt('Are you sure you want to delete this apartment?')
		)
	),
);

$this->breadcrumbs=array(
	$model->getStrByLang('title'),
);
$this->pageTitle .= ' - '.CHtml::encode($model->getStrByLang('title'));
?>

<div class="<?php echo issetModule('viewpdf') ? 'div-pdf-fix' : ''; ?>">
	<?php
	if(issetModule('viewpdf')) {
		echo '<div class="floatleft pdficon">
				<a href="'.Yii::app()->baseUrl.'/viewpdf/main/view?id='.$model->id.'"
					target="_blank"><img src="'.Yii::app()->baseUrl.'/images/design/file_pdf.png"
					alt="'.Yii::t('common', 'Pdf version').'" title="'.Yii::t('common', 'Pdf version').'"  />
				</a></div>';
	}
	?>
	<div class="floatleft-title">
		<div>
			<div class="div-title">
				<h1 class="h1-ap-title"><?php echo CHtml::encode($model->getStrByLang('title')); ?></h1>
			</div>
			<?php if($model->rating): ?>
			<div class="ratingview-title">
				<?php
				$this->widget('CStarRating',
					array(
						'name'=>'ratingview'.$model->id,
						'id'=>'ratingview'.$model->id,
						'value'=>intval($model->rating),
						'readOnly'=>true,
					));
				?>
			</div>
			<?php endif; ?>
		</div>
		<div class="clear"></div>
		<div class="stat-views">
			<?php if (isset($statistics) && is_array($statistics)) : ?>
			<?php echo tt('Views') ?>: <?php echo tt('views_all') . ' ' . $statistics['all'] ?>, <?php echo tt('views_today') . ' ' . $statistics['today'].'.&nbsp;';?>
			<?php echo '&nbsp;'.tc('Date created') . ': ' . $model->getDateTimeInFormat('date_created'); ?>
			<?php endif; ?>
		</div>
	</div>
</div>

<div class="clear"></div>
<?php
// показвываем непосредственно объявление
$this->renderPartial('../_view', array(
	'data'=>$model,
	'usertype' => 'visitor',
	'statistics' => $statistics,
));
?>




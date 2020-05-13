<?php
/**
 * @var $panel \putyourlightson\elementspanel\debug\ElementPanel
 */
$count = $panel->data['eagerLoadingOpportunity'] ? 1 : 0;
$class = $panel->data['eagerLoadingOpportunity'] ? 'yii-debug-toolbar__label_warning' : '';
?>

<div class="yii-debug-toolbar__block">
    <a href="<?= $panel->getUrl() ?>">
        Eager-Loading
        <span class="yii-debug-toolbar__label <?= $class ?>"><?= $count ?></span>
    </a>
</div>

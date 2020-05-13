<?php
/**
 * @var $panel \putyourlightson\elementspanel\debug\ElementPanel
 */
$count = count($panel->data['queries']);
?>

<div class="yii-debug-toolbar__block">
    <a href="<?= $panel->getUrl() ?>">
        Eager-Loading
        <span class="yii-debug-toolbar__label <?= $count ? 'yii-debug-toolbar__label_warning' : '' ?>"><?= $count ?></span>
    </a>
</div>

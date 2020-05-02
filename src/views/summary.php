<?php
/** @var $panel \putyourlightson\elementspanel\debug\ElementPanel */
$count = 0;
foreach ($panel->data as $elements) {
    $count = $count + count($elements);
}
?>

<div class="yii-debug-toolbar__block">
    <a href="<?= $panel->getUrl() ?>">
        Elements
        <span class="yii-debug-toolbar__label yii-debug-toolbar__label_info"><?= $count ?></span>
    </a>
</div>

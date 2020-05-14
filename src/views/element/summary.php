<?php
/**
 * @var $panel \putyourlightson\elementspanel\debug\ElementPanel
 */
$total = $panel->data['total'];
?>

<div class="yii-debug-toolbar__block">
    <a href="<?= $panel->getUrl() ?>">
        Elements
        <span class="yii-debug-toolbar__label yii-debug-toolbar__label_info"><?= $total ?></span>
    </a>
</div>

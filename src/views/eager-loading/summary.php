<?php
/**
 * @var \putyourlightson\elementspanel\debug\ElementPanel $panel
 */
$total = $panel->data['total'];
?>

<div class="yii-debug-toolbar__block">
    <a href="<?= $panel->getUrl() ?>">
        Eager-Loading
        <span class="yii-debug-toolbar__label <?= $total ? 'yii-debug-toolbar__label_warning' : '' ?>"><?= $total ?></span>
    </a>
</div>

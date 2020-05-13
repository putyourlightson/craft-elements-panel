<?php
/** @var $panel \putyourlightson\elementspanel\debug\EagerLoadingPanel */
?>

<h1>Eager-Loading</h1>

<?php if ($panel->data['hasEagerLoadingOpportunity']): ?>
    <p>
        <?= Craft::t('elements-panel', 'An opportunity for eager-loading elements was detected.') ?>
    </p>

<?php else: ?>
    <p>
        <?= Craft::t('elements-panel', 'No opportunities for eager-loading elements were detected.') ?>
    </p>

<?php endif; ?>


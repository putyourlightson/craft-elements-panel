<?php
/** @var $panel \putyourlightson\elementspanel\debug\EagerLoadingPanel */
?>

<h1>Eager-Loading</h1>

<?php if ($panel->data['eagerLoadingOpportunity']): ?>
    <p>
        <?= Craft::t('elements-panel', 'An opportunity to eager-load elements was detected.') ?>
    </p>

<?php else: ?>
    <p>
        <?= Craft::t('elements-panel', 'No opportunities to eager-load elements were detected.') ?>
    </p>

<?php endif; ?>


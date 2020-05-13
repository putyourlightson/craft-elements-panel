<?php
/** @var $panel \putyourlightson\elementspanel\debug\EagerLoadingPanel */
?>

<h1>Eager-Loading</h1>

<?php if ($panel->data['opportunity']): ?>
    <p>
        <?= Craft::t('elements-panel', 'Opportunities for eager-loading elements were detected.') ?>
    </p>

    <div class="table-responsive">
        <table class="table table-condensed table-bordered table-striped table-hover" style="table-layout: fixed;">
            <thead>
                <tr>
                    <th style="nowrap"><?= Craft::t('elements-panel', 'Source Element') ?></th>
                    <th><?= Craft::t('elements-panel', 'Field') ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($panel->data['queries'] as $query): ?>
                    <tr>
                        <td><?= $query['source']->title ?></td>
                        <td><?= $query['field']->name ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

<?php else: ?>
    <p>
        <?= Craft::t('elements-panel', 'No opportunities for eager-loading elements were detected.') ?>
    </p>

<?php endif; ?>


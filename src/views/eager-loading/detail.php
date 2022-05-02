<?php
/**
 * @var \putyourlightson\elementspanel\debug\ElementPanel $panel
 */
?>

<h1>Eager-Loading</h1>

<?php if (empty($panel->data['queries'])): ?>
    <p>
        <?= Craft::t('elements-panel', 'No opportunities for eager-loading elements were detected.') ?>
    </p>

<?php else: ?>
    <p>
        <?= Craft::t('elements-panel', 'Opportunities for eager-loading elements were detected on this page.') ?>
    </p>

    <div class="table-responsive">
        <table class="table table-condensed table-bordered table-striped table-hover" style="table-layout: fixed;">
            <thead>
                <tr>
                    <th><?= Craft::t('elements-panel', 'Field Name') ?></th>
                    <th><?= Craft::t('elements-panel', 'Field Handle') ?></th>
                    <th><?= Craft::t('elements-panel', 'Queries') ?></th>
                    <th><?= Craft::t('elements-panel', 'Duplicates') ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($panel->data['queries'] as $query): ?>
                    <tr>
                        <td><?= $query['fieldName'] ?></td>
                        <td><?= $query['fieldHandle'] ?></td>
                        <td><?= $query['count'] ?></td>
                        <td><?= $query['duplicates'] ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

<?php endif; ?>

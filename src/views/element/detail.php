<?php
/** @var $panel \putyourlightson\elementspanel\debug\ElementPanel */
?>

<h1>Elements</h1>

<?php if (empty($panel->data['elements'])): ?>
    <p>
        <?= Craft::t('elements-panel', 'No elements were populated.') ?>
    </p>

<?php else: ?>
    <div class="table-responsive">
        <table class="table table-condensed table-bordered table-striped table-hover" style="table-layout: fixed;">
            <thead>
                <tr>
                    <th style="nowrap"><?= Craft::t('elements-panel', 'Element Type') ?></th>
                    <th><?= Craft::t('elements-panel', 'Populated') ?></th>
                    <th><?= Craft::t('elements-panel', 'Duplicates') ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($panel->data['elements'] as $elements): ?>
                    <tr>
                        <td><?= $elements['elementType'] ?></td>
                        <td><?= $elements['count'] ?></td>
                        <td><?= $elements['duplicates'] ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

<?php endif; ?>

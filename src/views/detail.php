<?php
/** @var $panel \putyourlightson\elementspanel\debug\ElementPanel */
?>

<h1>Elements</h1>

<?php if (empty($panel->data)): ?>
    <p><?= Craft::t('elements-panel', 'No elements were populated.') ?></p>

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
                <?php foreach ($panel->data as $elementType => $elements): ?>
                    <?php
                        $duplicates = 0;
                        foreach ($elements as $id => $count) {
                            if ($count > 1) {
                                $duplicates++;
                            }
                        }
                    ?>
                    <tr>
                        <td><?= $elementType ?></td>
                        <td><?= count($elements) ?></td>
                        <td><?= $duplicates ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

<?php endif; ?>

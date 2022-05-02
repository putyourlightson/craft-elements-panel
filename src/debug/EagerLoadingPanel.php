<?php
/**
 * @copyright Copyright (c) PutYourLightsOn
 */

namespace putyourlightson\elementspanel\debug;

use Craft;
use craft\base\Field;
use craft\elements\db\ElementQuery;
use craft\elements\db\MatrixBlockQuery;
use craft\events\CancelableEvent;
use yii\base\Event;
use yii\debug\Panel;

class EagerLoadingPanel extends Panel
{
    /**
     * @var array
     */
    private array $_queries = [];

    /**
     * @var string
     */
    private string $_viewPath = '@vendor/putyourlightson/craft-elements-panel/src/views/eager-loading/';

    /**
     * @inheritdoc
     */
    public function init(): void
    {
        parent::init();

        Event::on(ElementQuery::class, ElementQuery::EVENT_BEFORE_PREPARE,
            function(CancelableEvent $event) {
                /** @var ElementQuery $elementQuery */
                $elementQuery = $event->sender;

                if ($elementQuery instanceof MatrixBlockQuery) {
                    $this->_checkMatrixRelations($elementQuery);
                }
                else {
                    $this->_checkBaseRelations($elementQuery);
                }
            }
        );
    }

    /**
     * @inheritdoc
     */
    public function getName(): string
    {
        return 'Eager-Loading';
    }

    /**
     * @inheritdoc
     */
    public function getSummary(): string
    {
        return Craft::$app->getView()->render($this->_viewPath . 'summary', ['panel' => $this]);
    }

    /**
     * @inheritdoc
     */
    public function getDetail(): string
    {
        return Craft::$app->getView()->render($this->_viewPath . 'detail', ['panel' => $this]);
    }

    /**
     * @inheritdoc
     */
    public function save(): array
    {
        $total = 0;
        $queries = [];
        $fields = Craft::$app->getFields();

        foreach ($this->_queries as $fieldId => $sourceIds) {
            $duplicates = 0;

            foreach ($sourceIds as $sourceId => $count) {
                $total++;

                if ($count > 1) {
                    $duplicates++;
                }
            }

            /** @var Field $field */
            $field = $fields->getFieldById($fieldId);

            $queries[] = [
                'fieldName' => $field->name,
                'fieldHandle' => $field->handle,
                'count' => count($sourceIds),
                'duplicates' => $duplicates,
            ];
        }

        return [
            'total' => $total,
            'queries' => $queries,
        ];
    }

    /**
     * Checks base relations.
     *
     * @see BaseRelationField::normalizeValue
     */
    private function _checkBaseRelations(ElementQuery $elementQuery)
    {
        $join = $elementQuery->join[0] ?? null;

        if ($join === null) {
            return;
        }

        $relationTypes = [
            ['relations' => '{{%relations}}'],
            '{{%relations}} relations',
        ];

        if ($join[0] == 'INNER JOIN' && in_array($join[1], $relationTypes)) {
            $fieldId = $join[2][2]['relations.fieldId'] ?? null;
            $sourceId = $join[2][2]['relations.sourceId'] ?? null;

            if ($fieldId === null || $sourceId === null) {
                return;
            }

            $this->_addQuery($fieldId, $sourceId);
        }
    }

    /**
     * Checks matrix relations.
     *
     * @see MatrixBlockQuery::beforePrepare
     */
    private function _checkMatrixRelations(MatrixBlockQuery $elementQuery)
    {
        if (empty($elementQuery->fieldId) || empty($elementQuery->ownerId)) {
            return;
        }

        $fieldId = is_array($elementQuery->fieldId) ? $elementQuery->fieldId[0] : $elementQuery->fieldId;
        $ownerId = is_array($elementQuery->ownerId) ? $elementQuery->ownerId[0] : $elementQuery->ownerId;

        $this->_addQuery($fieldId, $ownerId);
    }

    /**
     * Adds a query that could be eager-loaded.
     */
    private function _addQuery(int $fieldId, int $sourceId)
    {
        if (empty($this->_queries[$fieldId])) {
            $this->_queries[$fieldId] = [];
        }

        if (empty($this->_queries[$fieldId][$sourceId])) {
            $this->_queries[$fieldId][$sourceId] = 0;
        }

        $this->_queries[$fieldId][$sourceId]++;
    }
}

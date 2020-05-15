<?php
/**
 * @copyright Copyright (c) PutYourLightsOn
 */

namespace putyourlightson\elementspanel\debug;

use Craft;
use craft\base\Field;
use craft\elements\db\ElementQuery;
use craft\events\CancelableEvent;
use yii\base\Event;
use yii\debug\Panel;

class EagerLoadingPanel extends Panel
{
    /**
     * @var array
     */
    private $_queries = [];

    /**
     * @var string
     */
    private $_viewPath = '@vendor/putyourlightson/craft-elements-panel/src/views/eager-loading/';

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        Event::on(ElementQuery::class, ElementQuery::EVENT_BEFORE_PREPARE,
            function(CancelableEvent $event) {
                /** @var ElementQuery $elementQuery */
                $elementQuery = $event->sender;

                $this->_addQuery($elementQuery);
            }
        );
    }

    /**
     * @inheritdoc
     */
    public function getName()
    {
        return 'Eager-Loading';
    }

    /**
     * @inheritdoc
     */
    public function getSummary()
    {
        return Craft::$app->getView()->render($this->_viewPath.'summary', ['panel' => $this]);
    }

    /**
     * @inheritdoc
     */
    public function getDetail()
    {
        return Craft::$app->getView()->render($this->_viewPath.'detail', ['panel' => $this]);
    }

    /**
     * @inheritdoc
     */
    public function save()
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
     * Adds queries that could be eager-loading.
     *
     * @param ElementQuery $elementQuery
     */
    private function _addQuery(ElementQuery $elementQuery)
    {
        if (empty($elementQuery->join)) {
            return;
        }

        $join = $elementQuery->join[0];

        /**
         * This conditional relies on the way that relation fields are loaded.
         * @see \craft\fields\BaseRelationField::normalizeValue
         */
        if ($join[0] == 'INNER JOIN' && $join[1] == ['relations' => '{{%relations}}']) {
            $fieldId = $join[2][2]['relations.fieldId'] ?? null;
            $sourceId = $join[2][2]['relations.sourceId'] ?? null;

            if ($fieldId === null || $sourceId === null) {
                return;
            }

            if (empty($this->_queries[$fieldId])) {
                $this->_queries[$fieldId] = [];
            }

            if (empty($this->_queries[$fieldId][$sourceId])) {
                $this->_queries[$fieldId][$sourceId] = 0;
            }

            $this->_queries[$fieldId][$sourceId]++;
        }
    }
}

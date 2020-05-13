<?php
/**
 * @copyright Copyright (c) PutYourLightsOn
 */

namespace putyourlightson\elementspanel\debug;

use Craft;
use craft\elements\db\ElementQuery;
use craft\events\CancelableEvent;
use yii\base\Event;
use yii\debug\Panel;

class EagerLoadingPanel extends Panel
{
    /**
     * @var bool
     */
    private $_opportunity = false;

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

                if (empty($elementQuery->join)) {
                    return;
                }

                $join = $elementQuery->join[0];

                if ($join[0] == 'INNER JOIN' && $join[1] == ['relations' => '{{%relations}}']) {
                    $this->_opportunity = true;

                    $query = [
                        'sourceId' => $join[2][2]['relations.sourceId'] ?? null,
                        'fieldId' => $join[2][2]['relations.fieldId'] ?? null,
                    ];

                    if (!in_array($query, $this->_queries)) {
                        $this->_queries[] = $query;
                    }
                }
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
        $queries = [];
        $elements = Craft::$app->getElements();
        $fields = Craft::$app->getFields();

        foreach ($this->_queries as $query) {
            $queries[] = [
                'source' => $elements->getElementById($query['sourceId']),
                'field' => $fields->getFieldById($query['fieldId']),
            ];
        }

        return [
            'opportunity' => $this->_opportunity,
            'queries' => $queries,
        ];
    }
}

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
    private array $queries = [];

    /**
     * @var string
     */
    private string $viewPath = '@putyourlightson/elementspanel/views/eager-loading/';

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
                $this->checkElementQuery($elementQuery);
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
        return Craft::$app->getView()->render($this->viewPath . 'summary', ['panel' => $this]);
    }

    /**
     * @inheritdoc
     */
    public function getDetail(): string
    {
        return Craft::$app->getView()->render($this->viewPath . 'detail', ['panel' => $this]);
    }

    /**
     * @inheritdoc
     */
    public function save(): array
    {
        $total = 0;
        $queries = [];
        $fields = Craft::$app->getFields();

        foreach ($this->queries as $fieldId => $sourceIds) {
            $duplicates = 0;

            foreach ($sourceIds as $count) {
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
     * Checks for opportunities to eager-load elements.
     * Based on the `HintsService::checkElementQuery` method in Blitz, with permission.
     *
     * @see \putyourlightson\blitz\services\HintsService::checkElementQuery
     */
    private function checkElementQuery(ElementQuery $elementQuery): void
    {
        if ($elementQuery->wasEagerLoaded()
            || $elementQuery->eagerLoadHandle === null
            || $elementQuery->id !== null
        ) {
            return;
        }

        /** @see ElementQuery::wasEagerLoaded() */
        $planHandle = $elementQuery->eagerLoadHandle;
        if (str_contains($planHandle, ':')) {
            $planHandle = explode(':', $planHandle, 2)[1];
        }

        $field = Craft::$app->getFields()->getFieldByHandle($planHandle);
        if ($field === null) {
            return;
        }

        $this->addQuery($field->id, $elementQuery->eagerLoadSourceElement->id);
    }

    /**
     * Adds a query that could be eager-loaded.
     */
    private function addQuery(int $fieldId, int $sourceId): void
    {
        if (empty($this->queries[$fieldId])) {
            $this->queries[$fieldId] = [];
        }

        if (empty($this->queries[$fieldId][$sourceId])) {
            $this->queries[$fieldId][$sourceId] = 0;
        }

        $this->queries[$fieldId][$sourceId]++;
    }
}

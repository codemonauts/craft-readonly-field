<?php

namespace codemonauts\readonly\fields;

use Craft;
use craft\base\ElementInterface;
use craft\base\Field;
use craft\base\PreviewableFieldInterface;
use craft\base\SortableFieldInterface;
use craft\fields\conditions\TextFieldConditionRule;
use craft\helpers\Html;
use craft\helpers\StringHelper;
use LitEmoji\LitEmoji;
use Twig\Error\Error;
use yii\db\Schema;

class ReadonlyField extends Field implements PreviewableFieldInterface, SortableFieldInterface
{
    /**
     * @var string The type of database column the field should have in the content table
     */
    public string $columnType = Schema::TYPE_STRING;

    /**
     * @var string|null The template to render the value
     */
    public ?string $template = null;

    /**
     * @inheritdoc
     */
    public static function displayName(): string
    {
        return Craft::t('readonly', 'Read-only Field');
    }

    /**
     * @inheritdoc
     */
    public function getContentColumnType(): string
    {
        return $this->columnType;
    }

    /**
     * @inheritdoc
     */
    public function getInputHtml($value, ElementInterface $element = null): string
    {
        return Craft::$app->getView()->renderTemplate('readonly/input', [
            'name' => $this->handle,
            'value' => $value,
            'renderedValue' => $this->renderValue($value, $element),
            'field' => $this,
        ]);
    }

    /**
     * @inheritdoc
     */
    public function serializeValue($value, ElementInterface $element = null): mixed
    {
        if ($value !== null) {
            $value = LitEmoji::unicodeToShortcode($value);
        }

        return $value;
    }

    /**
     * @inheritdoc
     */
    public function getSearchKeywords($value, ElementInterface $element): string
    {
        $value = (string)$value;
        return LitEmoji::unicodeToShortcode($value);
    }

    /**
     * @inheritdoc
     */
    public function getElementConditionRuleType(): ?string
    {
        return TextFieldConditionRule::class;
    }

    /**
     * @inheritdoc
     */
    public function getSettingsHtml(): ?string
    {
        return Craft::$app->getView()->renderTemplate('readonly/settings', [
            'field' => $this,
        ]);
    }

    /**
     * @inheritdoc
     */
    public function getTableAttributeHtml(mixed $value, ElementInterface $element): string
    {
        return Html::encode(StringHelper::stripHtml($this->renderValue((string)$value, $element)));
    }

    /**
     * @param string $value
     * @param \craft\base\ElementInterface $element
     *
     * @return string
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\SyntaxError
     */
    private function renderValue(?string $value, ElementInterface $element): ?string
    {
        if ($this->template !== null) {
            try {
                $value = Craft::$app->getView()->renderString($this->template, [
                    'value' => $value,
                    'element' => $element,
                ]);
            } catch (Error) {
                Craft::error('Error rendering template of read-only field with handle "' . $this->handle . '".', 'readonly');
            }
        }

        return $value;
    }
}

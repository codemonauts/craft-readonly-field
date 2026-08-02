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
     * @var string The DB column type for the value.
     * @deprecated In Craft 5 liegen Feldwerte in der JSON-`content`-Spalte; diese
     *   Einstellung ist wirkungslos (Ersatz wäre die statische `dbType()`, Default TEXT).
     *   Bleibt nur, damit bestehende/migrierte Felder mit gespeichertem `columnType` laden.
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
    protected function inputHtml(mixed $value, ?ElementInterface $element, bool $inline = false): string
    {
        return Craft::$app->getView()->renderTemplate('readonly/input', [
            'name' => $this->handle,
            'value' => $value,
            'renderedValue' => $this->renderValue($value !== null ? (string)$value : null, $element),
            'field' => $this,
        ]);
    }

    /**
     * @inheritdoc
     */
    public function serializeValue(mixed $value, ?ElementInterface $element): mixed
    {
        if ($value !== null) {
            $value = LitEmoji::unicodeToShortcode($value);
        }

        return $value;
    }

    /**
     * @inheritdoc
     */
    protected function searchKeywords(mixed $value, ElementInterface $element): string
    {
        return LitEmoji::unicodeToShortcode((string)$value);
    }

    /**
     * @inheritdoc
     */
    public function getElementConditionRuleType(): array|string|null
    {
        return TextFieldConditionRule::class;
    }

    /**
     * @inheritdoc
     */
    protected function settingsHtml(): ?string
    {
        return Craft::$app->getView()->renderTemplate('readonly/settings', [
            'field' => $this,
        ]);
    }

    /**
     * @inheritdoc
     */
    public function getPreviewHtml(mixed $value, ElementInterface $element): string
    {
        return Html::encode(StringHelper::stripHtml($this->renderValue((string)$value, $element)));
    }

    /**
     * Rendert den Wert über das konfigurierte Twig-Template (falls gesetzt).
     *
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\SyntaxError
     */
    private function renderValue(?string $value, ?ElementInterface $element = null): ?string
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

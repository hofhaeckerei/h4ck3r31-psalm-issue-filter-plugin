<?php
declare(strict_types=1);

namespace H4ck3r31\PsalmIssueFilterPlugin;

use SimpleXMLElement;

class FilterItem
{
    private const TYPE_STR_STARTS_WITH = 'str_starts_with';
    private const TYPE_PREG_MATCH = 'preg_match';

    private const RESULT_MAP = [
        'true' => true,
        'false' => false,
    ];

    private string $type;
    private string $value;
    private ?bool $result;

    public static function fromConfig(SimpleXMLElement $config): self
    {
        $type = (string)$config['type'];
        $value = (string)$config['value'];
        $result = (string)$config['result'] ?: 'false';
        if (empty($type) || empty($value)) {
            throw new \LogicException(
                '`filter` attributes `type` and `value` are mandatory',
                1662804181
            );
        }
        if (!array_key_exists($result, self::RESULT_MAP)) {
            throw new \LogicException(
                sprintf(
                    '`filter` attribute `result` must be either %s - given %s',
                    implode(', ', self::resultMapKeys('`')),
                    var_export($result, true)
                ),
                1662804182
            );
        }
        return new self($type, $value, self::RESULT_MAP[$result]);
    }

    public function __construct(string $type, string $value, bool $result = null)
    {
        $this->type = $type;
        $this->value = $value;
        $this->result = $result;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function getResult(): ?bool
    {
        return $this->result;
    }

    public function evaluateCodeSnippet(string $codeSnippet): ?bool
    {
        $codeSnippetResult =
            ($this->type === self::TYPE_STR_STARTS_WITH && str_starts_with($codeSnippet, $this->value))
            || ($this->type === self::TYPE_PREG_MATCH && preg_match($this->value, $codeSnippet) > 0);
        if ($codeSnippetResult) {
            return $this->result;
        }
        return null;
    }

    /**
     * @param string $wrap
     * @return list<string>
     */
    private static function resultMapKeys(string $wrap): array
    {
        return array_map(
            static fn (string $key) => $wrap . $key . $wrap,
            array_keys(self::RESULT_MAP)
        );
    }
}

<?php
declare(strict_types=1);

namespace H4ck3r31\PsalmIssueFilterPlugin;

use SimpleXMLElement;

class IssueItem
{
    private string $class;

    public static function fromConfig(SimpleXMLElement $config): self
    {
        $class = (string)$config['class'];
        if (empty($class)) {
            throw new \LogicException(
                '`issue` attributes `class` is mandatory',
                1662804191
            );
        }
        return new self($class);
    }

    public function __construct(string $class)
    {
        $this->class = $class;
    }

    public function getClass(): string
    {
        return $this->class;
    }
}

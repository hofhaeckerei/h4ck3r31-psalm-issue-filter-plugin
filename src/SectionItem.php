<?php
declare(strict_types=1);

namespace H4ck3r31\PsalmIssueFilterPlugin;

use Psalm\Issue\CodeIssue;
use SimpleXMLElement;

class SectionItem
{
    /**
     * @var list<IssueItem>
     */
    private array $issues;

    /**
     * @var list<FilterItem>
     */
    private array $filters;

    public static function fromConfig(SimpleXMLElement $config): self
    {
        if ($config === null || !isset($config->issue) || !isset($config->filter)) {
            throw new \LogicException(
                '`issue` and `filter` configuration is mandatory',
                1662804171
            );
        }
        $issueItems = [];
        foreach ($config->issue as $issueConfig) {
            $issueItems[] = IssueItem::fromConfig($issueConfig);
        }
        $filterItems = [];
        foreach ($config->filter as $filterConfig) {
            $filterItems[] = FilterItem::fromConfig($filterConfig);
        }
        return new self($issueItems, $filterItems);
    }

    public function __construct(array $issues, array $filters)
    {
        $this->issues = $issues;
        $this->filters = $filters;
    }

    public function getIssues(): array
    {
        return $this->issues;
    }

    public function getFilters(): array
    {
        return $this->filters;
    }

    public function matchesCodeIssue(CodeIssue $codeIssue): bool
    {
        foreach ($this->issues as $issueItem) {
            if (is_a($codeIssue, $issueItem->getClass(), true)) {
                return true;
            }
        }
        return false;
    }

    public function evaluateCodeSnippet(string $codeSnippet): ?bool
    {
        foreach ($this->filters as $filterItem) {
            $result = $filterItem->evaluateCodeSnippet($codeSnippet);
            if (is_bool($result)) {
                return $result;
            }
        }
        return null;
    }
}

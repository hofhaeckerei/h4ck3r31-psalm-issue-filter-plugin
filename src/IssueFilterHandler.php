<?php
declare(strict_types=1);

namespace H4ck3r31\PsalmIssueFilterPlugin;

use Psalm\Internal\Analyzer\ProjectAnalyzer;
use Psalm\Issue\CodeIssue;
use Psalm\Issue\PossiblyUndefinedIntArrayOffset;
use Psalm\Issue\PossiblyUndefinedStringArrayOffset;
use Psalm\Plugin\EventHandler\BeforeAddIssueInterface;
use Psalm\Plugin\EventHandler\Event\BeforeAddIssueEvent;

class IssueFilterHandler implements BeforeAddIssueInterface
{
    /**
     * @var list<SectionItem>
     */
    private static array $sectionItems = [];

    public static function setSectionItems(SectionItem ...$sectionItems): void
    {
        self::$sectionItems = $sectionItems;
    }

    public static function beforeAddIssue(BeforeAddIssueEvent $event): ?bool
    {
        $codeIssue = $event->getIssue();
        $project_analyzer = ProjectAnalyzer::getInstance();
        $contents = $project_analyzer->getCodebase()->file_provider->getContents($codeIssue->getFilePath());
        $location = $codeIssue->code_location;
        $codeSnippet = substr(
            $contents,
            $location->raw_file_start,
            $location->raw_file_end - $location->raw_file_start + 1
        );

        foreach (self::$sectionItems as $sectionItem) {
            if ($sectionItem->matchesCodeIssue($codeIssue)) {
                $result = $sectionItem->evaluateCodeSnippet($codeSnippet);
                if (is_bool($result)) {
                    return $result;
                }
            }
        }

        return null;
    }
}

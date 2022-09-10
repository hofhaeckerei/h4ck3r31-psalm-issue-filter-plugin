<?php
declare(strict_types=1);

namespace H4ck3r31\PsalmIssueFilterPlugin;

use Psalm\Plugin\PluginEntryPointInterface;
use Psalm\Plugin\RegistrationInterface;
use SimpleXMLElement;

class Plugin implements PluginEntryPointInterface
{
    public function __invoke(RegistrationInterface $registration, ?SimpleXMLElement $config = null): void
    {
        class_exists(IssueFilterHandler::class);
        $registration->registerHooksFromClass(IssueFilterHandler::class);
        IssueFilterHandler::setSectionItems(...$this->buildSectionItems($config));
    }

    private function buildSectionItems(?SimpleXMLElement $config): array
    {
        if ($config === null || !isset($config->section)) {
            throw new \LogicException('No `section` configuration provided', 1662804161);
        }
        $sectionItems = [];
        foreach ($config->section as $sectionConfig) {
            $sectionItems[] = SectionItem::fromConfig($sectionConfig);
        }
        return $sectionItems;
    }
}

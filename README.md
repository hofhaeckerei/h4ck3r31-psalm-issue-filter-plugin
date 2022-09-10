# Psalm Issue Filter Plugin

Allows to filter/skip code issues based on code snippets.
The configuration example below skips `PossiblyUndefinedStringArrayOffset` or
`PossiblyUndefinedIntArrayOffset` that might occur in project scenarios like:

```php
class Subject
{
    /**
     * @return SomeService
     */
    protected function getSomeService()
    {
        // there's no guarantee this service instance is given
        // however, it particular frameworks it might be like that
        // -> this plugin helps to skip these boilerplate issues
        return $GLOBALS['SOME_SERVICE'];
    }
}
```

## Configuration Directives

* `section` used for logical grouping of `issue` and `filter` items
* `issue` selecting issue class names, `filter` items shall be applied to
  + `class` mandatory, using Psalm's issue class name
* `filter` defining matching strategies concerning filtering
  + `type` matching strategy - either `str_starts_with` or `preg_match`
  + `value` the corresponding payload to be matches (adjust for actual strategy)
  + `result` (default `false`) which is the same as in Psalm's `BeforeAddIssueInterface::beforeAddIssue`
    + `true` stops event handling & keeps issue
    + `false` stops event handling & ignores issue

## Example

_in `plugin` section of `psalm.xml`_

```xml
<psalm>
    <!-- ... -->
    <plugins>
        <!-- ... -->
        <pluginClass class="H4ck3r31\PsalmIssueFilterPlugin\Plugin">
            <section>
                <issue class="Psalm\Issue\PossiblyUndefinedStringArrayOffset" />
                <issue class="Psalm\Issue\PossiblyUndefinedIntArrayOffset" />

                <filter type="str_starts_with" value="$GLOBALS" result="false" />
                <!-- same impact, using `preg_match` instead of `str_starts_with` -->
                <filter type="preg_match" value="/^\$GLOBALS/" result="true" />
            </section>
            <section>
                <!-- ... -->
            </section>
        </pluginClass>
    </plugins>
    <!-- ... -->
</psalm>
```

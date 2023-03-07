<?php declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use DH\Essentials\EasyTemplate;

class EasyTemplateTest extends TestCase {

    private $template;

    protected function setUp(): void
    {
        $this->template = new EasyTemplate(__DIR__ . "/template.html");
    }

    protected function tearDown(): void
    {
        $this->template = NULL;
    }

    public function testParser()
    {
        $rows = $this->template->getSlice("{{ROWS}}")->parse([
            "[[UID]]" => 1,
            "[[NAME]]" => "Angus Young",
            "[[AGE]]" => 66,
            "[[CITY]]" => "Melbourne",
        ]);

        $tmpl = $this->template->parse([
            "[[TITLE]]" => "ABCDEFG",
            "[[HEADER]]" => "YXCVBNM",
        ], [
            "{{ROWS}}" => $rows
        ]);

        $this->assertStringEqualsFile(__DIR__ . "/result.html", $tmpl);
    }
}
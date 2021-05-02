<?php

declare(strict_types=1);


namespace App\Tests\Service;

use App\Service\ResultAnalyser;
use PHPUnit\Framework\TestCase;

final class ResultAnalyserTest extends TestCase
{
    // Stuff left: mount, exp progress, next action location, node remainder

    private ResultAnalyser $out;

    protected function setUp(): void
    {
        $this->out = new ResultAnalyser();
    }

    /**
     * @dataProvider timeUntilNextActionProvider
     */
    public function testThatTheTimeForTheNextActionIsCorrectlyRead(string $input, int $expectedTime): void
    {
        $this->assertSame($expectedTime, $this->out->getTimeUntilNextAction($input));
    }

    public function timeUntilNextActionProvider(): array
    {
        return [
            ['invalid response', 0],
            ['<script>startTimer(43000,\'#skill-timer\', \'action_logging-400-xxx\');</script>', 43000],
            ['<script>startTimer(12345,\'#skill-timer\', \'action_logging-400-xxx\');</script>', 12345],
        ];
    }

    public function testResultParsing(): void
    {
        $fixture = file_get_contents(__DIR__ . '/Fixtures/result.html');
        $result = $this->out->parseResult($fixture);
        $this->assertSame(159, $result->gainedExperience);
        $this->assertSame('03:48:42', $result->time);
        $this->assertSame(1156, $result->materialId);
        $this->assertSame('Common', $result->materialRarity);
        $this->assertSame('Silver Birch', $result->materialName);
        $this->assertSame(2, $result->amount);
        $this->assertSame([['Drop Rate' => 1]], $result->buffs);
    }

    public function testResultParsing2(): void
    {
        $fixture = file_get_contents(__DIR__ . '/Fixtures/result2.html');
        $result = $this->out->parseResult($fixture);
        $this->assertSame(159, $result->gainedExperience);
        $this->assertSame('03:52:38', $result->time);
        $this->assertSame(1156, $result->materialId);
        $this->assertSame('Common', $result->materialRarity);
        $this->assertSame('Silver Birch', $result->materialName);
        $this->assertSame(3, $result->amount);
        $this->assertEqualsCanonicalizing([['Drop Rate' => 1], ['Mastery' => 1]], $result->buffs);
    }

    public function testResultParsing3(): void
    {
        $fixture = file_get_contents(__DIR__ . '/Fixtures/result3.html');
        $result = $this->out->parseResult($fixture);
        $this->assertSame(318, $result->gainedExperience);
        $this->assertSame('03:56:33', $result->time);
        $this->assertSame(1156, $result->materialId);
        $this->assertSame('Common', $result->materialRarity);
        $this->assertSame('Silver Birch', $result->materialName);
        $this->assertSame(1, $result->amount);
        $this->assertSame([['Double Experience' => 159]], $result->buffs);
    }

    public function testResultParsingNothing(): void
    {
        $fixture = file_get_contents(__DIR__ . '/Fixtures/nothing.html');
        $result = $this->out->parseResult($fixture);
        $this->assertSame(38, $result->gainedExperience);
        $this->assertSame('17:22:48', $result->time);
        $this->assertSame(0, $result->materialId);
        $this->assertSame('', $result->materialRarity);
        $this->assertSame('', $result->materialName);
        $this->assertSame(0, $result->amount);
        $this->assertSame([], $result->buffs);
    }

    public function testFoodParsing(): void
    {
        $fixture = file_get_contents(__DIR__ . '/Fixtures/food.html');
        $result = $this->out->parseFood($fixture);
        $this->assertSame(97, $result->level);
        $this->assertSame(11277363, $result->id);
        $this->assertSame('/images/enhancements/food3.png', $result->imageSrc);
        $this->assertSame('Epic', $result->rarity);
        $this->assertSame('1h 44m', $result->timeLeft);
    }

    public function testNoFoodParsing(): void
    {
        $fixture = file_get_contents(__DIR__ . '/Fixtures/no_food.html');
        $result = $this->out->parseFood($fixture);
        $this->assertSame(0, $result->level);
        $this->assertSame(0, $result->id);
        $this->assertSame('/images/d-50.png', $result->imageSrc);
        $this->assertSame('', $result->rarity);
        $this->assertSame('', $result->timeLeft);
    }

    public function testIsNodeDepleted(): void
    {
        $fixture = file_get_contents(__DIR__ . '/Fixtures/node_depleted.html');
        $this->assertTrue($this->out->isNodeDepleted($fixture));
        $this->assertFalse($this->out->isNodeDepleted('<div>Some random html that does not say that the node is depleted</div>'));
    }

    public function testIsError(): void
    {
        $fixture = file_get_contents(__DIR__ . '/Fixtures/error.html');
        $this->assertTrue($this->out->hasError($fixture));
        $this->assertFalse($this->out->hasError('<div>Some random html response that is okay</div>'));
    }
}

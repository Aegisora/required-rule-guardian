<?php

namespace Aegisora\RuleGuardians\RequiredRule\Tests\Unit;

use Aegisora\Guardian\Exceptions\GuardianExecutingRuleException;
use Aegisora\Guardian\Exceptions\GuardianValidationException;
use Aegisora\Guardian\Guardian;
use Aegisora\RuleGuardians\RequiredRule\RequiredRuleGuardian;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use stdClass;
use Throwable;

class RequiredRuleGuardianTest extends TestCase
{
    private RequiredRuleGuardian $guardian;

    protected function setUp(): void
    {
        parent::setUp();

        $this->guardian = new RequiredRuleGuardian(
            new Guardian()
        );
    }

    /**
     * @dataProvider getSuccessfullyCheckProvidedData
     * @param mixed $value
     */
    public function testSuccessfullyCheck(
        $value
    ): void {
        $this->expectNotToPerformAssertions();

        $this->guardian->check($value);
    }

    public static function getSuccessfullyCheckProvidedData(): array
    {
        return [
            'value - zero integer' => [
                'value' => 0,
            ],
            'value - positive integer' => [
                'value' => 1,
            ],
            'value - negative integer' => [
                'value' => -1,
            ],
            'value - zero float' => [
                'value' => 0.0,
            ],
            'value - negative float' => [
                'value' => -0.01,
            ],
            'value - false' => [
                'value' => false,
            ],
            'value - true' => [
                'value' => true,
            ],
            'value - empty string' => [
                'value' => '',
            ],
            'value - not empty string' => [
                'value' => 'foo',
            ],
            'value - zero string' => [
                'value' => '0',
            ],
            'value - empty array' => [
                'value' => [],
            ],
            'value - not empty array' => [
                'value' => [1, 2, 3],
            ],
            'value - object' => [
                'value' => new stdClass(),
            ],
            'value - callable' => [
                'value' => static function () {
                },
            ],
        ];
    }

    /**
     * @dataProvider getFailedCheckProvidedData
     * @param mixed $value
     */
    public function testFailedCheck(
        $value,
        ?Throwable $customRuleValidationException,
        string $expectedExceptionClassName
    ): void {
        $this->expectException($expectedExceptionClassName);

        $this->guardian->check($value, $customRuleValidationException);
    }

    public static function getFailedCheckProvidedData(): array
    {
        return [
            'value - null, custom rule validation exception - null' => [
                'value' => null,
                'customRuleValidationException' => null,
                'expectedExceptionClassName' => GuardianValidationException::class,
            ],
            'value - null, custom rule validation exception - not null' => [
                'value' => null,
                'customRuleValidationException' => new CustomRuleException(),
                'expectedExceptionClassName' => CustomRuleException::class,
            ],
        ];
    }

    public function testFailedCheckWithDefaultCustomException(): void
    {
        $this->expectException(GuardianValidationException::class);

        try {
            $this->guardian->check(null);
        } catch (GuardianValidationException $exception) {
            self::assertSame('required_rule', $exception->getRuleCode());
            throw $exception;
        }
    }

    public function testFailedCheckCauseGuardianThrowsGuardianExecutingRuleException(): void
    {
        $this->expectException(GuardianExecutingRuleException::class);

        $guardian = new RequiredRuleGuardian(
            $this->getGuardianThrowsExceptionOnCheck(GuardianExecutingRuleException::class)
        );

        $guardian->check('foo');
    }

    public function testFailedCheckCauseGuardianThrowsNotExpectedException(): void
    {
        $this->expectException(Throwable::class);

        $guardian = new RequiredRuleGuardian(
            $this->getGuardianThrowsExceptionOnCheck(Throwable::class)
        );

        $guardian->check('foo');
    }

    /**
     * @return Guardian|MockObject
     */
    private function getGuardianThrowsExceptionOnCheck(string $expectedExceptionClass): Guardian
    {
        $guardian = $this->getGuardianMock();

        $guardian
            ->expects(self::once())
            ->method('check')
            ->willThrowException($this->createMock($expectedExceptionClass));

        return $guardian;
    }

    /**
     * @return Guardian|MockObject
     */
    private function getGuardianMock(): Guardian
    {
        /** @var Guardian|MockObject $mock */
        $mock = $this->createMock(Guardian::class);

        return $mock;
    }
}

<?php

namespace Trikoder\JsonApiBundle\Tests\Unit\Services\ModelInput;

use PHPUnit\Framework\TestCase;
use Trikoder\JsonApiBundle\Controller\Traits\Actions\IndexTrait;

class IndexTraitTest extends TestCase
{
    public function testCalculatePagesForLimitOffsetOnePage()
    {
        $testClass = new TestIndexTraitClass();

        // one page results
        $testResult = $testClass->callCalculatePagesForLimitOffset(0, 10, 5);
        $this->assertEquals([
            'self' => ['limit' => 10, 'offset' => 0],
            'prev' => null,
            'next' => null,
            'first' => ['limit' => 10, 'offset' => 0],
            'last' => ['limit' => 10, 'offset' => 0],
        ], $testResult);

        $testResult = $testClass->callCalculatePagesForLimitOffset(0, 10, 10);
        $this->assertEquals([
            'self' => ['limit' => 10, 'offset' => 0],
            'prev' => null,
            'next' => null,
            'first' => ['limit' => 10, 'offset' => 0],
            'last' => ['limit' => 10, 'offset' => 0],
        ], $testResult);
    }

    public function testCalculatePagesForLimitOffsetMultiplePages()
    {
        $testClass = new TestIndexTraitClass();

        // first page with multiple pages
        $testResult = $testClass->callCalculatePagesForLimitOffset(0, 10, 100);
        $this->assertEquals([
            'self' => ['limit' => 10, 'offset' => 0],
            'prev' => null,
            'next' => ['limit' => 10, 'offset' => 10],
            'first' => ['limit' => 10, 'offset' => 0],
            'last' => ['limit' => 10, 'offset' => 90],
        ], $testResult);
    }

    public function testCalculatePagesForLimitOffsetSecondPage()
    {
        $testClass = new TestIndexTraitClass();

        // second page
        $testResult = $testClass->callCalculatePagesForLimitOffset(10, 10, 100);
        $this->assertEquals([
            'self' => ['limit' => 10, 'offset' => 10],
            'prev' => ['limit' => 10, 'offset' => 0],
            'next' => ['limit' => 10, 'offset' => 20],
            'first' => ['limit' => 10, 'offset' => 0],
            'last' => ['limit' => 10, 'offset' => 90],
        ], $testResult);
    }

    public function testCalculatePagesForLimitOffsetLastPage()
    {
        $testClass = new TestIndexTraitClass();

        // last page
        $testResult = $testClass->callCalculatePagesForLimitOffset(90, 10, 100);
        $this->assertEquals([
            'self' => ['limit' => 10, 'offset' => 90],
            'prev' => ['limit' => 10, 'offset' => 80],
            'next' => null,
            'first' => ['limit' => 10, 'offset' => 0],
            'last' => ['limit' => 10, 'offset' => 90],
        ], $testResult);
    }

    public function testCalculatePagesForLimitOffsetOutOfBounds()
    {
        $testClass = new TestIndexTraitClass();

        // out of bounds
        $testResult = $testClass->callCalculatePagesForLimitOffset(110, 10, 100);
        $this->assertEquals([
            'self' => ['limit' => 10, 'offset' => 110],
            'prev' => ['limit' => 10, 'offset' => 100],
            'next' => null,
            'first' => ['limit' => 10, 'offset' => 0],
            'last' => ['limit' => 10, 'offset' => 90],
        ], $testResult);
    }

    public function testCalculatePagesForPageSizeOnePage()
    {
        $testClass = new TestIndexTraitClass();

        // one page results
        $testResult = $testClass->callCalculatePagesForPageSize(0, 10, 5);
        $this->assertEquals([
            'self' => ['size' => 10, 'number' => 1],
            'prev' => null,
            'next' => null,
            'first' => ['size' => 10, 'number' => 1],
            'last' => ['size' => 10, 'number' => 1],
        ], $testResult);

        $testResult = $testClass->callCalculatePagesForPageSize(0, 10, 10);
        $this->assertEquals([
            'self' => ['size' => 10, 'number' => 1],
            'prev' => null,
            'next' => null,
            'first' => ['size' => 10, 'number' => 1],
            'last' => ['size' => 10, 'number' => 1],
        ], $testResult);
    }

    public function testCalculatePagesForPageSizeMultiplePages()
    {
        $testClass = new TestIndexTraitClass();

        // first page with multiple pages
        $testResult = $testClass->callCalculatePagesForPageSize(0, 10, 100);
        $this->assertEquals([
            'self' => ['size' => 10, 'number' => 1],
            'prev' => null,
            'next' => ['size' => 10, 'number' => 2],
            'first' => ['size' => 10, 'number' => 1],
            'last' => ['size' => 10, 'number' => 10],
        ], $testResult);
    }

    public function testCalculatePagesForPageSizeSecondPage()
    {
        $testClass = new TestIndexTraitClass();

        // second page
        $testResult = $testClass->callCalculatePagesForPageSize(10, 10, 100);
        $this->assertEquals([
            'self' => ['size' => 10, 'number' => 2],
            'prev' => ['size' => 10, 'number' => 1],
            'next' => ['size' => 10, 'number' => 3],
            'first' => ['size' => 10, 'number' => 1],
            'last' => ['size' => 10, 'number' => 10],
        ], $testResult);
    }

    public function testCalculatePagesForPageSizeLastPage()
    {
        $testClass = new TestIndexTraitClass();

        // last page
        $testResult = $testClass->callCalculatePagesForPageSize(90, 10, 100);
        $this->assertEquals([
            'self' => ['size' => 10, 'number' => 10],
            'prev' => ['size' => 10, 'number' => 9],
            'next' => null,
            'first' => ['size' => 10, 'number' => 1],
            'last' => ['size' => 10, 'number' => 10],
        ], $testResult);
    }

    public function testCalculatePagesForPageSizeOutOfBounds()
    {
        $testClass = new TestIndexTraitClass();

        // out of bounds
        $testResult = $testClass->callCalculatePagesForPageSize(110, 10, 100);
        $this->assertEquals([
            'self' => ['size' => 10, 'number' => 12],
            'prev' => ['size' => 10, 'number' => 11],
            'next' => null,
            'first' => ['size' => 10, 'number' => 1],
            'last' => ['size' => 10, 'number' => 10],
        ], $testResult);
    }
}

class TestIndexTraitClass
{
    public function callCalculatePagesForLimitOffset($offset, $limit, $total)
    {
        return $this->calculatePagesForLimitOffset($offset, $limit, $total);
    }

    public function callCalculatePagesForPageSize($offset, $limit, $total)
    {
        return $this->calculatePagesForPageSize($offset, $limit, $total);
    }

    use IndexTrait;
}

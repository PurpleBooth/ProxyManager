<?php

declare(strict_types=1);

namespace ProxyManagerTest\ProxyGenerator\AccessInterceptorScopeLocalizer\MethodGenerator;

use PHPUnit_Framework_TestCase;
use ProxyManager\ProxyGenerator\AccessInterceptorScopeLocalizer\MethodGenerator\MagicSleep;
use ProxyManagerTestAsset\ClassWithMagicMethods;
use ProxyManagerTestAsset\EmptyClass;
use ReflectionClass;
use Zend\Code\Generator\PropertyGenerator;

/**
 * Tests for {@see \ProxyManager\ProxyGenerator\AccessInterceptorScopeLocalizer\MethodGenerator\MagicSleep}
 *
 * @author Marco Pivetta <ocramius@gmail.com>
 * @license MIT
 *
 * @group Coverage
 */
class MagicSleepTest extends PHPUnit_Framework_TestCase
{
    /**
     * @covers \ProxyManager\ProxyGenerator\AccessInterceptorScopeLocalizer\MethodGenerator\MagicSleep::__construct
     */
    public function testBodyStructure() : void
    {
        $reflection         = new ReflectionClass(EmptyClass::class);
        /* @var $prefixInterceptors PropertyGenerator|\PHPUnit_Framework_MockObject_MockObject */
        $prefixInterceptors = $this->createMock(PropertyGenerator::class);
        /* @var $suffixInterceptors PropertyGenerator|\PHPUnit_Framework_MockObject_MockObject */
        $suffixInterceptors = $this->createMock(PropertyGenerator::class);

        $prefixInterceptors->expects(self::any())->method('getName')->will(self::returnValue('pre'));
        $suffixInterceptors->expects(self::any())->method('getName')->will(self::returnValue('post'));

        $magicGet = new MagicSleep(
            $reflection,
            $prefixInterceptors,
            $suffixInterceptors
        );

        self::assertSame('__sleep', $magicGet->getName());
        self::assertEmpty($magicGet->getParameters());
        self::assertStringMatchesFormat('%a$returnValue = array_keys((array) $this);%a', $magicGet->getBody());
    }

    /**
     * @covers \ProxyManager\ProxyGenerator\AccessInterceptorScopeLocalizer\MethodGenerator\MagicSleep::__construct
     */
    public function testBodyStructureWithInheritedMethod() : void
    {
        $reflection         = new ReflectionClass(ClassWithMagicMethods::class);
        /* @var $prefixInterceptors PropertyGenerator|\PHPUnit_Framework_MockObject_MockObject */
        $prefixInterceptors = $this->createMock(PropertyGenerator::class);
        /* @var $suffixInterceptors PropertyGenerator|\PHPUnit_Framework_MockObject_MockObject */
        $suffixInterceptors = $this->createMock(PropertyGenerator::class);

        $prefixInterceptors->expects(self::any())->method('getName')->will(self::returnValue('pre'));
        $suffixInterceptors->expects(self::any())->method('getName')->will(self::returnValue('post'));

        $magicGet = new MagicSleep(
            $reflection,
            $prefixInterceptors,
            $suffixInterceptors
        );

        self::assertSame('__sleep', $magicGet->getName());
        self::assertEmpty($magicGet->getParameters());
        self::assertStringMatchesFormat('%a$returnValue = & parent::__sleep();%a', $magicGet->getBody());
    }
}

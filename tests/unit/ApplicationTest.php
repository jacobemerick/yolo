<?php

namespace Yolo;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ApplicationTest extends \PHPUnit_Framework_TestCase
{
    /** @test @dataProvider provideRouteMethods */
    public function routeBuildingShouldDelegate($method)
    {
        $builder = $this->createRouteBuilderExpecting($method, '/');
        $container = $this->createContainer(['route_builder' => $builder]);

        $app = new Application($container);
        $app->$method('/', function () {});
    }

    public function provideRouteMethods()
    {
        return [
            ['get'],
            ['post'],
            ['put'],
            ['delete'],
            ['match'],
        ];
    }

    /** @test */
    public function runShouldDelegate()
    {
        $front = $this->getMockBuilder('Yolo\FrontController')
                      ->disableOriginalConstructor()
                      ->getMock();
        $front->expects($this->once())
              ->method('run');

        $container = $this->createContainer(['front_controller' => $front]);

        $request = Request::create('/');
        $app = new Application($container);
        $app->run($request);
    }

    public function testGetContainer()
    {
        $container = $this->createContainer();
        $app = new Application($container);

        $this->assertSame($container, $app->getContainer());
    }

    private function createContainer(array $services = [])
    {
        $container = $this->getMock('Symfony\Component\DependencyInjection\ContainerInterface');

        foreach ($services as $name => $service) {
            $container->expects($this->any())
                      ->method('get')
                      ->with($name)
                      ->will($this->returnValue($service));
        }

        return $container;
    }

    private function createRouteBuilderExpecting($method, $path)
    {
        $builder = $this->getMockBuilder('Yolo\RouteBuilder')
                        ->disableOriginalConstructor()
                        ->getMock();
        $builder->expects($this->once())
                ->method($method)
                ->with($path, $this->isInstanceOf('Closure'));

        return $builder;
    }
}

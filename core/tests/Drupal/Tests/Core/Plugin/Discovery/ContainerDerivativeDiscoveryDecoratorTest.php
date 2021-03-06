<?php

/**
 * @file
 * Contains \Drupal\Tests\Core\Plugin\Discovery\ContainerDerivativeDiscoveryDecoratorTest.
 */

namespace Drupal\Tests\Core\Plugin\Discovery;

use Drupal\Core\Plugin\Discovery\ContainerDerivativeDiscoveryDecorator;
use Drupal\Tests\UnitTestCase;

/**
 * Tests the container aware derivative discovery decorator.
 */
class ContainerDerivativeDiscoveryDecoratorTest extends UnitTestCase {

  /**
   * {@inheritdoc}
   */
  public static function getInfo() {
    return array(
      'name' => 'Container aware derivative discovery decorator.',
      'description' => 'Tests the container aware derivative discovery decorator.',
      'group' => 'Plugin',
    );
  }

  /**
   * Tests the getDerivativeFetcher method.
   *
   * @see \Drupal\Core\Plugin\Discovery\ContainerDerivativeDiscoveryDecorator::getDerivativeFetcher().
   */
  public function testGetDerivativeFetcher() {
    $example_service = $this->getMock('Symfony\Component\EventDispatcher\EventDispatcherInterface');
    $example_container = $this->getMockBuilder('Symfony\Component\DependencyInjection\ContainerBuilder')
      ->setMethods(array('get'))
      ->getMock();
    $example_container->expects($this->once())
      ->method('get')
      ->with($this->equalTo('example_service'))
      ->will($this->returnValue($example_service));

    \Drupal::setContainer($example_container);

    $definitions = array();
    $definitions['container_aware_discovery'] = array(
      'id' => 'container_aware_discovery',
      'derivative' => '\Drupal\Tests\Core\Plugin\Discovery\TestContainerDerivativeDiscovery',
    );
    $definitions['non_container_aware_discovery'] = array(
      'id' => 'non_container_aware_discovery',
      'derivative' => '\Drupal\Tests\Core\Plugin\Discovery\TestDerivativeDiscovery',
    );

    $discovery_main = $this->getMock('Drupal\Component\Plugin\Discovery\DiscoveryInterface');
    $discovery_main->expects($this->any())
      ->method('getDefinitions')
      ->will($this->returnValue($definitions));

    $discovery = new ContainerDerivativeDiscoveryDecorator($discovery_main);
    $definitions = $discovery->getDefinitions();

    // Ensure that both the instances from container and non-container test derivatives got added.
    $this->assertEquals(4, count($definitions));
  }

}

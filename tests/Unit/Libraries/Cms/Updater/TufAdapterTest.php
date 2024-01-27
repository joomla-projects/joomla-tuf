<?php

/**
 * @package     Joomla.UnitTest
 * @subpackage  Updater
 *
 * @copyright   (C) 2019 Open Source Matters, Inc. <https://www.joomla.org>
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Joomla\Tests\Unit\Libraries\Cms\Updater;

use Joomla\CMS\Application\SiteApplication;
use Joomla\CMS\Updater\Adapter\TufAdapter;
use Joomla\Tests\Unit\UnitTestCase;
use Joomla\Utilities\ArrayHelper;
use Tuf\Exception\MetadataException;

class TufAdapterTest extends UnitTestCase
{
    public function testProcessTufTargetThrowsExceptionIfHashesAreMissing()
    {
        $this->expectException(MetadataException::class);
        $this->expectExceptionMessage("No trusted hashes are available for 'nohash.json'");

        $object = $this->getMockBuilder(TufAdapter::class)
            ->disableOriginalConstructor()
            ->getMock();

        $method = $this->getPublicMethod($object, 'processTufTarget');
        $method->invoke($object, 'nohash.json', []);
    }

    public function testProcesstuftargetAssignsCustomTargetKeys()
    {
        $object = $this->getMockBuilder(TufAdapter::class)
            ->disableOriginalConstructor()
            ->getMock();

        $method = $this->getPublicMethod($object, 'processTufTarget');
        $result = $method->invoke($object, 'targets.json', $this->getMockTarget([
            'custom' => [
                'name' => 'Testupdate',
                'version' => '1.2.3',
            ],
        ]));

        $this->assertSame('Testupdate', $result['name']);
        $this->assertSame('1.2.3', $result['version']);
    }

    public function testProcesstuftargetAssignsClientId()
    {
        $object = $this->getMockBuilder(TufAdapter::class)
            ->disableOriginalConstructor()
            ->getMock();


        $method = $this->getPublicMethod($object, 'processTufTarget');
        $result = $method->invoke($object, 'targets.json', $this->getMockTarget([
            'client' => 'site'
        ]));

        $this->assertSame(0, $result['client']);
    }

    public function testProcesstuftargetAssignsInfoUrl()
    {
        $object = $this->getMockBuilder(TufAdapter::class)
            ->disableOriginalConstructor()
            ->getMock();


        $method = $this->getPublicMethod($object, 'processTufTarget');
        $result = $method->invoke($object, 'targets.json', $this->getMockTarget([
            'custom' => [
                'infourl' => [
                    'url' => 'https://example.org'
                ]
            ]
        ]));

        $this->assertSame('https://example.org', $result['infourl']);
    }

    /**
     * Internal helper method to get access to protected methods
     *
     * @since   __DEPLOY_VERSION__
     *
     * @param $object
     * @param $method
     *
     * @return \ReflectionMethod
     * @throws \ReflectionException
     */
    protected function getPublicMethod($object, $method)
    {
        $reflectionClass = new \ReflectionClass($object);
        $method = $reflectionClass->getMethod($method);
        $method->setAccessible(true);

        return $method;
    }

    protected function getMockTarget($overrides)
    {
        return ArrayHelper::mergeRecursive(
            [
                'hashes' => [
                    'sha128' => ''
                ],
                'custom' => [
                    'name' => 'Joomla',
                    'type' => 'file',
                    'version' => '1.2.3',
                    'targetplatform' => [
                        'name' => 'joomla',
                        'version' => '(5\.[0-4])|^(4\.4)'
                    ],
                    'php_minimum' => '8.1.0',
                    'channel' => '5.x',
                    'stability' => 'stable',
                    'supported_databases' => [
                        'mariadb' => '10.4'
                    ]
                ]
            ],
            $overrides
        );
    }
}

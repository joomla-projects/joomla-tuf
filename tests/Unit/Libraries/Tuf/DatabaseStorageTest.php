<?php

/**
 * @package     Joomla.UnitTest
 * @subpackage  Tuf
 *
 * @copyright   (C) 2019 Open Source Matters, Inc. <https://www.joomla.org>
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Joomla\Tests\Unit\Libraries\Tuf;

use Joomla\CMS\Table\Tuf;
use Joomla\CMS\TUF\DatabaseStorage;
use Joomla\Tests\Unit\UnitTestCase;

/**
 * Test class for DatabaseStorage
 *
 * @package     Joomla.UnitTest
 * @subpackage  Tuf
 * @since       __DEPLOY_VERSION__
 */
class DatabaseStorageTest extends UnitTestCase
{
    /**
     * @return void
     *
     * @since   __DEPLOY_VERSION__
     */
    public function testContructorWritesColumnMetadataToInternalStorage()
    {
        $table  = $this->getTableMock(['root' => 'rootfoo']);
        $object = new DatabaseStorage($table);

        $this->assertEquals('rootfoo', $this->getInternalStorageValue($object)['root']);
    }

    /**
     * @return void
     *
     * @since   __DEPLOY_VERSION__
     */
    public function testContructorIgnoresNonMetadataColumns()
    {
        $table  = $this->getTableMock(['foobar' => 'aaa']);
        $object = new DatabaseStorage($table);

        $this->assertArrayNotHasKey('foobar', $this->getInternalStorageValue($object));
    }

    /**
     * @return void
     *
     * @since   __DEPLOY_VERSION__
     */
    public function testReadReturnsStorageValueForExistingColumns()
    {
        $object = new DatabaseStorage($this->getTableMock(['root' => 'foobar']));
        $this->assertEquals('foobar', $object->read('root'));
    }

    /**
     * @return void
     *
     * @since   __DEPLOY_VERSION__
     */
    public function testReadReturnsNullForNonexistentColumns()
    {
        $object = new DatabaseStorage($this->getTableMock([]));
        $this->assertNull($object->read('foobar'));
    }

    /**
     * @return void
     *
     * @since   __DEPLOY_VERSION__
     */
    public function testWriteUpdatesGivenInternalStorageValue()
    {
        $object = new DatabaseStorage($this->getTableMock(['root' => 'foo']));
        $object->write('root', 'bar');

        $this->assertEquals('bar', $this->getInternalStorageValue($object)['root']);
    }

    /**
     * @return void
     *
     * @since   __DEPLOY_VERSION__
     */
    public function testWriteCreatesNewInternalStorageValue()
    {
        $object = new DatabaseStorage($this->getTableMock(['root' => 'foo']));
        $object->write('targets', 'bar');

        $this->assertEquals('bar', $this->getInternalStorageValue($object)['targets']);
    }

    /**
     * @return void
     *
     * @since   __DEPLOY_VERSION__
     */
    public function testDeleteRemovesRowFromInternalStorage()
    {
        $object = new DatabaseStorage($this->getTableMock(['root' => 'foo']));
        $object->delete('root');

        $this->assertArrayNotHasKey('root', $this->getInternalStorageValue($object));
    }

    /**
     * @return void
     *
     * @since   __DEPLOY_VERSION__
     */
    public function testPersistUpdatesTableObjectState()
    {
        $tableMock = $this->getTableMock(['root' => 'foo', 'targets' => 'Joomla', 'nonexistent' => 'value']);

        $tableMock
            ->expects($this->once())
            ->method('save')
            ->with(['root' => 'foo', 'targets' => 'Joomla'])
            ->willReturn(true);

        $object = new DatabaseStorage($tableMock);
        $this->assertTrue($object->persist());
    }

    /**
     * @param array $mockData
     *
     * @since   __DEPLOY_VERSION__
     *
     * @return Tuf|(Tuf&\PHPUnit\Framework\MockObject\MockObject)|\PHPUnit\Framework\MockObject\MockObject
     */
    protected function getTableMock(array $mockData)
    {
        $table = $this->createMock(Tuf::class);

        // Write mock data to mock table
        foreach (DatabaseStorage::METADATA_COLUMNS as $column) {
            $table->$column = (!empty($mockData[$column])) ? $mockData[$column] : null;
        }

        return $table;
    }

    /**
     * @param $class
     *
     * @since   __DEPLOY_VERSION__
     *
     * @return mixed
     */
    protected function getInternalStorageValue($class)
    {
        $reflectionProperty = new \ReflectionProperty(DatabaseStorage::class, 'container');

        return $reflectionProperty->getValue($class);
    }
}

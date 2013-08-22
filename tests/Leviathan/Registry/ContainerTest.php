<?php
namespace Leviathan\Registry;

class ContainerTest extends \PHPUnit_Framework_TestCase
{

    use DataProviderTrait;
    use FlattenerTrait;

    public function setUp()
    {
        /**
         * @var Container
         */
        $this->object = new Container;
    }

    public function tearDown()
    {
        //$this->object = null;
    }

    public function testSet()
    {
        $storeResult = $this->object->set('name', 'Registry');
        $this->assertEquals(true, $storeResult);
    }

    /**
     * @dataProvider storageProvider
     */
    public function testGet($test, $expected)
    {
        $expectedKey = key(array_reverse($expected));
        $valueToTest = current(array_reverse($this->flatten($test)));
        $this->object->fill($test);
        $this->assertEquals($valueToTest, $this->object->get($expectedKey));
    }

    /**
     * @dataProvider storageProvider
     */
    public function testRemove($test, $expected)
    {
        $expectedKey = key(array_reverse($expected));
        $this->object->fill($test);
        $expectedValue = $expected[$expectedKey];
        $this->object->set('my.var', rand(1, 9999));
        $this->assertInternalType('boolean', $this->object->exists($expectedKey));
        $this->assertTrue($this->object->exists($expectedKey));
        $this->object->remove($expectedKey);
        $this->assertNull($this->object->get($expectedKey));
        $this->assertNotEquals($expectedValue, $this->object->get($expectedKey));
        $this->assertFalse($this->object->exists($expectedKey));
    }

    /**
     * @dataProvider storageProvider
     */
    public function testNonRetrieve($test)
    {
        $this->object->fill($test);
        $this->assertEquals(null, $this->object->get('c.old.namespace'));
    }

    public function testReturnNode()
    {
        $storable = [
            'db' => [
                'dsn' => 'mysql:host=localhost;dbname=my_db',
                'username' => 'db_user',
                'password' => 'someAwesomePlainPassword'
            ],
            'library' => [
                'vendor' => [
                    'leviathan' => [
                        'projects' => [
                            'registry' => [
                                'author' => 'Duodraco',
                                'version' => '0.2.0',
                                'description' => 'Registry container'
                            ]
                        ]
                    ]
                ]
            ]
        ];
        $this->object->fill($storable);
        $this->assertEquals((object)$storable['db'], $this->object->get('db'));
        $this->assertEquals(
            (object)$storable['library']['vendor']['leviathan']['projects']['registry'],
            $this->object->get('library.vendor.leviathan.projects.registry')
        );
    }

}

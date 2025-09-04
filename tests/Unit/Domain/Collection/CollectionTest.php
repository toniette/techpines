<?php

use App\Domain\Collection\Collection;
use App\Domain\Exception\InvalidItemTypeException;

test('it throws an exception when adding an invalid item', function () {
    $this->expectException(InvalidItemTypeException::class);

    $collection = new class extends Collection
    {
        protected ?string $type = StdClass::class;
    };

    $testObject = new class extends Exception
    {
        public function __construct()
        {
            parent::__construct('Test exception');
        }
    };

    $collection->attach($testObject);
});

test('offsetSet should use attach method', function () {
    $collection = new class extends Collection
    {
        protected ?string $type = StdClass::class;
    };

    $testObject = new StdClass;

    $collection->offsetSet($testObject);

    $this->assertTrue($collection->contains($testObject));
});

test('toArray should return array of objects', function () {
    $collection = new class extends Collection
    {
        protected ?string $type = StdClass::class;
    };

    $testObject = new StdClass;

    $collection->offsetSet($testObject);

    $this->assertEquals([$testObject], $collection->toArray());
});

test('addAll attach objects to the collection', function () {
    $collection = new class extends Collection
    {
        protected ?string $type = StdClass::class;
    };

    $storage = new SplObjectStorage;

    $testObject1 = new StdClass;
    $testObject2 = new StdClass;
    $storage->attach($testObject1);
    $storage->attach($testObject2);

    $collection->addAll($storage);

    $this->assertTrue($collection->contains($testObject1));
    $this->assertTrue($collection->contains($testObject2));
});

test('from method returns a new collection instance', function () {
    $collection = new class extends Collection
    {
        protected ?string $type = StdClass::class;
    };

    $testObject = new StdClass;

    $newCollection = $collection::from($testObject);

    $this->assertInstanceOf(Collection::class, $newCollection);
    $this->assertTrue($newCollection->contains($testObject));
});

test('toJson returns a valid json string', function () {
    $collection = new class extends Collection
    {
        protected ?string $type = StdClass::class;
    };

    $testObject = new StdClass;

    $collection->offsetSet($testObject);

    $this->assertEquals(json_encode([$testObject]), $collection->toJson());
});

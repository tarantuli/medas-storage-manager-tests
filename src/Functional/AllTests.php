<?php

declare(strict_types=1);

namespace Medas\StorageManagerTests\Functional;

trait AllTests
{
    use DefaultValuesTest;
    use ConsoleCommandsTest;
    use DatabaseManagerTest;
    use EntityPersisterTest;
    use EnumTest;
    use HandledPropertyTest;
    use GuidTest;
    use HydratorTest;
    use InheritanceTest;
    use ManyToManyRelationTest;
    use OneToManyRelationTest;
    use PropertyHandlerTest;
    use TimestampsTest;
    use UnsortedTest;
}

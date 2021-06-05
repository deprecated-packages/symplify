<?php

namespace Symplify\PHPStanRules\Tests\Rules\NoGetRepositoryOutsideConstructorRule\Fixture;

use Symplify\PHPStanRules\Tests\Rules\NoGetRepositoryOutsideConstructorRule\Source\SomeNonDoctrine;
use Symplify\PHPStanRules\Tests\Rules\NoGetRepositoryOutsideConstructorRule\Source\TestRepository;

final class SkipNonDoctrineRepository
{
    /**
     * @var SomeNonDoctrine
     */
    private $someNonDoctrine;

    public function __construct(SomeNonDoctrine $someNonDoctrine)
    {
        $this->someNonDoctrine = $someNonDoctrine;
    }

    public function find()
    {
        return $this->someNonDoctrine->getRepository(TestRepository::class)->findAll();
    }
}

<?php declare(strict_types=1);

namespace Symplify\PHP7_CodeSniffer\Sniff;

use Symplify\PHP7_CodeSniffer\Repository\SniffRepository;
use Symplify\PHP7_CodeSniffer\Validator\GroupValidator;

final class SniffCollectionResolver
{
    /**
     * @var GroupValidator
     */
    private $standardsOptionResolver;

    /**
     * @var SniffRepository
     */
    private $sniffRepository;

    public function __construct(
        GroupValidator $standardsOptionResolver,
        SniffRepository $sniffRepository
    ) {
        $this->standardsOptionResolver = $standardsOptionResolver;
        $this->sniffRepository = $sniffRepository;
    }

    /**
     * @return string[]
     */
    public function resolve(array $groups, array $sniffs, array $excludedSniffs) : array
    {
        $sniffClasses = [];
        if (count($groups)) {
            $this->standardsOptionResolver->ensureGroupsExist($groups);
            foreach ($groups as $group) {
                $sniffClasses += $this->sniffRepository->getByGroup($group);
            }
        }

        if (count($sniffs)) {
            dump('EEE');
            dump($sniffs);
            die;
        }

        if (count($excludedSniffs)) {
            dump('EEE');
            dump($excludedSniffs);
            die;
        }

        return $sniffClasses;
    }
}

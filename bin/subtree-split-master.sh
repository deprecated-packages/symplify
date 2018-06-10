#!/usr/bin/env bash
git subsplit init .git

git subsplit publish --heads=master --tags=none packages/BetterPhpDocParser:git@github.com:Symplify/BetterPhpDocParser.git
git subsplit publish --heads=master --tags=none packages/PackageBuilder:git@github.com:Symplify/PackageBuilder.git
git subsplit publish --heads=master --tags=none packages/TokenRunner:git@github.com:Symplify/TokenRunner.git
git subsplit publish --heads=master --tags=none packages/EasyCodingStandard:git@github.com:Symplify/EasyCodingStandard.git
git subsplit publish --heads=master --tags=none packages/EasyCodingStandardTester:git@github.com:Symplify/EasyCodingStandardTester.git
git subsplit publish --heads=master --tags=none packages/CodingStandard:git@github.com:Symplify/CodingStandard.git
git subsplit publish --heads=master --tags=none packages/Statie:git@github.com:Symplify/Statie.git
git subsplit publish --heads=master --tags=none packages/ChangelogLinker:git@github.com:Symplify/ChangelogLinker.git
git subsplit publish --heads=master --tags=none packages/MonorepoBuilder:git@github.com:Symplify/MonorepoBuilder.git

rm -rf .subsplit

# inspired by laravel: https://github.com/laravel/framework/blob/5.4/build/illuminate-split-full.sh
# they use SensioLabs now though: https://github.com/laravel/framework/pull/17048#issuecomment-269915319

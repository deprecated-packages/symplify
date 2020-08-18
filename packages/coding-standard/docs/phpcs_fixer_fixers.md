# PHP CS Fixer - Fixers

### Indent nested Annotations to Newline

- class: [`Symplify\CodingStandard\Fixer\Annotation\NewlineInNestedAnnotationFixer`](packages/coding-standard/src/Fixer/Annotation/NewlineInNestedAnnotationFixer.php)

```php
<?php

// ecs.php

declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();
    $services->set(Symplify\CodingStandard\Fixer\Annotation\NewlineInNestedAnnotationFixer::class);
};
```

```diff
 use Doctrine\ORM\Mapping as ORM;

 /**
- * @ORM\Table(name="user", indexes={@ORM\Index(name="user_id", columns={"another_id"})})
+ * @ORM\Table(name="user", indexes={
+ *     @ORM\Index(name="user_id", columns={"another_id"})
+ * })
  */
 class SomeEntity
 {
 }
```

<br>

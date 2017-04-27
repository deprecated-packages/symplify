<?php declare(strict_types=1);

namespace Symplify\ControllerAutowire\Controller\Form;

use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;

trait ControllerFormTrait
{
    /**
     * @var FormFactoryInterface
     */
    private $formFactory;

    public function setFormFactory(FormFactoryInterface $formFactory): void
    {
        $this->formFactory = $formFactory;
    }

    /**
     * @param mixed $data
     * @param mixed[] $options
     */
    protected function createForm(string $type, $data = null, array $options = []): FormInterface
    {
        return $this->formFactory->create($type, $data, $options);
    }

    /**
     * @param mixed $data
     * @param mixed[] $options
     */
    protected function createFormBuilder($data = null, array $options = []): FormBuilderInterface
    {
        return $this->formFactory->createBuilder(FormType::class, $data, $options);
    }

    /**
     * @param mixed[]|null $data
     * @param mixed[] $options
     */
    protected function createNamedBuilder(
        string $name,
        string $type = 'Symfony\Component\Form\Extension\Core\Type\FormType',
        ?array $data = null,
        array $options = []
    ): FormBuilderInterface {
        return $this->formFactory->createNamedBuilder($name, $type, $data, $options);
    }
}

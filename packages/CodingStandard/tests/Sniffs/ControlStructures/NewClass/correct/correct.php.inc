<?php
$someClass = new SomeNamespace\SomeClass;

$someClass = new SomeNamespace\SomeClass('ONE');

$someClass = new Exception('Class' . Some::getClassName() . ' not found');

$robot->setCacheStorage(new Nette\Caching\Storages\MemoryStorage);

$configurator = new Nette\Configurator;

$basket->setDeletedAt(new \DateTime);
$this->em->getDao(Basket::getClassName())->save($basket);

new Nette\Security\Identity($user->getId(), $user->getRole()->getName());

new SomeClass(new DummyEntity, $form->getEntity());

(new OperatorSpacingSniffTest)->run();

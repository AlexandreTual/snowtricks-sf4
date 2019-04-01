<?php

namespace App\tests\form;

use App\Entity\Category;
use App\Form\CategoryType;
use Symfony\Component\Form\Test\TypeTestCase;

class CategoryTypeTest extends TypeTestCase
{
    public function testForm(){
        $formData = [
            'name' => 'rotation',
            'description' => 'Zombie ipsum reversus ab viral inferno, nam rick grimes malum cerebro.'
        ];

        $objectToCompare = new Category();

        $form = $this->factory->create(CategoryType::class, $objectToCompare);

        $object = new Category();
        $object->setName('rotation')
        ->setDescription('Zombie ipsum reversus ab viral inferno, nam rick grimes malum cerebro.');

        $form->submit($formData);

        $this->assertTrue($form->isValid());

        $this->assertEquals($object, $objectToCompare);

        $this->assertInstanceOf(Category::class, $form->getData());

        $view = $form->createView();
        $children = $view->children;

        foreach (array_keys($formData) as $key) {
            $this->assertArrayHasKey($key, $children);
        }
    }
}

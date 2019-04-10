<?php

namespace App\tests\form;

use App\Entity\Comment;
use App\Form\CommentType;
use Symfony\Component\Form\Test\TypeTestCase;

class CommentTypeTest extends TypeTestCase
{
    public function testForm()
    {
        $formData = [
            'content' => 'Zombie ipsum reversus ab viral inferno, nam rick grimes malum cerebro.',
        ];

        $objectToCompare = new Comment();
        $form = $this->factory->create(CommentType::class, $objectToCompare);

        $object = new Comment();
        $object->setContent('Zombie ipsum reversus ab viral inferno, nam rick grimes malum cerebro.');

        $form->submit($formData);

        $this->assertTrue($form->isValid());

        $this->assertEquals($object, $objectToCompare);

        $this->assertInstanceOf(Comment::class, $form->getData());

        $view = $form->createView();
        $children = $view->children;

        foreach (array_keys($formData) as $key) {
            $this->assertArrayHasKey($key, $children);
        }
    }
}

<?php

namespace App\tests\form;

use App\Entity\User;
use App\Form\EmailValidationType;
use Symfony\Component\Form\Test\TypeTestCase;

class EmailValidationTypeTest extends TypeTestCase
{
    /**
     * @throws \Exception
     */
    public function testForm()
    {
        $formData = [
            'email' => 'tual.alexandre@gmail.com'
        ];

        $objectToCompare = new User();

        $form = $this->factory->create(EmailValidationType::class, $objectToCompare);

        $object = new User();
        $object->setEMail('tual.alexandre@gmail.com');

        $form->submit($formData);

        $this->assertTrue($form->isValid());

        $this->assertEquals($object->getEmail(), $objectToCompare->getEmail());

        $this->assertInstanceOf(User::class, $form->getData());

        $view = $form->createView();
        $children = $view->children;

        foreach (array_keys($formData) as $key) {
            $this->assertArrayHasKey($key, $children);
        }
    }
}

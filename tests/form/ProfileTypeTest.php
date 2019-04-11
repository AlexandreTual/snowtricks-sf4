<?php

namespace App\tests\form;

use App\Entity\User;
use App\Form\ProfileType;
use Symfony\Component\Form\Test\TypeTestCase;

class ProfileTypeTest extends TypeTestCase
{
    /**
     * @throws \Exception
     */
    public function testForm()
    {
        $dataForm = [
            'firstName' => 'Tual',
            'lastName' => 'Alexandre',
            'email' => 'tual.alexandre@gmail.com',
            'introduction' => 'Zombie ipsum reversus ab viral inferno, nam rick grimes malum cerebro.',
            'description' => 'Zombie ipsum reversus ab viral inferno, nam rick grimes malum cerebro. De carne lumbering animata corpora quaeritis. Summus brains sit​​, morbo vel maleficia? De apocalypsi gorger omero undead survivor dictum mauris. Hi mindless mortuis soulless creaturas, imo evil stalking monstra adventus resi dentevil vultus comedat cerebella viventium. Qui animated corpse, cricket bat max brucks terribilem incessu zomby. The voodoo sacerdos flesh eater, suscitat mortuos comedere carnem virus. Zonbi tattered for solum oculi eorum defunctis go lum cerebro. Nescio brains an Undead zombies. Sicut malus putrid voodoo horror. Nigh tofth eliv ingdead.',
        ];

        $objectToCompare = new User();

        $form = $this->factory->create(ProfileType::class, $objectToCompare);

        $object = new User();
        $object
            ->setFirstName('Tual')
            ->setLastName('Alexandre')
            ->setEmail('tual.alexandre@gmail.com')
            ->setIntroduction('Zombie ipsum reversus ab viral inferno, nam rick grimes malum cerebro.')
            ->setDescription('Zombie ipsum reversus ab viral inferno, nam rick grimes malum cerebro. De carne lumbering animata corpora quaeritis. Summus brains sit​​, morbo vel maleficia? De apocalypsi gorger omero undead survivor dictum mauris. Hi mindless mortuis soulless creaturas, imo evil stalking monstra adventus resi dentevil vultus comedat cerebella viventium. Qui animated corpse, cricket bat max brucks terribilem incessu zomby. The voodoo sacerdos flesh eater, suscitat mortuos comedere carnem virus. Zonbi tattered for solum oculi eorum defunctis go lum cerebro. Nescio brains an Undead zombies. Sicut malus putrid voodoo horror. Nigh tofth eliv ingdead.');

        $form->submit($dataForm);

        $this->assertTrue($form->isValid());

        $this->assertEquals($object->getFirstName(), $objectToCompare->getFirstName());
        $this->assertEquals($object->getLastName(), $objectToCompare->getLastName());
        $this->assertEquals($object->getIntroduction(), $objectToCompare->getIntroduction());
        $this->assertEquals($object->getDescription(), $objectToCompare->getDescription());

        $this->assertInstanceOf(User::class, $form->getData());

        $view = $form->createView();
        $children = $view->children;

        foreach (array_keys($dataForm) as $key) {
            $this->assertArrayHasKey($key, $children);
        }
    }
}

<?php

    namespace App\Form;

    use App\Entity\Famosos;
    use Symfony\Component\Form\AbstractType;
    use Symfony\Component\Form\FormBuilderInterface;
    use Symfony\Component\OptionsResolver\OptionsResolver;
    use Symfony\Component\Form\Extension\Core\Type\TextType;


    class FamososType extends AbstractType
    {
        public function buildForm(FormBuilderInterface $builder, array $options): void
        {
            $builder
                ->add('nombre', TextType::class)
                ->add('apellido', TextType::class)
                ->add('profesion', TextType::class)
;
        }

        public function configureOptions(OptionsResolver $resolver): void
        {
            $resolver->setDefaults([
                'data_class' => Famosos::class,
            ]);
        }
    }

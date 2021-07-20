<?php

namespace App\Controller\Admin;

use App\Entity\Stock;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Field\PercentField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\BooleanFilter;

class StockCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Stock::class;
    }

    public function createEntity(string $entityFqcn)
    {
        return new $entityFqcn('', '', '', '');
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add(BooleanFilter::new ('isTracked'))
        ;
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new ('id')->setFormTypeOption('disabled', true);
        yield TextField::new ('name')->setFormTypeOption('disabled', true);
        yield TextField::new ('ticker')->setFormTypeOption('disabled', true);
        yield BooleanField::new ('isTracked');
        yield NumberField::new ('sixMonthsMaximum', 'Maximum')->setFormTypeOption('disabled', true);
        yield NumberField::new ('sixMonthsMinimum', 'Minimum')->setFormTypeOption('disabled', true);
        yield NumberField::new ('weekOpen')->setFormTypeOption('disabled', true);
        yield NumberField::new ('current')->setFormTypeOption('disabled', true);
        yield PercentField::new ('sixMonthsMaximumPercent', 'MaximumPercent')->setFormTypeOption('disabled', true);
        yield PercentField::new ('weekOpenPercent')->setFormTypeOption('disabled', true);
        yield TextareaField::new ('usefulLinks')->renderAsHtml();

        $updatedAt = DateTimeField::new ('updatedAt')->setFormTypeOptions([
            'html5' => true,
            'years' => range(date('Y'), date('Y') + 5),
            'widget' => 'single_text',
            'disabled' => true
        ]);

        if (Crud::PAGE_EDIT === $pageName) {
            yield $updatedAt->setFormTypeOption('disabled', true);
        } else {
            yield $updatedAt;
        }
    }
}

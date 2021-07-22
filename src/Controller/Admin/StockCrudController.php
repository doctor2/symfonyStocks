<?php

namespace App\Controller\Admin;

use App\Entity\Stock;
use Doctrine\ORM\QueryBuilder;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FieldCollection;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FilterCollection;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\SearchDto;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Field\PercentField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\BooleanFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\NumericFilter;

class StockCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Stock::class;
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->disable(Action::NEW, Action::DELETE)
        ;
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add(BooleanFilter::new ('isTracked'))
            ->add(NumericFilter::new ('sixMonthsMinimumPercent'))
            ->add(NumericFilter::new ('sixMonthsMaximumPercent'))
            ->add(NumericFilter::new ('weekOpenPercent'))
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
        yield PercentField::new ('sixMonthsMinimumPercent', 'MinimumPercent')->setFormTypeOption('disabled', true);
        yield PercentField::new ('sixMonthsMaximumPercent', 'MaximumPercent')->setFormTypeOption('disabled', true);
        yield PercentField::new ('weekOpenPercent')->setFormTypeOption('disabled', true);
        yield TextareaField::new ('usefulLinks')->renderAsHtml();
        yield TextareaField::new ('comment');
    }

    public function createIndexQueryBuilder(SearchDto $searchDto, EntityDto $entityDto, FieldCollection $fields, FilterCollection $filters): QueryBuilder
    {
        return parent::createIndexQueryBuilder($searchDto, $entityDto, $fields, $filters)->andWhere('entity.current IS NOT NULL');
    }
}

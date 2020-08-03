<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Entity\Song;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class SongCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Song::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return parent::configureCrud($crud)
            ->setSearchFields(['title', 'album.title'])
            ->setEntityLabelInPlural('Songs')
            ->setEntityLabelInSingular('Song');
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id')->hideOnForm();
        yield TextField::new('displayTitle')
            ->addCssClass('font-weight-bold')
            ->setSortable(false)
            ->onlyOnIndex();

        yield FormField::addPanel('Song details');
        yield TextField::new('title')->hideOnIndex();
        yield IntegerField::new('track_number')->hideOnIndex();

        yield TextField::new('album.title', 'Album');
    }
}

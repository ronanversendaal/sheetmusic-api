<?php

namespace App\Controller\Admin;

use App\Entity\Album;
use App\Entity\Artist;
use App\Entity\Song;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractDashboardController
{
    /**
     * @Route("/admin", name="admin")
     */
    public function index(): Response
    {
        return parent::index();
    }

    public function configureDashboard() : Dashboard
    {
        return Dashboard::new()
            ->setTitle('Admin SheetMusic API')
            ->setFaviconPath('favicon.svg');
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linktoDashboard('Dashboard', 'fa fa-home');

        yield MenuItem::section('Music');
        yield MenuItem::linkToCrud('Albums', 'fa fa-record-vinyl', Album::class)
            ->setPermission('ROLE_ADMIN');
        yield MenuItem::linkToCrud('Artists', 'fa fa-users', Artist::class)->setPermission('ROLE_ADMIN');
        yield MenuItem::linkToCrud('Songs', 'fa fa-music', Song::class);

        yield MenuItem::section('Favorite actions', 'fa fa-star');
        yield MenuItem::linkToCrud('Create new song', 'fa fa-plus', Song::class)->setAction('new');

        yield MenuItem::section('Application routes', 'fa fa-smile');
        yield MenuItem::linkToUrl('App frontend', 'fa fa-star', $this->getParameter('app.url_fe'));
        yield MenuItem::linkToRoute('API Platform reference', 'fa fa-book', 'api_doc');
    }
}

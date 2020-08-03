<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Entity\Album;
use App\Entity\Artist;
use App\Entity\Song;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Config\UserMenu;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;

class DashboardController extends AbstractDashboardController
{
    /**
     * @Route("/admin", name="admin")
     */
    public function index(): Response
    {
        return parent::index();
    }

    public function configureCrud(): Crud
    {
        return parent::configureCrud()
            ->setPageTitle('index', '%entity_label_plural% listing');
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Admin SheetMusic API')
            ->setFaviconPath('favicon.svg');
    }

    public function configureUserMenu(UserInterface $user): UserMenu
    {
        return parent::configureUserMenu($user)
            ->displayUserName(false)
            ->setGravatarEmail($user->getEmail())
            ->addMenuItems([
//                Todo create these routes.
//                MenuItem::linktoRoute('Profile', 'fa fa-user-cog', 'app.settings'),
//                MenuItem::linktoRoute('Settings', 'fa fa-user-cog', 'app.settings'),
            ]);
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linktoDashboard('Dashboard', 'fa fa-home');

        yield MenuItem::subMenu('Music', 'fa fa-music')->setSubItems([
            MenuItem::linkToCrud('Albums', 'fa fa-record-vinyl', Album::class),
            MenuItem::linkToCrud('Artists', 'fa fa-users', Artist::class)->setPermission('ROLE_ADMIN'),
            MenuItem::linkToCrud('Songs', 'fa fa-music', Song::class),
        ])->setPermission('ROLE_ADMIN');

        yield MenuItem::subMenu('Favorite actions', 'fa fa-star')->setSubItems([
            MenuItem::linkToCrud('Create new song', 'fa fa-plus', Song::class)->setAction('new'),
        ])->setPermission('ROLE_ADMIN');

        yield MenuItem::section('Application routes', 'fa fa-smile');
        yield MenuItem::linkToUrl('App frontend', 'fa fa-star', $this->getParameter('app.url_fe'));
        yield MenuItem::linkToRoute('API Platform reference', 'fa fa-book', 'api_doc');

        yield MenuItem::section();

        if ($this->isGranted('ROLE_ADMIN')) {
            // Admin Things here
            if (!$this->isGranted('IS_IMPERSONATOR')) {
                yield MenuItem::linkToExitImpersonation('Stop impersonation', 'fa fa-user-secret');
            }
        }

        yield MenuItem::linkToLogout('Logout', 'fa fa-sign-out');
    }
}

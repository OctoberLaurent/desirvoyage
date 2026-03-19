<?php

namespace App\Controller\Admin;

use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;


use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminDashboard;

#[AdminDashboard(routePath: '/admin', routeName: 'admin_dashboard')]
class DashboardController extends AbstractDashboardController
{
    public function index(): Response
    {
        $adminUrlGenerator = $this->container->get(\EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator::class);
        return $this->redirect($adminUrlGenerator->setDashboard(self::class)->setController(TravelCrudController::class)->generateUrl());
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Désirvoyage - Administration');
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToRoute('Retour au site', 'fa fa-arrow-left', 'index');
        yield MenuItem::linkToDashboard('Tableau de bord', 'fa fa-home');

        yield MenuItem::linkTo(TravelCrudController::class, 'Travels', 'fa fa-plane');
        yield MenuItem::linkTo(CategoriesCrudController::class, 'Categories', 'fa fa-folder-open');
        yield MenuItem::linkTo(FormalityCrudController::class, 'formality', 'fa fa-globe');
        yield MenuItem::linkTo(StaysCrudController::class, 'Stays', 'fa fa-calendar-alt');
        yield MenuItem::linkTo(OptionsCrudController::class, 'Options', 'fa fa-sun');
        yield MenuItem::linkTo(UserCrudController::class, 'Users', 'fa fa-user');
        yield MenuItem::linkTo(ReservationCrudController::class, 'Reservation', 'fa fa-list');
        yield MenuItem::linkTo(ContactCrudController::class, 'Contact', 'fa fa-envelope');
    }
}

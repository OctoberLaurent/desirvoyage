<?php

namespace App\Controller\Admin;

use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminDashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Response;

#[AdminDashboard(routePath: '/admin', routeName: 'admin_dashboard')]
final class DashboardController extends AbstractDashboardController
{
    #[\Override]
    public function index(): Response
    {
        $adminUrlGenerator = $this->container->get(\EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator::class);

        return $this->redirect($adminUrlGenerator->setDashboard(self::class)->setController(TravelCrudController::class)->generateUrl());
    }

    #[\Override]
    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Désirvoyage - Administration');
    }

    #[\Override]
    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToRoute('Retour au site', 'fa fa-arrow-left', 'index');
        yield MenuItem::linkToDashboard('Tableau de bord', 'fa fa-home');

        yield MenuItem::linkTo(TravelCrudController::class, 'Travels', 'fa fa-plane');
        yield MenuItem::linkTo(CategoryCrudController::class, 'Category', 'fa fa-folder-open');
        yield MenuItem::linkTo(FormalityCrudController::class, 'formality', 'fa fa-globe');
        yield MenuItem::linkTo(StayCrudController::class, 'Stay', 'fa fa-calendar-alt');
        yield MenuItem::linkTo(OptionCrudController::class, 'Option', 'fa fa-sun');
        yield MenuItem::linkTo(UserCrudController::class, 'Users', 'fa fa-user');
        yield MenuItem::linkTo(ReservationCrudController::class, 'Reservation', 'fa fa-list');
        yield MenuItem::linkTo(ContactCrudController::class, 'Contact', 'fa fa-envelope');
    }
}

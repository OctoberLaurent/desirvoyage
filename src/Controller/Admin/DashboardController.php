<?php

namespace App\Controller\Admin;

use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractDashboardController
{
    #[Route('/admin', name: 'admin_dashboard')]
    #[Route(path: '/admin', name: 'admin_dashboard')]
    public function index(): Response
    {
        // En EasyAdmin 4, on peut initialement rediriger vers une page vierge ou vers un CRUD
        // Pour l'instant, on laisse l'index par défaut.
        return parent::index();
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

        // On ajoutera les CRUDs de Travels, Categories, Users, etc. ici.
    }
}

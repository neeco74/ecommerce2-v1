<?php

namespace App\Controller\Admin;

use App\Entity\Users;
use App\Entity\Headers;
use App\Entity\Produits;
use App\Entity\Commandes;
use App\Entity\Categories;
use App\Entity\Transporteur;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Controller\Admin\CommandesCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Router\CrudUrlGenerator;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;

class DashboardController extends AbstractDashboardController
{
    /**
     * @Route("/admin", name="admin")
     */
    public function index(): Response
    {
        // redirect to some CRUD controller
        $routeBuilder = $this->get(CrudUrlGenerator::class)->build();

        return $this->redirect($routeBuilder->setController(CommandesCrudController::class)->generateUrl());
    }

    public function configureDashboard(): Dashboard
    {  
        return Dashboard::new()
            ->setTitle('La boutique FR');
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linktoDashboard('Dashboard', 'fa fa-home');
        yield MenuItem::linkToCrud('Utilisateur', 'fas fa-user', Users::class);
        yield MenuItem::linkToCrud('Categories', 'fas fa-list', Categories::class);
        yield MenuItem::linkToCrud('Produits', 'fas fa-tag', Produits::class);
        yield MenuItem::linkToCrud('Transporteur', 'fas fa-truck', Transporteur::class);
        yield MenuItem::linkToCrud('Commandes', 'fas fa-shopping-cart', Commandes::class);
        yield MenuItem::linkToCrud('Headers', 'fas fa-desktop', Headers::class);
    }
}

<?php

namespace App\Controller\Admin;

use App\Entity\Commandes;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\MoneyField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Router\CrudUrlGenerator;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class CommandesCrudController extends AbstractCrudController
{
    private $em;
    private $crudUrlGenerator;

    public function __construct(EntityManagerInterface $em, CrudUrlGenerator $crudUrlGenerator)
    {
        $this->em = $em;
        $this->crudUrlGenerator = $crudUrlGenerator;

    }


    public static function getEntityFqcn(): string
    {
        return Commandes::class;
    }

    public function configureActions(Actions $actions): Actions
    {

        $updatePreparation = Action::new('updatePreparation', 'Préparation en cours', 'fas fa-box-open')->linkToCrudAction('updatePreparation');
        $updateDelivery = Action::new('updateDelivery', 'Livraison en cours', 'fas fa-truck')->linkToCrudAction('updateDelivery');

        return $actions
            ->add('detail', $updatePreparation)
            ->add('detail', $updateDelivery)
            ->add('index', 'detail');
    }

    public function updatePreparation(AdminContext $context)
    {
        $commande = $context->getEntity()->getInstance();
        $commande->setState(2);
        $this->em->flush();

        $this->addFlash('notice', "<span style='color:green;'><strong>La commande ".$commande->getReference()." est bien <u>en cours de préparation</u>.</strong></span>");

        $url = $this->crudUrlGenerator->build()
            ->setController(CommandesCrudController::class)
            ->setAction('index')
            ->generateUrl();

        //$mail = new MailJet();


        return $this->redirect($url);
    }


    public function updateDelivery(AdminContext $context)
    {
        $commande = $context->getEntity()->getInstance();
        $commande->setState(3);
        $this->em->flush();

        $this->addFlash('notice', "<span style='color:orange;'><strong>La commande ".$commande->getReference()." est bien <u>en cours de livraison</u>.</strong></span>");

        $url = $this->crudUrlGenerator->build()
            ->setController(CommandesCrudController::class)
            ->setAction('index')
            ->generateUrl();

        return $this->redirect($url);
    }


    public function configureCrud(Crud $crud): Crud
    {
        return $crud->setDefaultSort(['id' => 'DESC']);
    }
    
    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id'),
            DateTimeField::new('createdAt', 'Passée le'),
            TextField::new('user.fullname', 'Utilisateur'),
            TextEditorField::new('delivery', 'Adresse de livraison')->onlyOnDetail(),
            MoneyField::new('total', 'Total produit')->setCurrency('EUR'),
            TextField::new('transporteurName', 'Transporteur'),
            MoneyField::new('transporteurPrice', 'Frais de port')->setCurrency('EUR'),
            //BooleanField::new('isPaid', 'Payée'),
            ChoiceField::new('state', 'Statut')->setChoices([
                'Non payée' => 0,
                'Payée' => 1,
                'Préparation en cours' => 2,
                'Livraison en cours' => 3
            ]),
            ArrayField::new('commandeDetails', 'Produits achetés')->hideOnIndex()
        ];
    }
    
}

<?php


namespace App\Action;

use App\Entity\Product;
use App\Form\Product\Create as ProductCreateForm;
use Doctrine\ORM\EntityManager;
use Interop\Http\ServerMiddleware\DelegateInterface;
use Interop\Http\ServerMiddleware\MiddlewareInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\Response\HtmlResponse;
use Zend\Expressive\Template\TemplateRendererInterface;
use Zend\Hydrator\ClassMethods;


class ProductListAction implements MiddlewareInterface
{

    /**
     * @var TemplateRendererInterface
     */
    private $template;

    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * ProductListAction constructor.
     * @param TemplateRendererInterface $template
     * @param EntityManager             $entityManager
     */
    public function __construct(TemplateRendererInterface $template, EntityManager $entityManager)
    {
        $this->template      = $template;
        $this->entityManager = $entityManager;
    }

    /**
     * Process an incoming server request and return a response, optionally delegating
     * to the next middleware component to create the response.
     *
     * @param ServerRequestInterface $request
     * @param DelegateInterface      $delegate
     *
     * @return ResponseInterface
     */
    public function process(ServerRequestInterface $request, DelegateInterface $delegate)
    {
        /*
        $product = new Product();
        $product->name = 'Celular';
        $product->price = 999.99;
        $product->description = 'Um celular de qualquer marca';
        $this->entityManager->persist($product);
        $this->entityManager->flush();
        */

        $form = new ProductCreateForm();
        $form->setHydrator(new ClassMethods());
        $form->bind(new Product);

        $repository = $this->entityManager->getRepository(Product::class);
        $products = $repository->findAll();
        return new HtmlResponse($this->template->render('app::products/list', ['form' => $form, 'products' => $products]));
    }
}

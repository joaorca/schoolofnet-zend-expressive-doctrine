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
use Zend\Diactoros\Response\RedirectResponse;
use Zend\Expressive\Router\RouterInterface;
use Zend\Expressive\Template\TemplateRendererInterface;
use Zend\Hydrator\ClassMethods;


class ProductCreateAction implements MiddlewareInterface
{

    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * @var TemplateRendererInterface
     */
    private $template;

    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * ProductCreateAction constructor.
     * @param RouterInterface           $router
     * @param TemplateRendererInterface $template
     * @param EntityManager             $entityManager
     */
    public function __construct(
        RouterInterface $router,
        TemplateRendererInterface $template,
        EntityManager $entityManager
    ) {
        $this->router        = $router;
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
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function process(ServerRequestInterface $request, DelegateInterface $delegate)
    {
        $form = new ProductCreateForm();
        $form->setHydrator(new ClassMethods());
        $form->bind(new Product);

        $data = $request->getParsedBody();
        $form->setData($data);

        if ($form->isValid()) {
            $entity = $form->getData();
            $this->entityManager->persist($entity);
            $this->entityManager->flush();
        }

        $uri = $this->router->generateUri('products');
        return new RedirectResponse($uri);
    }
}

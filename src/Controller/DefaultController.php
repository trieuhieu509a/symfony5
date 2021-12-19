<?php

namespace App\Controller;

use App\Entity\User;
use App\Services\GiftsService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class DefaultController extends AbstractController
{

//    public function __construct(GiftsService $gifts)
//    {
//        $gifts->gifts = ['a', 'b', 'c', 'd'];
//    }

    public function __construct($logger)
    {
        // use $logger service
    }
//
//    public function index(): Response
//    {
//        $entityManager = $this->getDoctrine()->getManager();
//        $user = new User;
//        $user->setName('Adam');
//        $user2 = new User;
//        $user2->setName('Robert');
//        $user3 = new User;
//        $user3->setName('John');
//        $user4 = new User;
//        $user4->setName('Susan');
//        $entityManager->persist($user);
//        $entityManager->persist($user2);
//        $entityManager->persist($user3);
//        $entityManager->persist($user4);
//        exit($entityManager->flush());
//        return $this->render('default/index.html.twig', [
//            'controller_name' => 'DefaultController',
//        ]);
//    }

    /**
     * @Route("/default", name="default")
     */
    public function index(GiftsService $gifts): Response
    {
        $users = $this->getDoctrine()->getRepository(User::class)->findAll();

//        if ($users) { // Use Exception
//            throw $this->createNotFoundException('The users do not exist');
//        }

//        $this->addFlash(
//            'notice',
//            'your changes was updated'
//        );
//
//        $this->addFlash(
//            'warning',
//            'your changes was updated'
//        );

        return $this->render('default/index.html.twig', [
            'controller_name' => 'DefaultController',
            'users' => $users,
            'random_gift' => $gifts->gifts,
        ]);
    }

    /**
     * @Route("/blog/{page?}", name="blog_list", requirements={"page"="\d+"})
     */
    public function index2(): Response
    {
        return new Response('Optional paramaters in url and ...');
    }

    /**
     * @Route(
     *     "/articles/{_locale}/{year}/{slug}/{category}",
     *     defaults={"category": "computers"},
     *     requirements={
     *          "_locale"="en|fr",
     *          "category"="computers|rtv",
     *          "year"="\d+"
     *  }
     * )
     */
    public function index3($_locale, $year, $slug, $category): Response
    {
        return new Response('Optional paramaters in url and ...' . "$_locale, $year, $slug, $category");
    }

    /**
     * @Route({
     *      "nl": "/over-ons",
     *      "en": "/about-us"
     *}, name="about_us")
     */
    public function index4(): Response
    {
        return new Response('Optional paramaters in url and ...');
    }


    /**
     * @Route("/cookie", name="cookie")
     */
    public function cookie(Request $request, SessionInterface $session): Response
    {
//        exit($request->query->get('data', 'default'));
//        exit($request->server->get('HTTP_POST', 'default'));
        $request->isXmlHttpRequest(); // is Ajax
        $request->request->get('page');
        $request->files->get('foo');


//        exit($request->cookies->get('PHPSESSID')); // get cookie


        $cookie = new Cookie(
            'my_cookie', // name
            'cookie_value', // value
            time() + (2 * 365 * 24 * 60 * 60) // Expires after 2 years
        );

        $res = new Response();
//        $res->headers->setCookie($cookie); // send cookie to browser
        $res->headers->clearCookie('my_cookie'); // remove cookie
        $res->send();

        $session->set('name', 'session value');
//        $session->remove('name');
//        $session->clear();
        if ($session->has('name')) {
            exit($session->get('name'));
        }


        return $res;
    }


    /**
     * @Route("/generate-url/{param?}", name="generate_url")
     */
    public function generate_url(): Response
    {
        exit($this->generateUrl(
            'generate_url',
            ['param' => 10],
            UrlGeneratorInterface::ABSOLUTE_URL
        ));
    }


    /**
     * @Route("/download")
     */
    public function download(): Response
    {
        $path = $this->getParameter('download_directory');
        return $this->file($path . 'image.jpg');
    }

    /**
     * @Route("/redirect-test")
     */
    public function redirectTest(): Response
    {
        return $this->redirectToRoute('generate_url', ['param' => 10]);
    }

    /**
     * @Route("/forwarding-to-controller")
     */
    public function forwardingToController(): Response
    {
        $response = $this->forward(
            'App\Controller\DefaultController::methodToForwardTo',
            ['param' => 1]
        );
        return $response;
    }

    /**
     * @Route("/url-to-forward-to/{param?}", name="route_to_forward_to")
     */
    public function methodToForwardTo($param): Response
    {
        exit('Test controller forwarding :' . $param);
    }


    /**
     * @Route("/home", name="default", name="home")
     */
    public function page(GiftsService $gifts): Response
    {
        $users = $this->getDoctrine()->getRepository(User::class)->findAll();

        return $this->render('default/index.html.twig', [
            'controller_name' => 'DefaultController',
        ]);
    }

    public function mostPopularPosts($number = 3): Response
    {
        // database call:
        $posts = ['post 1', 'post 2', 'post 3', 'post 4'];
        return $this->render('default/most_popular_posts.html.twig', ['posts' => $posts]);
    }

    /**
     * @Route("/create", name="default", name="home")
     */
    public function create(GiftsService $gifts): Response
    {
        $entinyManager = $this->getDoctrine()->getManager();

        $user = new User();
        $user->setName('Robert');

        $entinyManager->persist($user);

        $entinyManager->flush();

        dump('A new user was saved with the id of' . $user->getId());die;

        return $this->render('default/index.html.twig', [
            'controller_name' => 'DefaultController',
        ]);
    }
}

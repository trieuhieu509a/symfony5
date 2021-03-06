<?php

namespace App\Controller;

use App\Entity\Address;
use App\Entity\Author;
use App\Entity\File;
use App\Entity\Pdf;
use App\Entity\SecurityUser;
use App\Entity\User;
use App\Entity\Video;
use App\Events\VideoCreatedEvent;
use App\Form\RegisterUserType;
use App\Form\VideoFormType;
use App\Services\GiftsService;
use App\Services\MyService;
use App\Services\ServiceInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\Cache\Adapter\TagAwareAdapter;
use Symfony\Component\Cache\CacheItem;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Contracts\Translation\TranslatorInterface;

class DefaultController extends AbstractController
{

//    public function __construct(GiftsService $gifts)
//    {
//        $gifts->gifts = ['a', 'b', 'c', 'd'];
//    }

    public function __construct(EventDispatcherInterface $dispatcher, $logger)
    {
        // use $logger service
        $this->dispatcher = $dispatcher;
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

        dump(123123123123); // show in debug tool
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

//    /**
//     * @Route({
//     *      "nl": "/over-ons",
//     *      "en": "/about-us"
//     *}, name="about_us")
//     */
//    public function index4(): Response
//    {
//        return new Response('Optional paramaters in url and ...');
//    }


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
        dump($users);
        return $this->render('default/index.html.twig', [
            'controller_name' => 'DefaultController',
            'users' => [],
            'random_gift' => [],
            'count'=> 0
        ]);
    }

    public function mostPopularPosts($number = 3): Response
    {
        // database call:
        $posts = ['post 1', 'post 2', 'post 3', 'post 4'];
        return $this->render('default/most_popular_posts.html.twig', ['posts' => $posts]);
    }

    /**
     * @Route("/create", name="default", name="create")
     */
    public function create(GiftsService $gifts): Response
    {
        $entinyManager = $this->getDoctrine()->getManager();

        $user = new User();
        $user->setName('Robert');

        $entinyManager->persist($user);

        $entinyManager->flush();

        dump('A new user was saved with the id of' . $user->getId());
        die;

        return $this->render('default/index.html.twig', [
            'controller_name' => 'DefaultController',
        ]);
    }


    /**
     * @Route("/user/{id}", name="user")
     */
    public function user(int $id): Response
    {
        $repository = $this->getDoctrine()->getRepository(User::class);

//        $user = $repository->find($id);
//        $user = $repository->findOneBy(['name' => 'Robert']);
//        $user = $repository->findOneBy(['name' => 'Robert 0', 'id' => 1]);
        $users = $repository->findBy(['name' => 'Robert'], ['id' => 'DESC']);

        dump($users);
        die;

        return $this->render('default/index.html.twig', [
            'controller_name' => 'DefaultController',
        ]);
    }

    /**
     * @Route("/update-user/{id}", name="user-update")
     */
    public function updateUser(int $id): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $id = 1;
        $user = $entityManager->getRepository(User::class)->find($id);


        if (!$user) {
            throw $this->createNotFoundException('Not fount user id ' . $id);
        }
        $user->setName('New user name!');

        $entityManager->flush();

        dump($user);
        die;

        return $this->render('default/index.html.twig', [
            'controller_name' => 'DefaultController',
        ]);
    }

    /**
     * @Route("/delete-user/{id}", name="user-delete")
     */
    public function deleteUser(int $id): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $id = 2;
        $user = $entityManager->getRepository(User::class)->find($id);
        $entityManager->remove($user);
        $entityManager->flush();

        dump($user);
        die;

        return $this->render('default/index.html.twig', [
            'controller_name' => 'DefaultController',
        ]);
    }

    /**
     * @Route("/raw-query", name="raw-query")
     */
    public function rawQeury(): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $conn = $entityManager->getConnection();
        $sql = '
            SELECT * from user u 
            WHERE u.id > :id
        ';

        $stmt = $conn->prepare($sql);
        $stmt->execute(['id' => 15]);

        dump($stmt->fetchAll());
        die;

        return $this->render('default/index.html.twig', [
            'controller_name' => 'DefaultController',
        ]);
    }

    /**
     * @Route("/param-converter/{id}", name="param-converter")
     */
    public function paramConverter(User $user): Response
    {
        // find user by id in route
        dump($user);
        die;
    }

    /**
     * @Route("/circle-callback", name="circle-callback")
     */
    public function circleCallback(): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $user = new User();
        $user->setName('Ronadinho');
        $entityManager->persist($user);
        $entityManager->flush();
        // find user by id in route
        dump($user);
        die;
    }

    /**
     * @Route("/one-to-many", name="one-to-many")
     */
    public function oneToMany(): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
//        $user = new User();
//        $user->setName('test 2');
//
//        for ($i=1; $i<=3; $i++) {
//            $video = new Video();
//            $video->setTitle('Video title - ' . $i);
//            $user->addVideo($video);
//            $entityManager->persist($video);
//        }
//
//        $entityManager->persist($user);
//        $entityManager->flush();
//        // find user by id in route
//        dump($video->getId());
//        dump($user->getId());


//        $video = $this->getDoctrine()->getRepository(Video::class)->find(1);
//        dump($video->getUser());
//        dump($video->getUser()->getName());


        /**
         * @var User $user
         */
        $user = $this->getDoctrine()->getRepository(User::class)->find(1);

        /**
         * @var Video $video
         */
        foreach ($user->getVideos() as $video) {
            dump($video->getTitle());
        }
        die;
    }

    /**
     * @Route("/delete-related-object", name="delete-related-object")
     */
    public function deleteRelatedObject(): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $user = $this->getDoctrine()->getRepository(User::class)->find(1);
        $entityManager->remove($user);
        $entityManager->flush();
        dump($user);
        die;

        $video = $this->getDoctrine()->getRepository(Video::class)->find(1);
        $user->removeVideo($video);
        $entityManager->flush();

        foreach ($user->getVideos() as $video) {
            dump($video->getTitle());
        }
        die;
    }

    /**
     * @Route("/one-to-one", name="one-to-one")
     */
    public function oneToOneRelatedObject(): Response
    {
        $entityManager = $this->getDoctrine()->getManager();

        $address = new Address();
        $address->setStreet('street');
        $address->setNumber(23);

        $user = new User();
        $user->setName('One to One');
        $user->setAddress($address);

        // $entityManager->persist($address);  // required, if cascade:persist is not set on user entity
        $entityManager->persist($user);

        $entityManager->flush();

        dump($user->getAddress()->getStreet());
        die;
    }

    /**
     * @Route("/many-to-many", name="many-to-many")
     */
    public function manyToManyRelatedObject(): Response
    {
        $entityManager = $this->getDoctrine()->getManager();


//        // create 4 user
//        for ($i = 1; $i <= 4; $i++) {
//            $user = new User();
//            $user->setName('Many to many' . $i);
//            $entityManager->persist($user);
//        }
//
//        $entityManager->flush();
//
//        dump($user->getId());
//        die;

        /**
         * @var User $user1
         * @var User $user4
         */
        $user1 = $entityManager->getRepository(User::class)->find(1);
//        $user2 = $entityManager->getRepository(User::class)->find(2);
//        $user3 = $entityManager->getRepository(User::class)->find(3);
        $user4 = $entityManager->getRepository(User::class)->find(4);
//
//        $user1->addFollowed($user2);
//        $user1->addFollowed($user3);
//        $user1->addFollowed($user4);
//
//        $entityManager->flush();

        dump($user1->getFollowed()->count());
        dump($user1->getFollowing()->count());
        dump($user4->getFollowing()->count());
        die;

    }

    /**
     * @Route("/query-builder-and-eager-load", name="query-builder-and-eager-load")
     */
    public function queryBuilderAndEagerLoad(): Response
    {
        $entityManager = $this->getDoctrine()->getManager();


//        $user = new User();
//        $user->setName('Robert');
//        $entityManager->persist($user);
//        for ($i = 1; $i <= 3; $i++) {
//            $video = new Video();
//            $video->setTitle('Video title' . $i);
//            $user->addVideo($video);
//            $entityManager->persist($video);
//        }
//        $entityManager->persist($user);
//        $entityManager->flush();


//        $user = $entityManager->getRepository(User::class)->find(1); // lazy load
        $user = $entityManager->getRepository(User::class)->findWithVideos(1);
        dump($user);
        die;

    }

    /**
     * @Route("/polymorphic", name="polymorphic")
     */
    public function polymorphic(): Response
    {
        $entityMananger = $this->getDoctrine()->getManager();

//        $items = $entityMananger->getRepository(Pdf::class)->findAll();
//        $items2 = $entityMananger->getRepository(File::class)->findAll();
        /**
         * @var Author $items3
         */
        $items3 = $entityMananger->getRepository(Author::class)->find(1);

//        dump($items); // show in debug tool
//        dump($items2); // show in debug tool
//        dump($items3->getFiles()); // show in debug tool
//
//        /**
//         * @var File $file
//         */
//        foreach ($items3->getFiles() as $file) {
//            if ($file instanceof Pdf) {
//                dump($file->getDescription()); // show in debug tool
//            }
//        }


        $author = $entityMananger->getRepository(Author::class)->findByIdWithPdf(1);
        dump($author);
        foreach ($author->getFiles() as $file) {
            // if($file instanceof Pdf)
            dump($file->getFileName());
        }

        return $this->render('default/index.html.twig', [
            'controller_name' => 'DefaultController',
            'users' => [],
            'random_gift' => [],
        ]);
    }

    /**
     * @Route("/service-paramaters", name="service-paramaters")
     */
    public function serviceparamaters(Request $request, MyService $service, ContainerInterface $container): Response
    {
//        $service->someAction();
//        dump($service->secondService->someMethod());
        dump($container->get('app.myservice'));
        return $this->render('default/index.html.twig', [
            'controller_name' => 'DefaultController',
            'users' => [],
            'random_gift' => [],
        ]);
    }

    /**
     * @Route("/service-tags", name="service-tags")
     */
    public function servicetags(Request $request, MyService $service): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $user = $entityManager->getRepository(User::class)->find(1);
        $user->setName('Rob');
        $entityManager->persist($user);
        $entityManager->flush();

        return $this->render('default/index.html.twig', [
            'controller_name' => 'DefaultController',
            'users' => [],
            'random_gift' => [],
        ]);
    }

    /**
     * @Route("/service-interface", name="service-interface")
     */
    public function serviceInterface(Request $request, ServiceInterface $service): Response
    {
        return $this->render('default/index.html.twig', [
            'controller_name' => 'DefaultController',
            'users' => [],
            'random_gift' => [],
        ]);
    }

    /**
     * @Route("/cache", name="cache")
     */
    public function cache(): Response
    {
        $cache = new FilesystemAdapter();
        /**
         * @var CacheItem $posts
         */
        $posts = $cache->getItem('database.get_posts');
        if (!$posts->isHit()) {
            $posts_from_db = ['post 1', 'post 2', 'post 3'];
            dump('connection with database .... ');
            $posts->set(serialize($posts_from_db));
            $posts->expiresAfter(5);
            $cache->save($posts);
        }
//        $cache->clear();
        dump(unserialize($posts->get()));

        return $this->render('default/index.html.twig', [
            'controller_name' => 'DefaultController',
            'users' => [],
            'random_gift' => [],
        ]);
    }

    /**
     * @Route("/cache-tags", name="cache-tags")
     */
    public function cacheTags(): Response
    {
        $cache = new TagAwareAdapter(
            new FilesystemAdapter()
        );
        /**
         * @var CacheItem $posts
         */
        $acer = $cache->getItem('acer');
        $dell = $cache->getItem('dell');
        $ibm = $cache->getItem('ibm');
        $apple = $cache->getItem('apple');

        if (!$acer->isHit()){
            $acer_from_db = 'acer laptop';
            $acer->set($acer_from_db);
            $acer->tag(['computers', 'laptops', 'acer']); // acer belong to laptops cartegory, laptops is sub-category of computers
            $cache->save($acer);
            dump('acer laptop form database ...');
        }

        if (!$dell->isHit()){
            $dell_from_db = 'dell laptop';
            $dell->set($dell_from_db);
            $dell->tag(['computers', 'laptops', 'dell']);
            $cache->save($dell);
            dump('dell laptop form database ...');
        }

        if (!$ibm->isHit()){
            $ibm_from_db = 'ibm desktops';
            $ibm->set($ibm_from_db);
            $ibm->tag(['computers', 'desktops', 'ibm']); // desktops NOT laptops
            $cache->save($ibm);
            dump('ibm laptop form database ...');
        }

        if (!$apple->isHit()){
            $apple_from_db = 'apple desktops';
            $apple->set($apple_from_db);
            $apple->tag(['computers', 'desktops', 'apple']); // desktops NOT laptops
            $cache->save($apple);
            dump('apple laptop form database ...');
        }

//        $cache->invalidateTags(['ibm']); // invalidate tags
//        $cache->invalidateTags(['desktops']);
//        $cache->invalidateTags(['laptops']);
        $cache->invalidateTags(['computers']);

        dump($acer->get());
        dump($dell->get());
        dump($ibm->get());
        dump($apple->get());

        return $this->render('default/index.html.twig', [
            'controller_name' => 'DefaultController',
            'users' => [],
            'random_gift' => [],
        ]);
    }

    /**
     * @Route("/profiler", name="profiler")
     */
    public function profiler(Request $request): Response
    {
        dump($request, $this);
        return $this->render('default/index.html.twig', [
            'controller_name' => 'DefaultController',
            'users' => [],
            'random_gift' => [],
        ]);
    }

    /**
     * @Route("/create-dispatching-event", name="create-dispatching-event")
     */
    public function createEvent(Request $request): Response
    {
        $video = new \stdClass();
        $video->title = 'funny video';
        $video->category = 'funny';
        $event = new VideoCreatedEvent($video);

        $this->dispatcher->dispatch('video.created.event' ,$event);

        return $this->render('default/index.html.twig', [
            'controller_name' => 'DefaultController',
            'users' => [],
            'random_gift' => [],
        ]);
    }

    /**
     * @Route("/form", name="form")
     */
    public function form(Request $request): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
//        $videos = $entityManager->getRepository(Video::class)->findAll();
//        dump($videos);


        $video = new Video();
        $video->setSize(1);
        $video->setFilename('File name');
        $video->setDescription('description');

//        $video = $entityManager->getRepository(Video::class)->find(12);
//        dump($video);

//        $video->setTitle('Write a blog post');
//        $video->setCreatedAt(new \DateTime('tomorrow'));

        $form = $this->createForm(VideoFormType::class, $video);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
//            dump($form->getData());
            $file = $form->get('file')->getData();
            $fileName = sha1(random_bytes(14)) . '.' . $file->guessExtension();
            $file->move(
                $this->getParameter('videos_directory'),
                $fileName
            );
            $video->setFile($fileName);
            $entityManager->persist($video);
            $entityManager->flush();
            return $this->redirect('form');
        }

        return $this->render('default/form.html.twig', [
            'controller_name' => 'DefaultController',
            'users' => [],
            'random_gift' => [],
            'form' => $form->createView(),
            'video' => $video,
        ]);
    }

    /**
     * @Route("/mail", name="mail")
     */
    public function mail(Request $request, \Swift_Mailer $mailer): Response
    {
        $message = (new \Swift_Message('Hello Email'))
            ->setFrom('send@example.com')
            ->setTo('recipient@example.com')
            ->setBody(
                $this->renderView(
                    'emails/registration.html.twig',
                    array(
                        'name' => 'Robert'
                    )
                ),
                'text/html'
            )
        ;
        $mailer->send($message);
        return $this->render('default/index.html.twig', [
            'controller_name' => 'DefaultController',
            'users' => [],
            'random_gift' => []
        ]);
    }

    /**
     * @Route("/register", name="register")
     */
    public function register(Request $request, UserPasswordEncoderInterface $passwordEncoder): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $users = $entityManager->getRepository(SecurityUser::class)->findAll();
        dump($users);

        $user = new SecurityUser();
        $form = $this->createForm(RegisterUserType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $user->setPassword(
                $passwordEncoder->encodePassword($user, $form->get('password')->getData())
            );
            $user->setEmail($form->get('email')->getData());
            $entityManager->persist($user);
            $entityManager->flush();
            return $this->redirectToRoute('register');
        }
        return $this->render('default/register.html.twig', [
            'controller_name' => 'DefaultController',
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/login", name="login")
     */
    public function login(AuthenticationUtils $authenticationUtils)
    {
        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', array(
            'last_username' => $lastUsername,
            'error'         => $error,
        ));
    }

    /**
     * @Route("/auth-annotation", name="auth-annotation")
     */
    public function authAnnotation(UserPasswordEncoderInterface $userPasswordEncoder)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $users = $entityManager->getRepository(SecurityUser::class)->findAll();
        dump($users);

//        $user = new SecurityUser;
//        $user->setEmail('user@user.com');
//        $password = $userPasswordEncoder->encodePassword($user, 'pass');
//        $user->setPassword($password);
//        $entityManager->persist($user);
//        $entityManager->flush();

        $user = new SecurityUser;
        $user->setEmail('admin@admin.com');
        $password = $userPasswordEncoder->encodePassword($user, 'pass');
        $user->setPassword($password);
        $user->setRoles(['ROLE_ADMIN']);

        $video = new Video;
        $video->setTitle('video title');
        $video->setFile('video path');
        $video->setFilename('video path');
        $video->setDescription('desc');
        $video->setSize(1);
        $video->setCreatedAt(new \DateTime());
        $entityManager->persist($video);

        $entityManager->persist($user);
        $entityManager->flush();

        dump($user->getId());
        dump($video->getId());

        return $this->render('default/index.html.twig', array(
            'controller_name' => 'DefaultController',
        ));
    }

    // @Security("user.getId() == video.getSecurityUser().getId()")
    /**
     * @Route("/home/{id}/video", name="home video")
     *
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function authAnnotationDeleteVideo(Request $request, UserPasswordEncoderInterface $userPasswordEncoder,Video $video, UserInterface $user)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $users = $entityManager->getRepository(SecurityUser::class)->findAll();
//        dump($users);
//        dump($video);

        return $this->render('default/index.html.twig', array(
            'controller_name' => 'DefaultController',
        ));
    }

    /**
     * @Route("/admin", name="admin")
     */
    public function authorizationuUsingConfigFile(Request $request)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $users = $entityManager->getRepository(SecurityUser::class)->findAll();
        dump($users);

        return $this->render('default/index.html.twig', array(
            'controller_name' => 'DefaultController',
        ));
    }

    /**
     * @Route("/deny-access", name="deny access")
     */
    public function authorizationuUsingDenyAccessUnlessGranted(Request $request, UserInterface $user)
    {
//        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
//        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_REMEMBERED'); // check user use remember check box for auto login
        dump($user);

        return $this->render('default/index.html.twig', array(
            'controller_name' => 'DefaultController',
        ));
    }

    /**
     * @Route("/voters", name="voters")
     */
    public function voters(Request $request, UserInterface $user)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $video = $entityManager->getRepository(Video::class)->find(7);

        $this->denyAccessUnlessGranted('VIDEO_DELETE', $video);

        return $this->render('default/index.html.twig', array(
            'controller_name' => 'DefaultController',
        ));
    }

    /**
     * @Route("/translations", name="translations")
     */
    public function translations(Request $request, TranslatorInterface $translator)
    {
        $translated = $translator->trans('some.key');
        dump($translated);
        dump($request->getLocale());

        return $this->render('default/index.html.twig', [
            'controller_name' => 'DefaultController',
        ]);
    }

    /**
     * @Route({
     *      "en": "/trans-route",
     *      "pl": "/pl-trans-route",
     * }, name="trans-route-te")
     */
    public function transRoute(AuthenticationUtils $authenticationUtils)
    {
        // http://127.0.0.1:8000/pl/home
        // http://127.0.0.1:8000/home
        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', array(
            'last_username' => $lastUsername,
            'error'         => $error
        ));
    }
}

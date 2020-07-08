<?php

namespace App\Controller;

use App\Entity\BlogPost;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Serializer;

/**
 * @Route("/blog")
 * Class BlogController
 * @package App\Controller
 */
class BlogController extends AbstractController
{
    /**
     * @Route("/{page}", name="blog_list", methods={"GET"}, defaults={"page": 1}, requirements={"page"="\d+"})
     * @param int $page
     * @param Request $request
     * @return JsonResponse
     */
    public function list(int $page, Request $request)
    {
        $limit = $request->get('limit', 10);
        $repository = $this->getDoctrine()->getRepository(BlogPost::class);
        $blogPosts = $repository->findAll();

        return $this->json(
            [
                'page' => $page,
                'limit' => $limit,
                'data' => array_map(function (BlogPost $post) {
                    return $this->generateUrl('blog_by_slug', ['slug' => $post->getSlug()]);
                }, $blogPosts)
            ]
        );
    }

    /**
     * @Route("/post/{id}", name="blog_by_id", methods={"GET"}, requirements={"id"="\d+"})
     * @ParamConverter("post", class="App:BlogPost")
     * @param $post
     * @return JsonResponse
     */
    public function postById($post)
    {
        return $this->json($post);
    }

    /**
     * @Route("/post/{slug}", name="blog_by_slug", methods={"GET"})
     * @ParamConverter("post", options={"mapping": {"slug": "slug"}}, class="App:BlogPost")
     * @param $post
     * @return JsonResponse
     */
    public function postBySlug($post)
    {
        return $this->json($post);
    }

    /**
     * @Route("/add", name="blog_add", methods={"POST"})
     * @param Request $request
     * @return JsonResponse
     */
    public function add(Request $request)
    {
        /** @var Serializer $serializer */
        $serializer = $this->get('serializer');

        $blogPost = $serializer->deserialize($request->getContent(), BlogPost::class, 'json');

        $em = $this->getDoctrine()->getManager();
        $em->persist($blogPost);
        $em->flush();

        return $this->json($blogPost);
    }

    /**
     * @Route("/post/{id}", name="blog_delete", methods={"DELETE"})
     * @param BlogPost $post
     * @return JsonResponse
     */
    public function delete(BlogPost $post) {
        $em = $this->getDoctrine()->getManager();
        $em->remove($post);
        $em->flush();

        return $this->json(null, Response::HTTP_NO_CONTENT);
    }
}
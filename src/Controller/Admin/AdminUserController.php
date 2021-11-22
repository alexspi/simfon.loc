<?php

namespace App\Controller\Admin;

use App\Entity\User;
use App\Form\UserType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AdminUserController extends AdminBaseController
{
    /**
     * @Route ("/admin/user",name="admin_user")
     * @return Response
     */
    public function index()
    {

        $users = $this->getDoctrine()->getRepository(User::class)->findAll();

        $forRender = parent::renderDefault();
        $forRender['title'] = 'Пользователи';
        $forRender['users'] = $users;
        return $this->render('admin/user/index.twig', $forRender);

    }

    /**
     * @Route ("/admin/user/create",name="admin_user_create")
     * @param Request $request
     * @param UserPasswordHasherInterface $userPasswordHasher
     * @return RedirectResponse|Response
     */
    public function create(Request $request, UserPasswordHasherInterface $userPasswordHasher)
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $em = $this->getDoctrine()->getManager();
        $form->handleRequest($request);

        if (($form->isSubmitted()) && ($form->isValid())) {
            $password = $userPasswordHasher->hashPassword($user, $form->get('plainPassword')->getData());
            $user->setPassword($password);
            $user->setRoles(['ROLE_ADMIN']);
            $em->persist($user);
            $em->flush();

            return $this->redirectToRoute('admin_user');
        }
        $forRender = parent::renderDefault();
        $forRender['title'] = "форма создания пользователя";
        $forRender['form']= $form->createView();

        return $this->render('admin/user/form.twig',$forRender);

    }

}
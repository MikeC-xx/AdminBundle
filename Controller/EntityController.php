<?php

namespace AdminBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class EntityController extends Controller
{
    /**
     * @Route("/entity/{className}", name="admin_entity_index", defaults={"page" = 1})
     */
    public function indexAction(Request $request, $className, $page)
    {
        $query = $this->getEntityList($request, $className, $page);
        return $this->render('AdminBundle::_partial/index.html.twig', [
            'className' => $className,
            'columns' => $className::getIndexColumns(),
            'list' => $query['list'],
            'page' => $page,
            'pageCount' => $query['pageCount']
        ]);
    }

    /**
     * @Route("/entity/{className}/{id}", name="admin_entity_show", requirements={"id=\d+"})
     */
    public function showAction(Request $request, $className, $id) {
        $formClass = self::getEntityFormClass($className);
        $entity = $this->getEntityRepository($className)->find($id);
        $form = $this->createForm($formClass, $entity);

        return $this->render('AdminBundle::_partial/show.html.twig', [
            'entity' => $entity,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/entity/{className}/{id}/edit", name="admin_entity_edit", requirements={"id=\d+"})
     * @Route("/entity/{className}/new", name="admin_entity_new")
     */
    public function editAction(Request $request, $className, $id = null)
    {
        if ($id === null) {
            $entity = new $className();
        } else {
            $entity = $this->getEntityRepository($className)->find($id);
        }

        $formClass = self::getEntityFormClass($className);

        $form = $this->createForm($formClass, $entity);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            $this->addFlash(
                'success',
                'Záznam byl uložen.'
            );

            return $this->redirectToRoute('admin_entity_show', ['className' => $className, 'id' => $entity->getId()]);
        }

        return $this->render('AdminBundle::_partial/edit.html.twig', [
            'entity' => $entity,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/entity_list?className={className}&page={page}", name="admin_entity_list", defaults={"page" = 1})
     */
    public function listAction(Request $request, $className, $page)
    {
        $query = $this->getEntityList($request, $className, $page);

        return $this->render('AdminBundle::_partial/list_content.html.twig', [
            'columns' => $className::getIndexColumns(),
            'list' => $query['list'],
            'page' => $page,
            'pageCount' => $query['pageCount']
        ]);
    }

    /*
    protected function renderIndexAction(Request $request)
    {
        return $this->render($this->getTemplatePath('index'), [
            'indexRoute' => $this->getRoute(),
            'listRoute' => $this->getRoute('list'),
            'newRoute' => $this->getRoute('new'),
            'showRoute' => $this->getRoute('show'),
            'editRoute' => $this->getRoute('edit'),
            'page' => $request->get('page')
        ]);
    }

    protected function renderShowAction(Request $request, $entity) {
        $formClass = $this->getFormClass();

        $form = $this->createForm($formClass, $entity);

        return $this->render($this->getTemplatePath('show'), [
            'entity' => $entity,
            'editRoute' => $this->getRoute('edit'),
            'form' => $form->createView(),
            'indexRoute' => $this->getRoute(),
            'indexRouteName' => $this->getRoute()
        ]);
    }

    protected function renderEditAction(Request $request, $entity = null)
    {
        $entityClass = $this->getEntityClass();
        if ($entity === null) {
            $entity = new $entityClass();
            $cancelUrl = $this->generateUrl($this->getRoute());
        } else if (get_class($entity) !== $entityClass) {
            throw new \Exception('Unsupported class ' . get_class($entity) . '. Class ' . $entityClass . ' expected.');
        } else {
            $cancelUrl = $this->generateUrl($this->getRoute('show'), ['id' => $entity->getId()]);
        }

        $formClass = $this->getFormClass();

        $form = $this->createForm($formClass, $entity);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getEntityManager();
            $em->persist($entity);
            $em->flush();

            $this->addFlash(
                'success',
                'Záznam byl uložen.'
            );

            return $this->redirectToRoute($this->getRoute('show'), ['id' => $entity->getId()]);
        }

        return $this->render($this->getTemplatePath('edit'), [
            'cancelUrl' => $cancelUrl,
            'entity' => $entity,
            'form' => $form->createView(),
            'indexRoute' => $this->getRoute(),
            'indexRouteName' => $this->getRoute(),
            'newRoute' => $this->getRoute('new'),
            'newRouteName' => $this->getRoute('new')
        ]);
    }
    */

    protected function getEntityList(Request $request, $className, $page)
    {
        $this->checkEntityClassName($className);
        return $this->getEntityRepository($className)->getPage($page, $request->get('search'));
    }

    protected function getEntityRepository($className)
    {
        $entityRepository = $this->getDoctrine()->getManager()->getRepository(self::getEntityRepositoryName($className));
        if (!is_subclass_of($entityRepository, 'AdminBundle\\Repository\\BaseRepository')) {
            throw new \Exception('"' . get_class($entityRepository) . '" class must be subclass of "AdminBundle\\Repository\\BaseRepository" class.');
        }

        return $entityRepository;
    }

    protected function checkEntityClassName($className)
    {
        if (!is_subclass_of($className, 'AdminBundle\\Entity\\BaseEntity')) {
            throw new \Exception('"' . $className . '" class must be subclass of "AdminBundle\\Entity\\BaseEntity" class.');
        }

        return true;
    }

    protected function getEntityClassMetaData($className)
    {
        return $this->getDoctrine()->getManager()->getClassMetaData(self::getEntityRepositoryName($className));
    }

    protected static function getEntityRepositoryName($className)
    {
        $array = explode('\\', $className);
        return $array[0] . ':' . end($array);
    }

    protected static function getEntityFormClass($className)
    {
        $array = explode('\\', $className);
        return $array[0] . '\\Form\\' . end($array) . 'Type';
    }
}

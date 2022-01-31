<?php

namespace AdminBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PropertyAccess\PropertyAccess;

class EntityController extends Controller
{
    /**
     * @Route("/entity/{className}?page={page}", name="admin_entity_index", defaults={"page"=1})
     */
    public function indexAction(Request $request, $className, $page)
    {
        $query = $this->getEntityList($request, $className, $page);
        return $this->render('AdminBundle::_partial/index.html.twig', [
            'className' => $className,
            'columns' => $className::getIndexColumns(),
            'config' => $this->getParameter('admin.config'),
            'list' => $query['list'],
            'page' => $page,
            'pageCount' => $query['pageCount']
        ]);
    }

    /**
     * @Route("/entity/{className}/{id}", name="admin_entity_show", requirements={"id"="\d+"})
     */
    public function showAction(Request $request, $className, $id) {
        //$formClass = self::getEntityFormClass($className);
        $entity = $this->getEntityRepository($className)->find($id);
        //$form = $this->createForm($formClass, $entity);

        return $this->render('AdminBundle::_partial/show.html.twig', [
            'className' => $className,
            'columns' => $className::getShowColumns(),
            'config' => $this->getParameter('admin.config'),
            'entity' => $entity
            //'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/entity/{className}/new", name="admin_entity_new")
     * @Route("/entity/{className}/{id}/edit", name="admin_entity_edit", requirements={"id"="\d+"})
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

            if (array_key_exists('Gedmo\\Uploadable\\Uploadable', class_implements(get_class($entity)))) {
                if (is_a($entity->getFile(), 'Symfony\\Component\\HttpFoundation\\File\\UploadedFile')) {
                    $uploadableManager = $this->get('stof_doctrine_extensions.uploadable.manager');
                    $uploadableManager->markEntityToUpload($entity, $entity->getFile());
                }
            }

            $associationMappings = $this->getEntityClassMetaData($className)->getAssociationMappings();
            $markToUpload = [];
            if (count($associationMappings) > 0) {
                foreach($associationMappings as $key => $value)
                {
                    if (array_key_exists('Gedmo\\Uploadable\\Uploadable', class_implements($value['targetEntity']))) {
                        $markToUpload[] = $key;
                    }
                }
            }

            if (count($markToUpload) > 0) {
                $accessor = PropertyAccess::createPropertyAccessor();
                $uploadableManager = $this->get('stof_doctrine_extensions.uploadable.manager');
                foreach($markToUpload as $property)
                {
                    $document = $accessor->getValue($entity, $property);
                    if ($document) {
                        if (is_a($document->getFile(), 'Symfony\\Component\\HttpFoundation\\File\\UploadedFile')) {
                            $uploadableManager->markEntityToUpload($document, $document->getFile());
                        }                        
                    }                    
                }
            }

            $em->flush();

            $this->addFlash(
                'success',
                'Record was saved.'
            );

            return $this->redirectToRoute('admin_entity_show', ['className' => $className, 'id' => $entity->getId()]);
        }

        return $this->render('AdminBundle::_partial/edit.html.twig', [
            'className' => $className,
            'config' => $this->getParameter('admin.config'),
            'entity' => $entity,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/entity_list/{className}?page={page}", name="admin_entity_list", defaults={"page"=1})
     */
    public function listAction(Request $request, $className, $page)
    {
        $query = $this->getEntityList($request, $className, $page);

        return $this->render('AdminBundle::_partial/list_content.html.twig', [
            'className' => $className,
            'columns' => $className::getIndexColumns(),
            'list' => $query['list'],
            'page' => $page,
            'pageCount' => $query['pageCount']
        ]);
    }

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

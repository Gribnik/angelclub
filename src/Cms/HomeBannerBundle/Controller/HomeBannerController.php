<?php

namespace Cms\HomeBannerBundle\Controller;

use Cms\XutBundle\Entity\Gist;
use Cms\HomeBannerBundle\Form\HomebannerType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class HomeBannerController extends Controller
{
    /**
     * Saves the home banner data
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @throws \Symfony\Component\Security\Core\Exception\AccessDeniedException
     */
    public function setImageAction(Request $request)
    {
        if ($this->_isAdmin()) {
            $homebanner = $this->_getInitialBanner();
            $form = $this->createForm(new HomebannerType(), $homebanner);

            $form->handleRequest($request);
            if ($form->isValid()) {
                $em = $this->getDoctrine()->getManager();

                $em->persist($homebanner);
                $em->flush();

                $this->get('session')->getFlashBag()->add(
                    'notice',
                    'Changes were saved!'
                );

                return $this->redirect($this->generateUrl('cms_xut_homepage'));
            } else {
                $errors = $form->getErrorsAsString();
                $this->get('session')->getFlashBag()->add(
                    'error',
                    "Cannot save changes " . $errors
                );
                return $this->redirect($this->generateUrl('cms_xut_homepage'));
            }
        } else { /* TODO: consolidate this into the single unified method */
            $this->get('session')->getFlashBag()->add(
                'error',
                "You don't have permission to complete this action"
            );
            throw new AccessDeniedException();
        }
    }

    /**
     * Returns home banner edit form content
     *
     * @return mixed
     * @throws \Symfony\Component\Security\Core\Exception\AccessDeniedException
     */
    public function getFormAction()
    {
        if ($this->_isAdmin()) {
            $homebanner = $this->_getInitialBanner();
            $form = $this->createForm(new HomebannerType(), $homebanner);
            $json = array();
            $view = $this->render('CmsHomeBannerBundle:Homebanner:form.html.twig', array(
                'form' => $form->createView()
            ));
            $json['content'] = $view->getContent();

            return $this->get('backpack')->sendJsonResponse($json);
        } else {
            $this->get('session')->getFlashBag()->add(
                'error',
                "You don't have permission to complete this action"
            );
            throw new AccessDeniedException();
        }
    }

    /**
     * Returns home banner images, filtered by a tag
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function getImageAction()
    {
        $homebanner = $this->_getInitialBanner();
        $em = $this->getDoctrine()->getManager();
        $tagname = $homebanner->getName(); // Get image tag for home page banner slides

        $images = '';
        // TODO: add ability to use comma separated tags
        if (!empty($tagname)) {
            $images = $em->createQueryBuilder()
                ->select('bl')
                ->from('CmsXutBundle:Gist', 'bl')
                ->where('bl.type = :gisttype')
                ->setParameter('gisttype', 'image')
                ->addOrderBy('bl.date_created');

            $tag = $em->getRepository('CmsXutBundle:Tag')->findOneByName($tagname);
            if (count($tag) > 0) {
                $images = $images->innerJoin('bl.tags', 'tg')
                    ->andWhere('tg.id = :tag')
                    ->setParameter('tag', $tag->getId());

                $images = $images->getQuery()
                    ->getResult();
            } else {
                $images = '';
            }
        }

        return $this->render('CmsHomeBannerBundle:Homebanner:images.html.twig', array(
            'images' => $images,
            'banner' => $homebanner
        ));
    }

    public function previewAction(Request $request)
    {
        if ($this->_isAdmin()) {
            $homebanner = $this->_getInitialBanner();
            $form = $this->createForm(new HomebannerType(), $homebanner);

            $form->handleRequest($request);
            if ($form->isValid()) {

                $view = $this->render('CmsHomeBannerBundle:Admin:banner_preview.html.twig', array(
                    'banner' => $homebanner
                ));

                /* TODO: Make an observer in order to remove uploaded images for preview */

                $json = array(
                    'status'  => 'success',
                    'content' => $view->getContent()
                );

                return $this->get('backpack')->sendJsonResponse($json);

            } else {
                $errors = $form->getErrorsAsString();
                $json = array(
                    'status'  => 'error',
                    'content' => $errors
                );
                return $this->get('backpack')->sendJsonResponse($json);
            }
        } else {
            /* TODO: make some error handling here */
            throw new AccessDeniedException();
        }
    }

    /**
     * Returns first banner. Temporary solution
     *
     * @return Homebanner
     */
    protected function _getInitialBanner()
    {
        $em = $this->getDoctrine()->getManager();
        $banner = $em->getRepository('CmsXutBundle:Gist')->findOneByType('homebanner');
        $currentDate = date("Y-m-d H:i:s");
        if (NULL === $banner) {
            $banner = new Gist();
            $banner->setType('homebanner');
            $banner->setDateCreated(new \DateTime($currentDate));
        }

        $banner->setDateUpdated(new \DateTime($currentDate));
        return $banner;
    }

    /**
     * Check if current user has admin role
     *
     * @return bool
     */
    protected function _isAdmin()
    {
        return true === $this->get('security.context')->isGranted('ROLE_ADMIN');
    }
}

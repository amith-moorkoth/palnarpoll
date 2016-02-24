<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use HWI\Bundle\OAuthBundle\OAuth\ResourceOwnerInterface;
use HWI\Bundle\OAuthBundle\Security\Core\Authentication\Token\OAuthToken;
use HWI\Bundle\OAuthBundle\Security\Core\Exception\AccountNotLinkedException;
use AppBundle\Entity\logins;
use AppBundle\Entity\polls;
use AppBundle\Entity\vote;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Cookie;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction(Request $request)
    {
        // replace this example code with whatever you need
        return $this->render('default/index.html.twig', array(
            'base_dir' => realpath($this->getParameter('kernel.root_dir').'/..'),
        ));


    }

    /**
     * @Route("/authenticate", name="authenticate")
     */
    public function authenticate(Request $request)
    {
        // replace this example code with whatever you need
        $Full_Name=$request->request->get('name', '0');
        $email=$request->request->get('email_id', '0');

        if($Full_Name!='0' or $email!='0'){
            $log = new logins();
            $log->setEmailId($email);
            $log->setName($Full_Name);
            $log->setDate(new \DateTime());
            $em = $this->getDoctrine()->getManager();
            $validate = $em->getRepository('AppBundle:logins')->findByEmailId($email);
            if (!$validate) {
                $em->persist($log);
                $em->flush();
            }
            $session = new Session();
            $session->set("email_id",$email);
        }

        return $this->render('null.html.twig', array(
            'base_dir' => realpath($this->getParameter('kernel.root_dir').'/..'),
        ));
    }

    /**
     * @Route("/redirects", name="redirects")
     */
    public function redirects(Request $request)
    {
        $session = new Session();
        $a=$session->get("email_id");
        if(isset($a)){
            return $this->redirectToRoute('pollings');
        }
        else{
            return $this->redirectToRoute('homepage');
    }}

    /**
     * @Route("/pollings", name="pollings")
     */
    public function pollings(Request $request)
    {
        $session = new Session();
        $a=$session->get("email_id");
        if(isset($a)){
            return $this->render('default/polling.html.twig', array(
                'admin' => config::admin(),
                'base_dir' => realpath($this->getParameter('kernel.root_dir') . '/..'),
            ));
        }
        else{
            return $this->redirectToRoute('homepage');
        }
    }


    /**
     * @Route("/createpoll", name="createpoll")
     */
    public function createpoll(Request $request)
    {
        $a=$this->get('request')->request->all();
        $b="";
        foreach ($a as $key => $item)
        {
            if (strpos($key, 'p_option') !== false)
                $b.=$item.";:;:;";
        }
        $poll_ques=$a['poll_ques'];
        $opt=$b;
        $email=$a['sme'];
        $date=$a['sme1'];
        /*$dd=explode(" - ",$date);
        $dd[0]=explode(" ",$dd[0]);
        $dd[1]=explode(" ",$dd[1]);
        $contractDateBegin = date('y/m/d', strtotime($dd[0][0]));
        $contractDateEnd = date('y/m/d', strtotime($dd[1][0]));
        echo $contractDateBegin.$contractDateEnd;*/

        if($poll_ques!='0' or $opt!='0' or $email==config::admin() or $date!='') {
            $log = new polls();
            $log->setPollQues($poll_ques);
            $log->setOptions($opt);
            $log->setDuration($date);
            $log->setDate(new \DateTime());
            $em = $this->getDoctrine()->getManager();
            $em->persist($log);
            $em->flush();
        }else{echo"email not valid";}
            return $this->render('null.html.twig', array(
            'base_dir' => realpath($this->getParameter('kernel.root_dir') . '/..'),
        ));
    }
    /**
     * @Route("/getMePolls", name="getMePolls")
     */
    public function getMePolls(Request $request)
    {
        $count=$request->request->get('current', '0');
        $em = $this->getDoctrine()->getManager();
        $query = $em->createQuery('SELECT p FROM AppBundle:polls p WHERE p.id > :id')->setParameter('id', $count);
        $products = $query->setMaxResults(1)->getOneOrNullResult();
        if($products!=""){
        $id=$products->getId();
        $ques=$products->getPollQues();
        $opt=$products->getOptions();
        $opt=explode(";:;:;",$opt);
        $opt = array_filter($opt);
        $dur=$products->getDuration();
            $dd=explode(" - ",$dur);
            if(!isset($dd[0])){$dd[0]=" ";}
            if(!isset($dd[1])){$dd[1]=" ";}
            $dd[0]=explode(" ",$dd[0]);
            $dd[1]=explode(" ",$dd[1]);
            if(!isset($dd[0][0])){$dd[0][0]=" ";}
            if(!isset($dd[1][0])){$dd[1][0]=" ";}
            $contractDateBegin = date('y/m/d', strtotime($dd[0][0]));
            $contractDateEnd = date('y/m/d', strtotime($dd[1][0]));
            $today = date('y/m/d');
            if (($today > $contractDateBegin) && ($today < $contractDateEnd))
            {
                $session = new Session();
                $a=$session->get("email_id");
                $em = $this->getDoctrine()->getManager();
                $validate = $em->getRepository('AppBundle:vote')->findOneBy(
                    array('poll_id' => $id, 'email_id' => $a)
                );
                if ($validate) {
                    return $this->render('default/viewpollconfirm.html.twig', array(
                        'me' => $validate->getVote(),
                        'id' => $id,
                        'ques' => $ques,
                        'opt' => $opt,
                        'dur' => $dur,
                    ));
                }
                else{

                    return $this->render('default/viewpoll.html.twig', array(
                        'id' => $id,
                        'ques' => $ques,
                        'opt' => $opt,
                        'dur' => $dur,
                    ));}
            }else{
                if($today < $contractDateBegin){
                    return $this->render('null.html.twig', array(
                        'base_dir' => realpath($this->getParameter('kernel.root_dir') . '/..'),
                    ));
                }else if($today > $contractDateEnd){
                    $em = $this->getDoctrine()->getManager();
                    $myopt=array();
                    foreach($opt as $val){
                        $validate = $em->getRepository('AppBundle:vote')->findBy(
                            array('poll_id' => $id,'vote' =>$val)
                        );
                        $valcount=count($validate);
                        //$varss=array($val=>$valcount);
                        //array_push($myopt,$varss);
                        $myopt[$val]=$valcount;
                    }
                    return $this->render('default/viewpollresult.html.twig', array(
                        'id' => $id,
                        'ques' => $ques,
                        'opt' => $opt,
                        'dur' => $dur,
                        'arr' => $myopt,
                    ));
                }
            }



        }else{
            return $this->render('null.html.twig', array(
                'base_dir' => realpath($this->getParameter('kernel.root_dir') . '/..'),
            ));
        }
    }

    /**
     * @Route("/voteepoll", name="voteepoll")
     */
    public function voteepoll(Request $request)
    {
        $polls=$this->get('request')->request->all();
        $log = new vote();
        $session = new Session();
        $a=$session->get("email_id");
        $log->setEmailId($a);
        $log->setPollId($polls['sme']);
        $log->setVote($polls['me']);
        $log->setDate(new \DateTime());
        $em = $this->getDoctrine()->getManager();
        $validate = $em->getRepository('AppBundle:vote')->findOneBy(
            array('poll_id' => $polls['sme'], 'email_id' => $a)
        );
        if (!$validate) {
            $em->persist($log);
            $em->flush();
            $query = $em->createQuery('SELECT p FROM AppBundle:polls p WHERE p.id = :id')->setParameter('id', $polls['sme']);
            $products = $query->setMaxResults(1)->getOneOrNullResult();
            $opt=$products->getOptions();
            $opt=explode(";:;:;",$opt);
            $opt = array_filter($opt);
            return $this->render('default/viewpollconfirm.html.twig', array(
                'me' => $polls['me'],
                'id' => $polls['sme'],
                'ques' => $products->getPollQues(),
                'opt' => $opt,
                'dur' => $products->getDuration(),
            ));
        }
        else{
            return $this->render('null.html.twig', array(
                'base_dir' => realpath($this->getParameter('kernel.root_dir') . '/..'),
            ));
        }
    }

    /**
     * @Route("/viewshortpoll", name="viewshortpoll")
     */
    public function viewshortpoll(Request $request)
    {
        $val=$request->request->get('val', '0');
        $off=$request->request->get('p_off', '0');
        $em = $this->getDoctrine()->getManager();
        $query = $em->createQuery("SELECT p FROM AppBundle:polls p WHERE p.pollQues LIKE '%$val%'")
            ->setMaxResults(10)
            ->setFirstResult($off);
        $products = $query->getResult();
        if($products!="") {
            $arr=array();
            $arr1=array();
            foreach($products as $pro) {
                $arr1['ques']=$pro->getPollQues();
                $arr1['dates']=$pro->getDuration();
                $arr1['datess']=$pro->getDate();
                $arr1['id']=$pro->getId();
                $opt=$pro->getOptions();
                $opt=explode(";:;:;",$opt);
                $opt = array_filter($opt);
                $opt = implode(",",$opt);
                $arr1['opt']=$opt;
                array_push($arr, $arr1);

            }
            return $this->render('default/viewpollEdit.html.twig', array(
                'arr' => $arr,
            ));
        }else{
            return $this->render('null.html.twig', array(
                'base_dir' => realpath($this->getParameter('kernel.root_dir') . '/..'),
            ));
        }
    }

    /**
     * @Route("/editshortpoll", name="editshortpoll")
     */
    public function editshortpoll(Request $request)
    {
        $val=$request->request->get('val', '0');
        $em = $this->getDoctrine()->getManager();
        $query = $em->createQuery('SELECT p FROM AppBundle:polls p WHERE p.id = :id')->setParameter('id', $val);
        $products = $query->setMaxResults(1)->getOneOrNullResult();
        if($products!="") {
            $id = $products->getId();
            $ques = $products->getPollQues();
            $opt = $products->getOptions();
            $opt=explode(";:;:;",$opt);
            $opt = array_filter($opt);
            $dur = $products->getDuration();
            return $this->render('default/viewpollEditIt.html.twig', array(
                'id' => $id,
                'ques' => $ques,
                'opt' => $opt,
                'dur' => $dur,
            ));
        }else{
            return $this->render('null.html.twig', array(
                'base_dir' => realpath($this->getParameter('kernel.root_dir') . '/..'),
            ));
        }

    }
    /**
     * @Route("/updatepoll", name="updatepoll")
     */
    public function updatepoll(Request $request)
    {
        $a=$this->get('request')->request->all();
        $b="";
        foreach ($a as $key => $item)
        {
            if (strpos($key, 'p_option') !== false)
                $b.=$item.";:;:;";
        }
        $poll_ques=$a['poll_ques'];
        $opt=$b;
        $email=$a['sme'];
        $date=$a['sme1'];
        $id=$a['ids'];
        /*$dd=explode(" - ",$date);
        $dd[0]=explode(" ",$dd[0]);
        $dd[1]=explode(" ",$dd[1]);
        $contractDateBegin = date('y/m/d', strtotime($dd[0][0]));
        $contractDateEnd = date('y/m/d', strtotime($dd[1][0]));
        echo $contractDateBegin.$contractDateEnd;*/
        print_r($a);
        if($poll_ques!='0' or $opt!='0' or $email==config::admin() or $date!='') {
            $log = new polls();
            $em = $this->getDoctrine()->getManager();
            $product = $em->getRepository('AppBundle:polls')->find($id);
            $product->setPollQues($poll_ques);
            $product->setOptions($opt);
            $product->setDuration($date);
            $em->flush();
            return $this->render('null.html.twig', array(
                'base_dir' => realpath($this->getParameter('kernel.root_dir') . '/..'),
            ));
        }else{echo"email not valid";}
        return $this->render('null.html.twig', array(
            'base_dir' => realpath($this->getParameter('kernel.root_dir') . '/..'),
        ));
    }
}

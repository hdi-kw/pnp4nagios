<?php defined('SYSPATH') OR die('No direct access allowed.');
/**
 * Graph controller.
 *
 * @package    PNP4Nagios
 * @author     Joerg Linge
 * @license    GPL
 */
class Page_Controller extends System_Controller  {

    public function __construct(){
        parent::__construct();
        $this->template                = $this->add_view('template');
        $this->template->page          = $this->add_view('page');
        $this->template->page->graph_content  = $this->add_view('graph_content');
        $this->template->page->graph_content->graph_width = ($this->config->conf['graph_width'] + 85);
        $this->template->page->graph_content->timerange_select = $this->add_view('timerange_select');
        $this->template->page->header         = $this->add_view('header');
        $this->template->page->logo_box       = $this->add_view('logo_box');
        $this->is_authorized=TRUE;
    }

    public function index(){
        if( !$this->isAuthorizedFor('pages') ){
            throw new Kohana_Exception('error.auth-pages');
        }
        $this->page = pnp::clean($this->input->get('page'));
        if($this->page == ""){
            $this->page = $this->data->getFirstPage();
        }
        if($this->page == ""){
            throw new Kohana_Exception('error.page-config-dir', $this->config->conf['page_dir']);
        }
        if($this->view == ""){
            $this->view = $this->config->conf['overview-range'];
        }
        $this->data->buildPageStruct($this->page,$this->view);
        $this->template->page->header->title = Kohana::lang('common.page',$this->data->PAGE_DEF['page_name']);
        $this->url = "?page&page=$this->page";
           // Timerange Box Vars
           $this->template->page->timerange_box = $this->add_view('timerange_box');
           $this->template->page->timerange_box->timeranges = $this->data->TIMERANGE;
        // Pages Box
        $this->pages = $this->data->getPages();
           $this->template->page->pages_box = $this->add_view('pages_box');
           $this->template->page->pages_box->pages = $this->pages;
        // Basket Box
        $this->template->page->basket_box      = $this->add_view('basket_box');
        // Icon Box    
        $this->template->page->icon_box      = $this->add_view('icon_box');
        $this->template->page->icon_box->position = "page";

    }

    public function basket(){
        $basket = $this->session->get("basket");
        if($this->view == ""){
            $this->view = $this->config->conf['overview-range'];
        }
        if(is_array($basket) && sizeof($basket) > 0){
            foreach($basket as $item){
                # explode host::service::source
                $slices = explode("::",$item);
		if(sizeof($slices) == 2)
                    $this->data->buildDataStruct($slices[0], $slices[1], $this->view);
		if(sizeof($slices) == 3)
                    $this->data->buildDataStruct($slices[0], $slices[1], $this->view, $slices[2]);
            }
            $this->template->page->basket_box      = $this->add_view('basket_box');
            $this->template->page->header->title = Kohana::lang('common.page-basket');
            $this->url = "basket?";
               // Timerange Box Vars
               $this->template->page->timerange_box = $this->add_view('timerange_box');
               $this->template->page->timerange_box->timeranges = $this->data->TIMERANGE;
                // Pages Box
            $this->pages = $this->data->getPages();
               $this->template->page->pages_box = $this->add_view('pages_box');
               $this->template->page->pages_box->pages = $this->pages;
            // Icon Box    
            $this->template->page->icon_box      = $this->add_view('icon_box');
            $this->template->page->icon_box->position = "basket";
        }else{
            url::redirect("start", 302);
        }
    }
}

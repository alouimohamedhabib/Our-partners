<?php

/**
 * Class PartenaireListModuleFrontController
 */
class PartenaireListModuleFrontController extends ModuleFrontController
{
    public function setMedia()
    {
        parent::setMedia();
        Tools::addCSS(_PS_MODULE_DIR_ . 'partenaire/css/partenaire.css');
    }


    public function initContent()
    {

        parent::initContent();
        $data = $this->getAllPartenaire();
        $this->context->smarty->assign(array(
            'data' => $data
        ));
        $this->setTemplate("list.tpl");
    }


    public function getAllPartenaire()
    {
        $db = DB::getInstance();
        $data = $db->executeS("select * from " . _DB_PREFIX_ . "partenaire order by nom asc  ");
        $this->context->smarty->assign(array(
            'data' => $data
        ));

        return $data;
    }
}
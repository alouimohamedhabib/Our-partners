<?php

class PartenaireController extends ModuleAdminController

{
    public function postProcess()
    {
        if (($id_thumb = Tools::getValue('deleteThumb', false)) !== false) {
            if (file_exists(_PS_CAT_IMG_DIR_ . (int)Tools::getValue('id_category') . '-' . (int)$id_thumb . '_thumb.jpg')
                && !unlink(_PS_CAT_IMG_DIR_ . (int)Tools::getValue('id_category') . '-' . (int)$id_thumb . '_thumb.jpg')
            )
                $this->context->controller->errors[] = Tools::displayError('Error while delete');

            if (empty($this->context->controller->errors))
                Tools::clearSmartyCache();

            Tools::redirectAdmin(Context::getContext()->link->getAdminLink('AdminCategories') . '&id_category='
                . (int)Tools::getValue('id_category') . '&updatecategory');
        }

        parent::postProcess();
    }
}


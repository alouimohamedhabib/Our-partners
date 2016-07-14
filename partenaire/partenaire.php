<?php

/**
 * Module : partenaires
 * @author : Dev Ingenierie
 */

if (!defined('_PS_VERSION_'))
    exit();

class Partenaire extends Module
{
    /**
     * Partenaire constructor.
     */
    function __construct()
    {
        $this->name = 'partenaire';
        $this->tab = 'front_office_features';
        $this->version = '1.0';
        $this->version = '1.0';
        $this->author = 'Aloui Mohamed habib';
        $this->need_instance = 0;
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('Nos partenaires');
        $this->description = $this->l('Les partenaires');
    }


    /**
     * @return bool
     */
    public function install()
    {
        if (!parent::install() || !$this->registerHook('top'))
            return false;

        $res = Db::getInstance()->execute(
            'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'partenaire` (
			`id_parteianire` int(10) unsigned NOT NULL auto_increment,
			`nom` varchar(120) NOT NULL,
			`lien` varchar(255) NOT NULL,
			`image` varchar(255) NOT NULL,
			`descriptif` varchar(255) NOT NULL,
			PRIMARY KEY (`id_parteianire`))
			ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8'
        );

        return (bool)$res;
    }


    public function uninstall()
    {
        $res = Db::getInstance()->execute('DROP TABLE IF EXISTS `' . _DB_PREFIX_ . 'partenaire`');

        if ($res == 0 || !parent::uninstall())
            return false;
    }

    /**
     * Hook for the header
     * @param $params
     * @param  $description  define whether display the description or not
     */
    public function hookHeader($params, $description = true)
    {
        $db = DB::getInstance();
        $data = $db->executeS("select * from " . _DB_PREFIX_ . "partenaire order by nom asc  ");
        $this->context->smarty->assign(array(
            'data' => $data,
            'desc' => $description
        ));
        $this->context->controller->addCSS($this->_path . 'partenaire.css', 'all');
        return $this->display(__FILE__, 'hookHeader.tpl');
    }

    public function hookDisplayLeftColumn($params)
    {
        $data = $this->displayPartenaires();
        $this->context->smarty->assign(
            array('data' => $data)
        );
        $this->context->controller->addCSS($this->_path . 'partenaire.css', 'all');
        return $this->display(__FILE__, 'hookLeftColumn.tpl');
    }

    /**
     *
     * @return string
     */
    public function getContent()
    {
        $output = null;

        /**
         * Add record to db
         */
        if (Tools::isSubmit('submit' . $this->name)) {

            /**
             * Deal with the image
             */
            $img_link = "default.jpg";
            if (isset($_FILES['file']) && is_uploaded_file($_FILES['file']['tmp_name'])) {
                if ($_FILES['file']['size'] > (Configuration::get('PS_ATTACHMENT_MAXIMUM_SIZE') * 1024 * 1024))
                    $this->errors[] = sprintf(
                        $this->l('The file is too large. Maximum size allowed is: %1$d kB. The file you are trying to upload is %2$d kB.'),
                        (Configuration::get('PS_ATTACHMENT_MAXIMUM_SIZE') * 1024),
                        number_format(($_FILES['file']['size'] / 1024), 2, '.', '')
                    );
                else {
                    do $uniqid = sha1(microtime());
                    while (file_exists(_PS_UPLOAD_DIR_ . $uniqid));
                    $extension = pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION);
                    if (!move_uploaded_file($_FILES['file']['tmp_name'], _PS_UPLOAD_DIR_ . $uniqid . "." . $extension))
                        $this->errors[] = $this->l('Failed to copy the file.');
                    $_POST['file_name'] = $_FILES['file']['name'];
                    @unlink($_FILES['file']['tmp_name']);
                    if (!sizeof($this->errors) && isset($a) && file_exists(_PS_UPLOAD_DIR_ . $a->file))
                        unlink(_PS_UPLOAD_DIR_ . $a->file);
                    $_POST['file'] = $uniqid;
                    $_POST['mime'] = $_FILES['file']['type'];
                    $img_link = $uniqid . "." . $extension;
                }
            }

            $db = Db::getInstance();
            $name = Tools::getValue('nom');
            $lien = Tools::getValue('lien');
            $descriptif = Tools::getValue('descriptif');

            $db->insert('partenaire', array(
                'nom' => pSQL($name),
                'lien' => pSQL($lien),
                'descriptif' => pSQL($descriptif),
                'image' => pSQL($img_link),
            ));
            $this->_html .= "<p class='alert alert-success'>Partenaire ajouté avec succée</p>";

            /**
             * check the inserted inputs
             */
            if (!$name || empty($name) || !Validate::isGenericName($name)
                || !$lien || empty($lien) || !Validate::isGenericName($lien)
                || !$name || empty($name) || !Validate::isGenericName($name)
                || !$descriptif || empty($descriptif) || !Validate::isGenericName($descriptif)
            )
                $output .= $this->displayError($this->l('Invalid Configuration value'));
            else {
                Configuration::updateValue('partenaire', $name);
                $output .= $this->displayConfirmation($this->l('Settings updated'));
            }

        } else
            // update action form
            if (isset($_GET["updateaction"]) && isset($_GET['id_partenaire'])) {
                $id_partenaire = Tools::getValue('id_partenaire');
                $data = $this->getPartenaire($id_partenaire);
                return $this->renderForm();
            } // save changes
            else
                if (Tools::isSubmit('submitUpdate')) {
                    // upload new image
                    if (isset($_FILES['file']) && is_uploaded_file($_FILES['file']['tmp_name'])) {
                        $imageUrl = Tools::getValue('imageUrl');

                        if ($_FILES['file']['size'] > (Configuration::get('PS_ATTACHMENT_MAXIMUM_SIZE') * 1024 * 1024))
                            $this->errors[] = sprintf(
                                $this->l('The file is too large. Maximum size allowed is: %1$d kB. The file you are trying to upload is %2$d kB.'),
                                (Configuration::get('PS_ATTACHMENT_MAXIMUM_SIZE') * 1024),
                                number_format(($_FILES['file']['size'] / 1024), 2, '.', '')
                            );
                        else {
                            do $uniqid = sha1(microtime());
                            while (file_exists(_PS_UPLOAD_DIR_ . $uniqid));
                            $extension = pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION);
                            if (!move_uploaded_file($_FILES['file']['tmp_name'], _PS_UPLOAD_DIR_ . $imageUrl))
                                $this->errors[] = $this->l('Failed to copy the file.');
                            $_POST['file_name'] = $_FILES['file']['name'];
                            @unlink($_FILES['file']['tmp_name']);
                            if (!sizeof($this->errors) && isset($a) && file_exists(_PS_UPLOAD_DIR_ . $a->file))
                                unlink(_PS_UPLOAD_DIR_ . $a->file);
                            $_POST['file'] = $uniqid;
                            $_POST['mime'] = $_FILES['file']['type'];
                            $img_link = $uniqid . "." . $extension;
                        }
                    }
                    $this->saveChangesPartenaire();
                    $this->_html .= "<p class='alert alert-success'>Partenaire mise a jour avec succée</p>";
                } else
                    // delete action
                    if (isset($_GET["deleteaction"]) && isset($_GET['id_partenaire'])) {
                        $this->deletePartenaire();
                        $this->_html .= "<p class='alert alert-success'>Partenaire supprimé</p>";
                    }
        // current and the add form
        $this->_html .= $this->renderList();
        $this->_html .= $this->displayForm();
        return $this->_html;
    }


    public function deletePartenaire()
    {
        $id_partenaire = Tools::getValue('id_partenaire');
        $query = Db::getInstance()->delete('partenaire', 'id_partenaire = ' . (int)$id_partenaire);
    }

    public function saveChangesPartenaire()
    {
        $nom = Tools::getValue('nom');
        $lien = Tools::getValue('lien');
        $descriptif = pSQL(Tools::getValue('descriptif'));
        $id_partenaire = Tools::getValue('id_partenaire');
        $query = Db::getInstance()->update('partenaire', array('nom' => $nom, 'lien' => $lien, 'descriptif' => $descriptif), 'id_partenaire = ' . (int)$id_partenaire);
    }

    public function renderForm()
    {
        $partenaire = $this->getPartenaire();
        $fields_form = array(
            'form' => array(
                'legend' => array(
                    'title' => $this->l('Settings'),
                    'icon' => 'icon-cogs'
                ),
                'input' => array(
                    array(
                        'type' => 'text',
                        'label' => $this->l('Nom du partenaire'),
                        'name' => 'nom',
                        'class' => 'fixed-width-md',
                    ),
                    array(
                        'type' => 'textarea',
                        'label' => $this->l('Lien du partenaire'),
                        'name' => 'lien',
                        'class' => 'fixed-width-md',
                    ),
                    array(
                        'type' => 'textarea',
                        'label' => $this->l('Descriptif du partenaire'),
                        'name' => 'descriptif',
                        'class' => 'fixed-width-md',
                    ),
                    array(
                        'type' => 'file',
                        'label' => $this->l('Image du partenaire'),
                        'name' => 'file',
                        'class' => 'fixed-width-md',
                        'thumb' => (_PS_BASE_URL_ . __PS_BASE_URI__ . 'upload/' . $partenaire['image'])
                    ),
                    array(
                        'type' => 'hidden',
                        'name' => 'imageUrl',
                    ),
                    array(
                        'type' => 'hidden',
                        'name' => 'id_partenaire',
                    ),
                ),
                'submit' => array(
                    'title' => $this->l('Save'),
                )
            ),
        );

        $helper = new HelperForm();
        $helper->show_toolbar = false;
        $helper->table = $this->table;
        $lang = new Language((int)Configuration::get('PS_LANG_DEFAULT'));
        $helper->default_form_language = $lang->id;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') ? Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') : 0;
        $helper->identifier = $this->identifier;
        $helper->submit_action = 'submitUpdate';
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false) . '&configure=' . $this->name . '&tab_module=' . $this->tab . '&module_name=' . $this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->tpl_vars = array(
            'fields_value' => $partenaire,
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id
        );

        return $helper->generateForm(array($fields_form));
    }

    public function renderList()
    {

        $fields_list = array(
            'id_partenaire' => array(
                'title' => $this->l('Id'),
                'width' => 140,
                'type' => 'text',
            ),
            'nom' => array(
                'title' => $this->l('Name'),
                'width' => 140,
                'type' => 'text',
            ),
            'lien' => array(
                'title' => $this->l('Lien'),
                'width' => 140,
                'type' => 'text',
            )
        );

        $helper_list = New HelperList();
        $helper_list->module = $this;
        $helper_list->title = $this->l('Liste des partenaires');
        $helper_list->shopLinkType = '';
        $helper_list->no_link = true;
        $helper_list->show_toolbar = true;
        $helper_list->simple_header = false;
        $helper_list->identifier = 'id_partenaire';
        $helper_list->table = 'action';
        $helper_list->currentIndex = $this->context->link->getAdminLink('AdminModules', false) . '&configure=partenaire';
        $helper_list->token = Tools::getAdminTokenLite('AdminModules');
        $helper_list->actions = array('edit', 'delete', 'view');

        /* Before 1.6.0.7 displayEnableLink() could not be overridden in Module class
           we declare another row action instead 			*/
        if (version_compare(_PS_VERSION_, '1.6.0.7', '<')) {
            unset($fields_list['subscribed']);
            $helper_list->actions = array_merge($helper_list->actions, array('unsubscribe'));
        }

        // This is needed for displayEnableLink to avoid code duplication
        $this->_helperlist = $helper_list;

        /* Retrieve list data */
        $subscribers = $this->displayPartenaires();
        $helper_list->listTotal = count($subscribers);

        /* Paginate the result */
        $page = ($page = Tools::getValue('submitFilter' . $helper_list->table)) ? $page : 1;
        $pagination = ($pagination = Tools::getValue($helper_list->table . '_pagination')) ? $pagination : 50;
        $subscribers = $this->paginatePartenaire($subscribers, $page, $pagination);

        return $helper_list->generateList($subscribers, $fields_list);
    }

    public function displayPartenaires()
    {
        $dbquery = ' SELECT * FROM ' . _DB_PREFIX_ . 'partenaire order by id_partenaire asc';

        $subscribers = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($dbquery);
        return $subscribers;


    }


    public function getPartenaire()
    {
        if (isset($_GET['id_partenaire'])) {
            $id = $_GET['id_partenaire'];
            $dbquery = ' SELECT * FROM ' . _DB_PREFIX_ . 'partenaire where id_partenaire = ' . $id . ' order by nom asc';

            $partenaire = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($dbquery);

            return array(
                'id_partenaire' => $id,
                'nom' => $partenaire[0]['nom'],
                'lien' => $partenaire[0]['lien'],
                'descriptif' => $partenaire[0]['descriptif'],
                'image' => $partenaire[0]['image'],
                'imageUrl' => $partenaire[0]['image'],
            );


        }
    }

    /**
     * @param $subscribers
     * @param int $page
     * @param int $pagination
     * @return array
     */
    public function paginatePartenaire($subscribers, $page = 1, $pagination = 50)
    {
        if (count($subscribers) > $pagination)
            $subscribers = array_slice($subscribers, $pagination * ($page - 1), $pagination);

        return $subscribers;
    }

    /**
     * @return mixed
     */
    public function displayForm()
    {
        $default_lang = (int)Configuration::get('PS_LANG_DEFAULT');

        // Init Fields form array
        $fields_form[0]['form'] = array(
            'legend' => array(
                'title' => $this->l('Settings'),
            ),
            'input' => array(
                array(
                    'type' => 'text',
                    'label' => $this->l('Nom'),
                    'name' => 'nom',
                    'size' => 120,
                    'required' => true
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Lien'),
                    'name' => 'lien',
                    'size' => 255,
                    'required' => true
                ),
                array(
                    'type' => 'file',
                    'label' => $this->l('Image'),
                    'name' => 'file',
                    'display_image' => true,
                    'hint' => $this->l('Upload a category logo from your computer.'),
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Description'),
                    'name' => 'descriptif',
                    'size' => 255,
                    'required' => true
                )
            ),
            'submit' => array(
                'title' => $this->l('Save'),
                'class' => 'button'
            )
        );

        $helper = new HelperForm();

        // Module, token and currentIndex
        $helper->module = $this;
        $helper->name_controller = $this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->currentIndex = AdminController::$currentIndex . '&configure=' . $this->name;

        // Language
        $helper->default_form_language = $default_lang;
        $helper->allow_employee_form_lang = $default_lang;

        // Title and toolbar
        $helper->title = $this->displayName;
        $helper->show_toolbar = true;        // false -> remove toolbar
        $helper->toolbar_scroll = true;      // yes - > Toolbar is always visible on the top of the screen.
        $helper->submit_action = 'submit' . $this->name;
        $helper->toolbar_btn = array(
            'save' =>
                array(
                    'desc' => $this->l('Save'),
                    'href' => AdminController::$currentIndex . '&configure=' . $this->name . '&save' . $this->name .
                        '&token=' . Tools::getAdminTokenLite('AdminModules'),
                ),
            'back' => array(
                'href' => AdminController::$currentIndex . '&token=' . Tools::getAdminTokenLite('AdminModules'),
                'desc' => $this->l('Back to list')
            )
        );

        // Load current value
        $helper->fields_value['partenaire'] = Configuration::get('partenaire');

        return $helper->generateForm($fields_form);
    }


}


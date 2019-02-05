<?php
include_once "class.ilHSLUAddPictureConfig.php";
/**
 * Example configuration user interface class
 *
 * @author Stephan Winiker <stephan.winiker@hslu.ch>
 * @version $Id$
 *
 */
class ilHSLUAddPictureConfigGUI extends ilPluginConfigGUI {
	private $pl;
	private $config;
	private $DIC;

    /**
	* Handles all commmands, default is "configure"
	*/
	function performCommand($cmd) {
		switch ($cmd)
		{
			case "configure":
			case "save":
			    global $DIC;
			    $this->DIC = &$DIC;
			    $this->pl = $this->getPluginObject();
			    $this->config = ilHSLUAddPictureConfig::getInstance();
				$this->$cmd();
				break;

		}
	}

	/**
	 * Configure screen
	 */
	function configure() {
		$form = $this->initConfigurationForm();
		$this->DIC->ui()->mainTemplate()->setContent($form->getHTML());
	}
	
	/**
	 * Init configuration form.
	 *
	 * @return object form object
	 */
	public function initConfigurationForm() {
		$form = new ilPropertyFormGUI();
		
		$ws_item = new ilTextInputGUI(
				$this->pl->txt('ws_url'),
				'ws_url'
				);
		$ws_item->setInfo($this->pl->txt('ws_url_desc'));
		$ws_item->setRequired(true);
		$ws_item->setValue($this->config->get('ws_url'));
		$form->addItem($ws_item);
		
		$ws_item = new ilTextInputGUI(
				$this->pl->txt('ws_user'),
				'ws_user'
				);
		$ws_item->setInfo($this->pl->txt('ws_user_desc'));
		$ws_item->setRequired(true);
		$ws_item->setValue($this->config->get('ws_user'));
		$form->addItem($ws_item);
		
		$ws_item = new ilPasswordInputGUI(
				$this->pl->txt('ws_password'),
				'ws_password'
				);
		$ws_item->setInfo($this->pl->txt('ws_password_desc'));
		$ws_item->setSkipSyntaxCheck(true);
		$ws_item->setRequired(true);
		$ws_item->setRetype(false);
		$ws_item->setValue($this->config->get('ws_password') == '' ? '' : '(__unchanged__)');
		$form->addItem($ws_item);
		
		$form->addCommandButton('save', $this->DIC->language()->txt('save'));
		
		$form->setTitle($this->pl->txt('title'));
		$form->setDescription($this->pl->txt('description'));
		$form->setFormAction($this->DIC->ctrl()->getFormAction($this));
		
		return $form;
	}
	
	public function save() {
		$this->pl = $this->getPluginObject();
		$form = $this->initConfigurationForm();
		if ($form->checkInput()) {
			$form_input = [];

			$form_input['ws_url'] = $form->getInput('ws_url');
		    $form_input['ws_user'] = $form->getInput('ws_user');
		    $form_input['ws_password'] = $form->getInput('ws_password') == '(__unchanged__)' ? $this->config->get('ws_password') : $form->getInput('ws_password');	    
		    
		    if ($r = $this->config->saveConf($form_input) > 0) {
		    	ilUtil::sendSuccess($this->pl->txt('saved_success'), true);
		    } else if ($r == 0) {
		    	ilUtil::sendInfo($this->pl->txt('no_changes'), true);
			} else {	
				ilUtil::sendFailure($this->pl->txt('save_error'), true);
			}
			$this->configure();
		} else {
			$form->setValuesByPost();
			$this->DIC->ui()->mainTemplate()->setContent($form->getHtml());
		}
	}
}
?>

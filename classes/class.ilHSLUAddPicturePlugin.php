<?php
/**
 * Class ilHSLUAddPicturePlugin
 *
 *
 * @author  Stephan Winiker <stephan.winiker@hslu.ch>
 * @version 0.0.1
 */

include_once 'class.ilHSLUAddPictureConfig.php';

class ilHSLUAddPicturePlugin extends ilEventHookPlugin
{

    /**
     * @var
     */
    private $user;
    private $config;
    
    const PLUGIN_NAME = 'HSLUAddPicture';
    
    /**
     * @return string
     */
    public function getPluginName()
    {
        return self::PLUGIN_NAME;
    }

    /**
     * Handle the event
     *
     * @param    string        component, e.g. "Services/User"
     * @param    event         event, e.g. "afterUpdate"
     * @param    array         array of event specific parameters
     */
    public function handleEvent($a_component, $a_event, $a_parameter)
    {
        // Fetch profile picture from Evento, but only for testees, not for administrators/lecturers
        global $DIC;
        
        $this->user = $DIC->user();
        $this->config = ilHSLUAddPictureConfig::getInstance();
        
        if ($a_component == 'Services/Authentication' && $a_event == 'afterLogin' &&
                !$DIC->rbac()->review()->isAssigned($this->user->getId(), SYSTEM_ROLE_ID) &&
                ($external_account = $this->user->getExternalAccount()) &&
                strpos(ilObjUser::_getPersonalPicturePath($this->user->getId(), "small", false), 'data:image/svg+xml') !== false) {
            // Upload image
            $eventoid = substr($external_account, 0, strpos($external_account, '@'));
            if ($picture = $this->getPicture(array('parameters' => array('eventoId' => $eventoid)))) {
                $tmp_file = ilUtil::ilTempnam();
                imagepng(imagecreatefromstring($picture), $tmp_file, 0);
                ilObjUser::_uploadPersonalPicture($tmp_file, $this->user->getId());
                unlink($tmp_file);
            }
        }
    }
    
    /**
     * Retrieves a single record from the SOAP-interface
     * @param string $operation
     * @param array $params
     * @return array or false
     */
    private function getPicture($params)
    {
        $soap_client = new ilSoapClient(
            $this->config->get('ws_url')
        );
        
        if ($soap_client->init()) {
            $result = $soap_client->call('Login', array('parameters' => array('username' => $this->config->get('ws_user'), 'password' => $this->config->get('ws_password'))));
            if (isset($result->LoginResult) && $result->LoginResult != 'wrong credentials') {
                $params['parameters']['token'] = $result->LoginResult;
                $has_picture_result = $soap_client->call('HasPhoto', $params);
                
                if (isset($has_picture_result->HasPhotoResult) && $has_picture_result->HasPhotoResult === true) {
                    $picture_result = $soap_client->call('GetPhoto', $params);
                    return $picture_result->GetPhotoResult;
                } else {
                    return false;
                }
            }
        }
    }
}

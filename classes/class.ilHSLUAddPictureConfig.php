<?php
class ilHSLUAddPictureConfig {    
    private static $instance;
    private $conf;
    private $db;
    
    const VALID_NAMES = array(
    		'ws_url',
    		'ws_user',
    		'ws_password'
    );
    
    private function __construct() {
        global $DIC;
        $this->db = $DIC->database();
        if ($this->db->tableExists('evhk_hsluaddpic_conf')) {
            $this->readConf();
        }
    }
    
    public function get($name) {
    	if (in_array($name, self::VALID_NAMES) && isset($this->conf[$name])) {
    		return $this->conf[$name];
    	} else {
    		return '';
    	}
    }
    
    public static function getInstance() {
        if (! isset(self::$instance)) {
            self::$instance = new self();
        }
        
        return self::$instance;
    }
    
    public function saveConf($conf) {
        $r = 0;
        
        foreach ($conf as $name => $value) {
            if (in_array($name, self::VALID_NAMES)) {
                if($this->db->update(
                        'evhk_hsluaddpic_conf', array(
                            'value' => array('text', $value)
                        ),
                        array(
                            'name' => array('text', $name)
                        )
                    ) > 0) {
                        $r += 1;
                 }
                
            } else {
                return -1;
            }
        }
        $this->readConf();
        return $r;
    }
    
    private function readConf() {
        $q = $this->db->query("SELECT * FROM evhk_hsluaddpic_conf");
        
        while ($row = $this->db->fetchAssoc($q)) {
            $this->conf[$row['name']] = $row['value'];
        }
    }
}


<?php

namespace Application\Service;

use Zend\Session\Container;
use Exception;
use Zend\Db\Sql\Sql;
use Zend\Mail\Transport\Smtp as SmtpTransport;
use Zend\Mail\Transport\SmtpOptions;
use Zend\Mail;
use Zend\Mime\Message as MimeMessage;
use Zend\Mime\Part as MimePart;
use Zend\Mime\Mime;
use Zend\Mail\Transport\Sendmail;
use Zend\Db\Sql\Expression;

class CommonService {

    public $sm = null;

    public function __construct($sm = null) {
        $this->sm = $sm;
    }

    public function getServiceManager() {
        return $this->sm;
    }

    public static function generateRandomString($length = 8, $seeds = 'alphanum') {
        // Possible seeds
        $seedings['alpha'] = 'abcdefghijklmnopqrstuvwqyz';
        $seedings['numeric'] = '0123456789';
        $seedings['alphanum'] = 'abcdefghijklmnopqrstuvwqyz0123456789';
        $seedings['hexidec'] = '0123456789abcdef';

        // Choose seed
        if (isset($seedings[$seeds])) {
            $seeds = $seedings[$seeds];
        }

        // Seed generator
        list($usec, $sec) = explode(' ', microtime());
        $seed = (float) $sec + ((float) $usec * 100000);
        mt_srand($seed);

        // Generate
        $str = '';
        $seeds_count = strlen($seeds);

        for ($i = 0; $length > $i; $i++) {
            $str .= $seeds{mt_rand(0, $seeds_count - 1)};
        }

        return $str;
    }

    public function checkFieldValidations($params) {

        $adapter = $this->sm->get('Zend\Db\Adapter\Adapter');
        $tableName = $params['tableName'];
        $fieldName = $params['fieldName'];
        $value = trim($params['value']);
        $fnct = $params['fnct'];
        try {
            $sql = new Sql($adapter);
            if ($fnct == '' || $fnct == 'null') {
                $select = $sql->select()->from($tableName)->where(array($fieldName => $value));
                //$statement=$adapter->query('SELECT * FROM '.$tableName.' WHERE '.$fieldName." = '".$value."'");
                $statement = $sql->prepareStatementForSqlObject($select);
                $result = $statement->execute();
                $data = count($result);
            } else {
                $table = explode("##", $fnct);
                if ($fieldName == 'password') {
                    //Password encrypted
                    $config = new \Zend\Config\Reader\Ini();
                    $configResult = $config->fromFile(CONFIG_PATH . '/custom.config.ini');
                    $password = sha1($value . $configResult["password"]["salt"]);
                    $select = $sql->select()->from($tableName)->where(array($fieldName => $password, $table[0] => $table[1]));
                    $statement = $sql->prepareStatementForSqlObject($select);
                    $result = $statement->execute();
                    $data = count($result);
                } else {
                    // first trying $table[1] without quotes. If this does not work, then in catch we try with single quotes
                    //$statement=$adapter->query('SELECT * FROM '.$tableName.' WHERE '.$fieldName." = '".$value."' and ".$table[0]."!=".$table[1] );
                    $select = $sql->select()->from($tableName)->where(array("$fieldName='$value'", "$table[0]!=$table[1]"));
                    $statement = $sql->prepareStatementForSqlObject($select);
                    $result = $statement->execute();
                    $data = count($result);
                }
            }
            return $data;
        } catch (Exception $exc) {
            error_log($exc->getMessage());
            error_log($exc->getTraceAsString());
        }
    }

    public function dateFormat($date) {
        if (!isset($date) || $date == null || $date == "" || $date == "0000-00-00") {
            return "0000-00-00";
        } else {
            $dateArray = explode('-', $date);
            if (sizeof($dateArray) == 0) {
                return;
            }
            $newDate = $dateArray[2] . "-";

            $monthsArray = array('Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec');
            $mon = 1;
            $mon += array_search(ucfirst($dateArray[1]), $monthsArray);

            if (strlen($mon) == 1) {
                $mon = "0" . $mon;
            }
            return $newDate .= $mon . "-" . $dateArray[0];
        }
    }

    public function humanDateFormat($date) {
        if ($date == null || $date == "" || $date == "0000-00-00" || $date == "0000-00-00 00:00:00") {
            return "";
        } else {
            $dateArray = explode('-', $date);
            $newDate = $dateArray[2] . "-";

            $monthsArray = array('Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec');
            $mon = $monthsArray[$dateArray[1] - 1];

            return $newDate .= $mon . "-" . $dateArray[0];
        }
    }

    public function viewDateFormat($date) {

        if ($date == null || $date == "" || $date == "0000-00-00") {
            return "";
        } else {
            $dateArray = explode('-', $date);
            $newDate = $dateArray[2] . "-";

            $monthsArray = array('Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec');
            $mon = $monthsArray[$dateArray[1] - 1];

            return $newDate .= $mon . "-" . $dateArray[0];
        }
    }

    public function insertTempMail($to, $subject, $message, $fromMail, $fromName, $cc, $bcc) {
        $tempmailDb = $this->sm->get('TempMailTable');
        return $tempmailDb->insertTempMailDetails($to, $subject, $message, $fromMail, $fromName, $cc, $bcc);
    }

    public function sendTempMail() {
        try {
            $tempMailDb = $this->sm->get('TempMailTable');
            $config = new \Zend\Config\Reader\Ini();
            $configResult = $config->fromFile(CONFIG_PATH . '/custom.config.ini');
            $dbAdapter = $this->sm->get('Zend\Db\Adapter\Adapter');
            $sql = new Sql($dbAdapter);

            // Setup SMTP transport using LOGIN authentication
            $transport = new SmtpTransport();
            $options = new SmtpOptions(array(
                'host' => $configResult["email"]["host"],
                'port' => $configResult["email"]["config"]["port"],
                'connection_class' => $configResult["email"]["config"]["auth"],
                'connection_config' => array(
                    'username' => $configResult["email"]["config"]["username"],
                    'password' => $configResult["email"]["config"]["password"],
                    'ssl' => $configResult["email"]["config"]["ssl"],
                ),
            ));
            $transport->setOptions($options);
            $limit = '10';
            $mailQuery = $sql->select()->from(array('tm' => 'temp_mail'))->where("status='pending'")->limit($limit);
            $mailQueryStr = $sql->getSqlStringForSqlObject($mailQuery);
            $mailResult = $dbAdapter->query($mailQueryStr, $dbAdapter::QUERY_MODE_EXECUTE)->toArray();
            if (count($mailResult) > 0) {
                foreach ($mailResult as $result) {
                    $alertMail = new Mail\Message();
                    $id = $result['temp_id'];
                    $tempMailDb->updateTempMailStatus($id);
                
                    $fromEmail = $result['from_mail'];
                    $fromFullName = $result['from_full_name'];
                    $subject = $result['subject'];
                    
                    $alertMail->addFrom($fromEmail, $fromFullName);
                    $alertMail->addReplyTo($fromEmail, $fromFullName);
                    $alertMail->addTo($result['to_email']);
                    
                    if (isset($result['cc']) && trim($result['cc']) != "") {
                        $alertMail->addCc($result['cc']);
                    }

                    if (isset($result['bcc']) && trim($result['bcc']) != "") {
                        $alertMail->addBcc($result['bcc']);
                    }

                    $alertMail->setSubject($subject);
                    
                    $html = new MimePart($result['message']);
                    $html->type = "text/html";

                    $body = new MimeMessage();
                    $body->setParts(array($html));
                    
                    $dirPath = TEMP_UPLOAD_PATH . DIRECTORY_SEPARATOR . "email". DIRECTORY_SEPARATOR . $id;
                    if(is_dir($dirPath)) {
                        $dh  = opendir(TEMP_UPLOAD_PATH . DIRECTORY_SEPARATOR . "email". DIRECTORY_SEPARATOR . $id);
                        while (($filename = readdir($dh)) !== false) {
                            if ($filename != "." && $filename != "..") {
                                $fileContent = fopen($dirPath. DIRECTORY_SEPARATOR. $filename, 'r');
                                $attachment = new MimePart($fileContent);
                                $attachment->filename    = $filename;
                                $attachment->type        = Mime::TYPE_OCTETSTREAM;
                                $attachment->encoding    = Mime::ENCODING_BASE64;
                                $attachment->disposition = Mime::DISPOSITION_ATTACHMENT;
                                $body->addPart($attachment);
                            }
                        }
                        closedir($dh);
                        $this->removeDirectory($dirPath);
                    }
                    
                    $alertMail->setBody($body);
                    
                    $transport->send($alertMail);
                    $tempMailDb->deleteTempMail($id);
                }
            }
        } catch (Exception $e) {
            error_log($e->getMessage());
            error_log($e->getTraceAsString());
            error_log('whoops! Something went wrong in send-mail.');
        }
    }

    public function sendAuditMail() {
        try {
            $auditMailDb = $this->sm->get('AuditMailTable');
            $config = new \Zend\Config\Reader\Ini();
            $configResult = $config->fromFile(CONFIG_PATH . '/custom.config.ini');
            $dbAdapter = $this->sm->get('Zend\Db\Adapter\Adapter');
            $sql = new Sql($dbAdapter);

            // Setup SMTP transport using LOGIN authentication
            $transport = new SmtpTransport();
            $options = new SmtpOptions(array(
                'host' => $configResult["email"]["host"],
                'port' => $configResult["email"]["config"]["port"],
                'connection_class' => $configResult["email"]["config"]["auth"],
                'connection_config' => array(
                    'username' => $configResult["email"]["config"]["username"],
                    'password' => $configResult["email"]["config"]["password"],
                    'ssl' => $configResult["email"]["config"]["ssl"],
                ),
            ));
            $transport->setOptions($options);
            $limit = '10';
            $mailQuery = $sql->select()->from(array('a_mail' => 'audit_mails'))->where("status='pending'")->limit($limit);
            $mailQueryStr = $sql->getSqlStringForSqlObject($mailQuery);
            $mailResult = $dbAdapter->query($mailQueryStr, $dbAdapter::QUERY_MODE_EXECUTE)->toArray();
            if (count($mailResult) > 0) {
                foreach ($mailResult as $result) {
                    $alertMail = new Mail\Message();
                    $id = $result['mail_id'];
                    $auditMailDb->updateInitialAuditMailStatus($id);
                    
                    $fromEmail = $result['from_mail'];
                    $fromFullName = $result['from_full_name'];
                    $subject = $result['subject'];
                    
                    $alertMail->addFrom($fromEmail, $fromFullName);
                    $alertMail->addReplyTo($fromEmail, $fromFullName);
                    $alertMail->addTo($result['to_email']);
                    
                    if (isset($result['cc']) && trim($result['cc']) != "") {
                        $alertMail->addCc($result['cc']);
                    }

                    if (isset($result['bcc']) && trim($result['bcc']) != "") {
                        $alertMail->addBcc($result['bcc']);
                    }

                    $alertMail->setSubject($subject);
                    
                    $html = new MimePart($result['message']);
                    $html->type = "text/html";

                    $body = new MimeMessage();
                    $body->setParts(array($html));
                    
                    $dirPath = TEMP_UPLOAD_PATH . DIRECTORY_SEPARATOR . "audit-email". DIRECTORY_SEPARATOR . $id;
                    if(is_dir($dirPath)) {
                        $dh  = opendir(TEMP_UPLOAD_PATH . DIRECTORY_SEPARATOR . "audit-email". DIRECTORY_SEPARATOR . $id);
                        while (($filename = readdir($dh)) !== false) {
                            if ($filename != "." && $filename != "..") {
                                $fileContent = fopen($dirPath. DIRECTORY_SEPARATOR. $filename, 'r');
                                $attachment = new MimePart($fileContent);
                                $attachment->filename    = $filename;
                                $attachment->type        = Mime::TYPE_OCTETSTREAM;
                                $attachment->encoding    = Mime::ENCODING_BASE64;
                                $attachment->disposition = Mime::DISPOSITION_ATTACHMENT;
                                $body->addPart($attachment);
                            }
                        }
                        closedir($dh);
                        $this->removeDirectory($dirPath);
                    }
                    
                    $alertMail->setBody($body);
                    
                    if($transport->send($alertMail)){
                       $auditMailDb->updateAuditMailStatus($id);
                    }
                }
            }
        } catch (Exception $e) {
            error_log($e->getMessage());
            error_log($e->getTraceAsString());
            error_log('whoops! Something went wrong in send-audit-mail.');
        }
    }
    
    public static function getDate($timezone = 'Asia/Calcutta') {
        $date = new \DateTime(date('Y-m-d'), new \DateTimeZone($timezone));
        return $date->format('Y-m-d');
    }

    public static function getDateTime($timezone = 'Asia/Calcutta') {
        $date = new \DateTime(date('Y-m-d H:i:s'), new \DateTimeZone($timezone));
        return $date->format('Y-m-d H:i:s');
    }

    public static function getCurrentTime($timezone = 'Asia/Calcutta') {
        $date = new \DateTime(date('Y-m-d H:i:s'), new \DateTimeZone($timezone));
        return $date->format('H:i');
    }

    public function checkMultipleFieldValidations($params) {
        $adapter = $this->sm->get('Zend\Db\Adapter\Adapter');
        $jsonData = $params['json_data'];
        $tableName = $jsonData['tableName'];
        $sql = new Sql($adapter);
        $select = $sql->select()->from($tableName);
        foreach ($jsonData['columns'] as $val) {
            if ($val['column_value'] != "") {
                $select->where($val['column_name'] . "=" . "'" . $val['column_value'] . "'");
            }
        }
        //edit
        if (isset($jsonData['tablePrimaryKeyValue']) && $jsonData['tablePrimaryKeyValue'] != null && $jsonData['tablePrimaryKeyValue'] != "null") {
            $select->where($jsonData['tablePrimaryKeyId'] . "!=" . $jsonData['tablePrimaryKeyValue']);
        }
        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();
        $data = count($result);
        return $data;
    }
    
    public function getAllConfig($params){
        $configDb = $this->sm->get('GlobalTable');
        return $configDb->fetchAllConfig($params);      
    }
    public function getGlobalConfigDetails(){
        $globalDb = $this->sm->get('GlobalTable');
        return $globalDb->getGlobalConfig();        
    }
    
    public function updateConfig($params) {
        $container = new Container('alert');
        $adapter = $this->sm->get('Zend\Db\Adapter\Adapter')->getDriver()->getConnection();
        $adapter->beginTransaction();
        try {
            $globalDb = $this->sm->get('GlobalTable');
            $updateRes = $globalDb->updateConfigDetails($params);
            $subject = '';
            $eventType = 'global-config-update';
            $action = 'updated global config details';
            $resourceName = 'global-config-update';
            $eventLogDb = $this->sm->get('EventLogTable');
            $eventLogDb->addEventLog($subject, $eventType, $action, $resourceName);
            $adapter->commit();
            $container->alertMsg ="Global Config Updated Successfully.";
        }catch (Exception $exc) {
            error_log($exc->getMessage());
            error_log($exc->getTraceAsString());
        }
    }
    
    public function dbBackup() {
        try {
            $configResult = include(CONFIG_PATH . '/autoload/local.php');
            $dbUsername = $configResult["db"]["username"];
            $dbPassword = $configResult["db"]["password"];
            $dbName = $configResult["db"]["data-base-name"];
            $dbHost = $configResult["db"]["data-base-host"];
            $folderPath = BACKUP_PATH . DIRECTORY_SEPARATOR;

            if (!file_exists($folderPath) && !is_dir($folderPath)) {
                mkdir($folderPath);
            }
            $currentDate = date("d-m-Y-H-i-s");
            $file = $folderPath . 'odkdash-dbdump-' . $currentDate . '.sql';
            $command = sprintf("mysqldump -h %s -u %s --password='%s' -d %s --skip-no-data > %s", $dbHost, $dbUsername, $dbPassword, $dbName, $file);
            exec($command);

            $days = 30;
            if (is_dir($folderPath)) {
                $dh = opendir($folderPath);
                while (($oldFileName = readdir($dh)) !== false) {
                    if ($oldFileName == 'index.php' || $oldFileName == "." || $oldFileName == ".." || $oldFileName == "") {
                        continue;
                    }
                    $file = $folderPath . $oldFileName;
                    if (time() - filemtime($file) > (86400) * $days) {
                        unlink($file);
                    }
                }
                closedir($dh);
            }
        } catch (Exception $exc) {
            error_log($exc->getMessage());
            error_log($exc->getTraceAsString());
            error_log('whoops! Something went wrong in cron/dbBackup');
        }
    }
    
    function removeDirectory($dirname) {
        // Sanity check
        if (!file_exists($dirname)) {
            return false;
        }

        // Simple delete for a file
        if (is_file($dirname) || is_link($dirname)) {
            return unlink($dirname);
        }

        // Loop through the folder
        $dir = dir($dirname);
        while (false !== $entry = $dir->read()) {
            // Skip pointers
            if ($entry == '.' || $entry == '..') {
                continue;
            }

            // Recurse
            $this->removeDirectory($dirname . DIRECTORY_SEPARATOR . $entry);
        }

        // Clean up
        $dir->close();
        return rmdir($dirname);
    }
    
    public function getAllActiveCountries(){
        $dbAdapter = $this->sm->get('Zend\Db\Adapter\Adapter');
        $sql = new Sql($dbAdapter);
        $countryQuery = $sql->select()->from(array('c' => 'country'))->where("c.country_status='active'");
        $countryQueryStr = $sql->getSqlStringForSqlObject($countryQuery);
        return $dbAdapter->query($countryQueryStr, $dbAdapter::QUERY_MODE_EXECUTE)->toArray();
    }
    
    public function getAllProvinces($selectedCountries){
        $dbAdapter = $this->sm->get('Zend\Db\Adapter\Adapter');
        $sql = new Sql($dbAdapter);
        $provinceQuery = $sql->select()->from(array('l_d' => 'location_details'))
                                       ->join(array('c'=>'country'),'c.country_id=l_d.country',array('country_name'))
                                       ->where("l_d.parent_location='0'");
        if(isset($selectedCountries) && !empty($selectedCountries)){
            $provinceQuery = $provinceQuery->where('l_d.country IN (' . implode(',',$selectedCountries) . ')');
        }
        $provinceQueryStr = $sql->getSqlStringForSqlObject($provinceQuery);
        return $dbAdapter->query($provinceQueryStr, $dbAdapter::QUERY_MODE_EXECUTE)->toArray();
    }
    
    public function getAllDistricts($selectedProvinces){
        $dbAdapter = $this->sm->get('Zend\Db\Adapter\Adapter');
        $sql = new Sql($dbAdapter);
        $districtQuery = $sql->select()->from(array('l_d' => 'location_details'))
                                       ->join(array('l_d_r'=>'location_details'),'l_d_r.location_id=l_d.parent_location',array('region_name'=>'location_name'))
                                       ->where("l_d.parent_location !='0'");
        if(isset($selectedProvinces) && !empty($selectedProvinces)){
            $districtQuery = $districtQuery->where('l_d.parent_location IN (' . implode(',',$selectedProvinces) . ')');
        }
        $districtQueryStr = $sql->getSqlStringForSqlObject($districtQuery);
        return $dbAdapter->query($districtQueryStr, $dbAdapter::QUERY_MODE_EXECUTE)->toArray();
    }
    
    public function getCountryLocations($params){
        $dbAdapter = $this->sm->get('Zend\Db\Adapter\Adapter');
        $sql = new Sql($dbAdapter);
        $countryLocations = array();
        $provinceQuery = $sql->select()->from(array('l_d' => 'location_details'))->where("l_d.parent_location ='0'");
        if(isset($params['country']) && !empty($params['country'])){
            $provinceQuery = $provinceQuery->where('l_d.country IN (' . implode(',',$params['country']) . ')');
        }
        $provinceQueryStr = $sql->getSqlStringForSqlObject($provinceQuery);
        $countryLocations['province'] = $dbAdapter->query($provinceQueryStr, $dbAdapter::QUERY_MODE_EXECUTE)->toArray();
        
        $districtQuery = $sql->select()->from(array('l_d' => 'location_details'))->where("l_d.parent_location !='0'");
        if(isset($params['country']) && !empty($params['country'])){
            $districtQuery = $districtQuery->where('l_d.country IN (' . implode(',',$params['country']) . ')');
        }
        $districtQueryStr = $sql->getSqlStringForSqlObject($districtQuery);
        $countryLocations['district'] = $dbAdapter->query($districtQueryStr, $dbAdapter::QUERY_MODE_EXECUTE)->toArray();
      return $countryLocations;
    }
    
    public function getProvinceDistricts($params){
        $dbAdapter = $this->sm->get('Zend\Db\Adapter\Adapter');
        $sql = new Sql($dbAdapter);
        $districtQuery = $sql->select()->from(array('l_d' => 'location_details'))->where("l_d.parent_location !='0'");
        if(isset($params['province']) && !empty($params['province'])){
            $districtQuery = $districtQuery->where('l_d.parent_location IN (' . implode(',',$params['province']) . ')');
        }
        $districtQueryStr = $sql->getSqlStringForSqlObject($districtQuery);
        return $dbAdapter->query($districtQueryStr, $dbAdapter::QUERY_MODE_EXECUTE)->toArray();
    }
    
    public function saveRegion($region){
        $container = new Container('alert');
        $adapter = $this->sm->get('Zend\Db\Adapter\Adapter')->getDriver()->getConnection();
        $adapter->beginTransaction();
        try {
            $locationDetailsDb = $this->sm->get('LocationDetailsTable');
            $response = $locationDetailsDb->saveRegion($region);
            if ($response > 0) {
                $adapter->commit();
                //<-- Event log
                $id = (int) $region->location_id;
                if($id == 0){
                    $subject = $response;
                    $eventType = 'region-add';
                    $action = 'added a new region '.$region->location_name;
                    $resourceName = 'Region';
                    $eventLogDb = $this->sm->get('EventLogTable');
                    $eventLogDb->addEventLog($subject,$eventType,$action,$resourceName);
                    $container->alertMsg = 'Region added successfully';
                }else{
                    $subject = $response;
                    $eventType = 'region-update';
                    $action = 'updates a region '.$region->location_name;
                    $resourceName = 'Region';
                    $eventLogDb = $this->sm->get('EventLogTable');
                    $eventLogDb->addEventLog($subject,$eventType,$action,$resourceName);
                    $container->alertMsg = 'Region updated successfully'; 
                }
            }else{
               $container->alertMsg = 'Oops..';
            }
        } catch (Exception $exc) {
            error_log($exc->getMessage());
            error_log($exc->getTraceAsString());
        }
    }
    
    public function saveDistrict($district){
       $container = new Container('alert');
        $adapter = $this->sm->get('Zend\Db\Adapter\Adapter')->getDriver()->getConnection();
        $adapter->beginTransaction();
        try {
            $locationDetailsDb = $this->sm->get('LocationDetailsTable');
            $response = $locationDetailsDb->saveDistrict($district);
            if ($response > 0) {
                $adapter->commit();
                //<-- Event log
                $id = (int) $district->location_id;
                if($id == 0){
                    $subject = $response;
                    $eventType = 'district-add';
                    $action = 'added a new district '.$district->location_name;
                    $resourceName = 'District';
                    $eventLogDb = $this->sm->get('EventLogTable');
                    $eventLogDb->addEventLog($subject,$eventType,$action,$resourceName);
                    $container->alertMsg = 'District added successfully';
                }else{
                    $subject = $response;
                    $eventType = 'district-update';
                    $action = 'updates a district '.$district->location_name;
                    $resourceName = 'District';
                    $eventLogDb = $this->sm->get('EventLogTable');
                    $eventLogDb->addEventLog($subject,$eventType,$action,$resourceName);
                    $container->alertMsg = 'District updated successfully'; 
                }
            }else{
               $container->alertMsg = 'Oops..';
            }
        } catch (Exception $exc) {
            error_log($exc->getMessage());
            error_log($exc->getTraceAsString());
        }
    }
    
    public function getLocation($id){
        $locationDetailsDb = $this->sm->get('LocationDetailsTable');
        return $locationDetailsDb->getLocation($id);
    }
    
    public function deleteLocation($id){
        $locationDetailsDb = $this->sm->get('LocationDetailsTable');
        return $locationDetailsDb->delete(array('location_id'=>$id));
    }
}

?>
<?php

class managertaskscomponents {
    const INPUT_TYPE_NAME=1;
    const INPUT_TYPE_DELETE=2;
    const INPUT_TYPE_ADD=3;
    
    static public function processUpdate($originalElementID,$newValue,$splitResult) {        
        $sqlClient = new mysqlclient(__METHOD__);        
        $settingType = trim($splitResult[1]);

        
        switch ($settingType) {
            case(managertaskscomponents::INPUT_TYPE_NAME):                
                if ($newValue == '' || $newValue == null) break;
                $taskID = trim($splitResult[2]);                
                $sqlClient->query("UPDATE tbl_Tasks SET Name=\"".$newValue."\" WHERE PK_Task_ID=\"".$taskID."\"");                
                break;            
            case(managertaskscomponents::INPUT_TYPE_DELETE):
                $taskID = trim($splitResult[2]);          
                $deleteID=delete::createDelete("Delete Task");
                $sqlClient->query("UPDATE tbl_Tasks SET FK_Delete_ID=\"".$deleteID."\" WHERE PK_Task_ID=\"".$taskID."\"");
                echo "$('#task".$taskID."TableRow').remove();";                
                break;
            case(managertaskscomponents::INPUT_TYPE_ADD):                
                $addTaskName=trim($_GET["taskName"]);
                if ($addTaskName == '' || $addTaskName == null) break;
                $sqlClient->query("INSERT INTO tbl_Tasks SET Name=\"".$addTaskName."\"");
                $taskID=-999;
                $sqlClient->query("SELECT last_insert_id() AS PK_Task_ID");
                if ($sqlClient->getNextRow()) {
                    $taskID = $sqlClient->getValue("PK_Task_ID");
                }
                $sqlClient->freeResult();
                if($taskID!=-999) {
                    $managerTasksComponents=new managertaskscomponents();
                    $returnHTML=$sqlClient->escapeString($managerTasksComponents->getTask($taskID,$addTaskName,0));
                    echo "$('#tasksTable').append('".$returnHTML."');";
                }
                break;
            default:
                echo "Wrong setting type. You shouldn't o that...";
        }
    }
        
    var $sqlClient;
    
    public function __construct() {
        $_SESSION['key']="d7shdk5kwncsj5fs";
        $_SESSION['iv']="1234567890123456";
        $this->sqlClient = new mysqlclient(__METHOD__);                                
    }

    public function getTasksTable() {
        $returnHTML="";
        $returnHTML=$returnHTML."<table>";
        $returnHTML=$returnHTML."<thead>";
        $returnHTML=$returnHTML."<tr>";
        $returnHTML=$returnHTML."<th>Task Name</th><th>Hours Spent</th><th></th>";
        $returnHTML=$returnHTML."</tr>";
        $returnHTML=$returnHTML."</thead>";
        $returnHTML=$returnHTML."<tbody id=\"tasksTable\">";
        
        // $this->sqlClient->query("SELECT PK_Task_ID, Name, Hours_Spent FROM tbl_Tasks, tbl_task_hours WHERE FK_Delete_ID=-999 ORDER BY Name");
        $this->sqlClient->query("SELECT PK_Task_ID, Name, sum( IFNULL(Hours_Spent,0) ) AS Hours_Sum FROM tbl_Tasks LEFT JOIN tbl_task_hours ON PK_Task_ID=FK_Task_ID WHERE FK_Delete_ID=-999 GROUP BY Name ORDER BY Name");
        while($this->sqlClient->getNextRow()) {
            $returnHTML=$returnHTML.$this->getTask($this->sqlClient->getValue("PK_Task_ID"),$this->sqlClient->getValue("Name"),$this->sqlClient->getValue("Hours_Sum"));
        }
        $this->sqlClient->freeResult();
        
        $returnHTML=$returnHTML."</tbody>";
        $returnHTML=$returnHTML."<tfoot>";
        $returnHTML=$returnHTML."<tr id=\"taskAdd\">";        
        $returnHTML=$returnHTML."<td><input type=\"text\" id=\"taskName\" placeholder='add task'/></td>";
        $returnHTML=$returnHTML."<td></td>";
        // $settingID = bin2hex(mcrypt_encrypt(MCRYPT_RIJNDAEL_128, $_SESSION['key'], globalconstants::PAGE_UPDATE_TASKS.",".managertaskscomponents::INPUT_TYPE_ADD, MCRYPT_MODE_CBC, $_SESSION['iv']));
        $settingID = bin2hex(openssl_encrypt(globalconstants::PAGE_UPDATE_TASKS.",".managertaskscomponents::INPUT_TYPE_ADD,'AES-128-CBC', $_SESSION['key'], OPENSSL_RAW_DATA, $_SESSION['iv']));
        $returnHTML=$returnHTML."<td><button id=\"".$settingID."\" onClick=\"settingsChangeListenerMultiInput('".$settingID."','taskAdd')\" class='btn btn-primary'>Add</button></td>";
        $returnHTML=$returnHTML."</tr>";
        $returnHTML=$returnHTML."</tfoot>";
        $returnHTML=$returnHTML."</table>";
        return $returnHTML;
    }
    
    public function getTask($taskID,$name, $hoursSum) {
        $returnHTML="";
        $returnHTML=$returnHTML."<tr id=\"task".$taskID."TableRow\" name='".$name."'>";
        $returnHTML=$returnHTML."<td>";
        // $settingID = bin2hex(mcrypt_encrypt(MCRYPT_RIJNDAEL_128, $_SESSION['key'], globalconstants::PAGE_UPDATE_TASKS.",".managertaskscomponents::INPUT_TYPE_NAME.",".$taskID, MCRYPT_MODE_CBC, $_SESSION['iv']));
        $settingID = bin2hex(openssl_encrypt(globalconstants::PAGE_UPDATE_TASKS.",".managertaskscomponents::INPUT_TYPE_NAME.','.$taskID,'AES-128-CBC', $_SESSION['key'], OPENSSL_RAW_DATA, $_SESSION['iv']));
        $returnHTML = $returnHTML .  "<input type=\"text\" id=\"".$settingID."\" onchange=\"settingsChangeListener('".$settingID."')\" value=\"".$name."\" oldValue=\"".$name."\" /><br/>";
        $returnHTML=$returnHTML."</td>";
        $returnHTML=$returnHTML."<td>";
        $returnHTML=$returnHTML."<div>";
        $returnHTML=$returnHTML.$hoursSum;
        $returnHTML=$returnHTML."</div>";
        $returnHTML=$returnHTML."</td>";
        $returnHTML=$returnHTML."<td>";
        // $settingID = bin2hex(mcrypt_encrypt(MCRYPT_RIJNDAEL_128, $_SESSION['key'], globalconstants::PAGE_UPDATE_TASKS.",".  managertaskscomponents::INPUT_TYPE_DELETE . "," . $taskID, MCRYPT_MODE_CBC, $_SESSION['iv']));
        $settingID = bin2hex(openssl_encrypt(globalconstants::PAGE_UPDATE_TASKS.",".managertaskscomponents::INPUT_TYPE_DELETE.','.$taskID,'AES-128-CBC', $_SESSION['key'], OPENSSL_RAW_DATA, $_SESSION['iv']));
        $returnHTML=$returnHTML."<button id=\"".$settingID."\" onClick=\"settingsChangeListener('".$settingID."')\" class='btn btn-danger'>Delete</button>";
        $returnHTML=$returnHTML."</td>";
        $returnHTML=$returnHTML."</tr>";
        return $returnHTML;
    }
}

?>

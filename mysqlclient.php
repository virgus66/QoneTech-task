<?php

class mysqlclient {
    public static $clientCount;
    
    var $databaseConnection;
    var $result;
    var $resultRow;
    var $errorReason;
    var $location;
    var $databaseName;
    var $databaseServer;
    var $databaseUsername;
    var $databasePassword;
    
    static public function setClientCount($clientCount) {
        self::$clientCount=$clientCount;
    }
    
    static public function getClientCount() {        
        return self::$clientCount;
    }
    
    public function __construct($location,$databaseName=globalconstants::DATABASE_NAME,$databaseServer=globalconstants::DATABASE_SERVER,$databaseUsername=globalconstants::DATABASE_USERNAME,$databasePassword=globalconstants::DATABASE_PASSWORD) {
        /*if(globalconstants::DEBUG_MYSQL_CONNECTION_COUNT) {
            $this->location=$location;
            mysqlclient::setClientCount(mysqlclient::getClientCount()+1);
            echo "Create mysql: ".$location." (".mysqlclient::getClientCount().")<br/>";
        }
        $this->errorReason="";        
        $this->databaseConnection = mysqli_connect($databaseServer,$databaseUsername,$databasePassword,$databaseName,globalconstants::DATABASE_PORT);
        if (!$this->databaseConnection) {
            die('Could not connect to MySQL: ' . mysqli_connect_error());
        }
        mysqli_query($this->databaseConnection, 'SET NAMES \'utf8\'');*/
        $this->errorReason="";        
        $this->databaseConnection = null;
        $this->location=$location;
        $this->databaseName=$databaseName;
        $this->databaseServer=$databaseServer;
        $this->databaseUsername=$databaseUsername;
        $this->databasePassword=$databasePassword;
    }
    
    public function query($query) {
        if(globalconstants::DEBUG_DO_NOT_REQUIRE_DB) {
            $this->result=true;        
            return true;
        }
        if($this->databaseConnection==null) {
            if(globalconstants::DEBUG_MYSQL_CONNECTION_COUNT) {                
                mysqlclient::setClientCount(mysqlclient::getClientCount()+1);
                echo "Create mysql: ".$this->location." (".mysqlclient::getClientCount().")<br/>";
            }             
            $this->databaseConnection = mysqli_connect($this->databaseServer,$this->databaseUsername,$this->databasePassword,$this->databaseName,globalconstants::DATABASE_PORT);
            if (!$this->databaseConnection) {
                die('Could not connect to MySQL: ' . mysqli_connect_error());
            }
            mysqli_query($this->databaseConnection, 'SET NAMES \'utf8\'');
        }
        if(globalconstants::DEBUG_SQL) echo "SQL Query: ".$query."<br/>";
        $this->result = mysqli_query($this->databaseConnection,$query);
        if(!$this->result) {
            if(globalconstants::DEBUG_SQL) echo "Error for query: ".$query." is: ".mysqli_error($this->databaseConnection)." *** <br/>";
            $this->errorReason=$this->errorReason."Error for query: ".$query." is: ".mysqli_error($this->databaseConnection)." *** ";
        }
        return $this->result;
    }
    
    public function getNextRow() {
        if(globalconstants::DEBUG_DO_NOT_REQUIRE_DB) {
            if($this->result) {
                $this->result=false;
                return true;
            }
            return false;
        }            
        return(($this->resultRow = mysqli_fetch_array($this->result, MYSQLI_ASSOC))!=NULL);
    }
    
    public function getRowCount() {
        if(globalconstants::DEBUG_DO_NOT_REQUIRE_DB) return 1;
        return(mysqli_num_rows($this->result));
    }
    
    public function getAffectedRows() {
        if(globalconstants::DEBUG_DO_NOT_REQUIRE_DB) return 1;
        return(mysqli_affected_rows($this->databaseConnection));        
    }
    
    public function resetQuery() {
        if(globalconstants::DEBUG_DO_NOT_REQUIRE_DB) return true;
        mysqli_data_seek($this->result, 0 );
    }
    
    public function getValue($field) {
        if(globalconstants::DEBUG_DO_NOT_REQUIRE_DB) return "DEBUG";
        return $this->resultRow[$field];
    }
    
    public function getMySQLValueIncNull($field) {
        if(globalconstants::DEBUG_DO_NOT_REQUIRE_DB) return 1;
        if(is_null($this->resultRow[$field])) return "NULL"; else return $this->resultRow[$field];
    }
    
    public function getTableValue($field) {
        if(globalconstants::DEBUG_DO_NOT_REQUIRE_DB) return "DEBUG";
        if($this->resultRow[$field]=="") return "&nbsp"; else return $this->resultRow[$field];
    }
    
    public function getError() {
        if(globalconstants::DEBUG_DO_NOT_REQUIRE_DB) return "";
        return $this->errorReason;
    }
    
    public function freeResult() {        
        if(globalconstants::DEBUG_DO_NOT_REQUIRE_DB) return true;
        mysqli_free_result($this->result);
        $this->result=null;
    }
    
    public function close() {    
        if(globalconstants::DEBUG_DO_NOT_REQUIRE_DB) return true;
        mysqli_close($this->databaseConnection);
        $this->databaseConnection=null;
    }    
    
    public function escapeString($string) {
        if(globalconstants::DEBUG_DO_NOT_REQUIRE_DB) return $string;
        if($this->databaseConnection==null) {
            if(globalconstants::DEBUG_MYSQL_CONNECTION_COUNT) {                
                mysqlclient::setClientCount(mysqlclient::getClientCount()+1);
                echo "Create mysql: ".$this->location." (".mysqlclient::getClientCount().")<br/>";
            }             
            $this->databaseConnection = mysqli_connect($this->databaseServer,$this->databaseUsername,$this->databasePassword,$this->databaseName,globalconstants::DATABASE_PORT);
            if (!$this->databaseConnection) {
                die('Could not connect to MySQL: ' . mysqli_connect_error());
            }
            mysqli_query($this->databaseConnection, 'SET NAMES \'utf8\'');
        }
        return mysqli_real_escape_string($this->databaseConnection,$string);
    }
    
    public function __destruct() {
        if(globalconstants::DEBUG_DO_NOT_REQUIRE_DB) return true;
        if(globalconstants::DEBUG_MYSQL_CONNECTION_COUNT) {
            if($this->databaseConnection!=null) {
                mysqlclient::setClientCount(mysqlclient::getClientCount()-1);
                echo "Delete mysql: ".$this->location." (".mysqlclient::getClientCount().")<br/>";
            }
        }
        if($this->result!=null && $this->result!=true && $this->result!=false) $this->freeResult ();
        if($this->databaseConnection!=null) $this->close ();
    }        
}

?>

<?php
class delete {
    static function createDelete($description) {
        $delete=new mysqlclient(__METHOD__);
        $deleteID=-999;
        $delete->query("INSERT INTO tbl_Deletes SET Description=\"".$description."\"");
        $delete->query("SELECT last_insert_id() AS PK_Delete_ID");
        if($delete->getNextRow()) {
            $deleteID=$delete->getValue("PK_Delete_ID");
        }
        $delete->freeResult();
        return $deleteID;
    }        
}
    
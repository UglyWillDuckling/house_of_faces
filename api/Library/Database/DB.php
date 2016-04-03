<?php namespace Tricky\Database;

    use PDO;

    class DB extends PDO
    {

        protected $table;
        protected $result = array();

        public $success;

        public function __construct($db_type, $db, $host, $user, $pass){

            $dns = "$db_type:dbname=$db; host=$host";
            parent::__construct($dns, $user, $pass);
            $this->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        }

        public function findAll($rules = array()){

            $sql  = "SELECT * FROM " . $this->table . " ";

            foreach($rules as $rule => $value){

                $sql .= $rule . " " . $value . " ";
            }

            $stmt = $this->query($sql, PDO::FETCH_ASSOC);
            $rez  = $stmt->fetchAll();

        // ako je rez definiran njegova vrijednost se zadaje polju result
            $this->result = $rez ?: array();
            return $this;
        }//findAll()

        public function whereJoin($query, $fields){

            $rules = $query->rules;
            $freeRules = $query->freeRules;
            $joins = $query->joins;
            $conds = $query->conditions;

         //where   
            $where = $this->_makeWhere($rules, $freeRules);
            
        //conditions
            $condition = $this->_makeConditions($conds);

        //joins
            $joinSql = "";
            foreach($joins as $join)
            {
            //0 je tip joina, 1 je ime tablice, 2 i 3 su polja za usporedbu
                $joinSql .=
                $join['0'] . 
                " JOIN " . 
                $join[1] . 
                " ON " .
                $join[2] .
                "=" .
                $join[3] .
                " ";
            }

            $fieldsWanted = "";
            foreach($fields as $polje){

                $fieldsWanted .= $polje . ", ";
            }
            $fieldsWanted = trim($fieldsWanted, ", ");

            $sql =
                "SELECT "  . $fieldsWanted
                . " FROM " . $this->table
                . " " . $joinSql
                . " " . $where
                . " " . $condition
            ;

            //echo $sql . "<hr>";die;

            $sth = $this->prepare($sql);

            $v = 1;
            foreach($rules as $rule)
            {   
                $sth->bindValue($v , $rule[2]);
                $v++;
            }

            $sth->execute();
            $result = $sth->fetchAll(PDO::FETCH_ASSOC);

            if($result) $this->result = $result;
            else        $this->result = array();

            return $this;

        }//whereJoin()


        /**
         * [where description]
         * @param  [type] $rules [description]
         * @param  array  $conds [description]
         * @return [type]        [description]
         */
        public function where($query, $fields=array()){

          $rules     = $query->rules;
          $freeRules = $query->freeRules;
          $conds     = $query->conditions;

          $where = $this->_makeWhere($rules, $freeRules);
          $conditions = $this->_makeConditions($conds);  


          $wanted = "";
          if(!$fields)
               $wanted = "*"; 
          else{

            foreach($fields as $field)
            {
                $wanted .= $field . ", ";
            }
            $wanted = trim($wanted, ", ");
          }

          $sql = "SELECT " . $wanted . " FROM " . $this->table . " " . $where . " " . $conditions;

          $sth = $this->prepare($sql);

        //radimo bind parametara u slučaju da je on potreban
            $v=1;
            foreach($rules as $rule)
            {
                $sth->bindValue($v, $rule[2]);
                $v++;
            }  

            $sth->execute();
            $result = $sth->fetchAll(PDO::FETCH_ASSOC);

            if($result) $this->result = $result;
            else        $this->result = array();

            return $this;
        }//where


        /**
         * metoda za postavljanje trenutne tablice
         * @param [type] $table ime tablice
         */
        public function setTable($table){

            $this->table = $table;
            return $this;
        }//setTable()

        public function getAll()
        {
            return (!empty($this->result)) ? $this->result : array();
        }//getAll()


        /**
         * dodavanje novog redu u tablicu
         * @param [type] $row [description]
         */
        public function add($row, $noBindingColumns = array()){

            //uzimamo imena kolumni koje zelimo staviti u query
            $columns = array_keys($row);

            $stupci = "(";
            $values = "(";
            foreach($columns as $column){

                $stupci .= $column . ", ";
                $values .= ":" . $column . ", ";
            }
        //dopisujemo stupce koji ne zahtjevaju 'vezanje'(bind)
            foreach($noBindingColumns as $column => $val){

                $stupci .= $column . ", ";
                $values .= $val . ", ";
            }
            $stupci  = trim($stupci, ", ");
            $stupci .= ")";
            $values  = trim($values, ", ");

            $values .= ")";

            $sql = "INSERT INTO " . $this->table . $stupci . " VALUES " . $values;
            $sth = $this->prepare($sql);

           //die($sql);
            
            
            foreach($row as $stupac => $vrijednost){

                $sth->bindValue( ":{$stupac}", $vrijednost);
            }

            if(!$sth->execute())
            {
                throw new PDOException(
                    "nevaljan insert, pokusajte opet.<br>". print_r($sth->errorinfo())
                );
            }

            return $this;
        }//add


        /**
         * [set description]
         * @param [type] $setData    polja i podaci koje treba promijeniti
         *                           ili unijeti u bazu
         * @param array  $whereField imena polja u tablici koja se moraju
         *                           podudarati
         */
        public function set($setData, $whereFields = array()) //doradi imena argumenata
        {     
            $set = "SET ";
            foreach($setData as $field => $value)
            {
                $set .= $field . "=:" . $field . ", ";
            }
            $set = trim($set, ", ");         

            $where = "WHERE ";
            foreach($whereFields as $cond)
            {
                $where .= $cond[0] . $cond[1] . ":" . $cond[0];
            }

            $sql = "UPDATE " . $this->table . " " . $set . " " . $where;
            $stmt = $this->prepare($sql);

            foreach($setData as $field => $value)
            {
                $stmt->bindValue(":".$field, $value);
            }
            foreach($whereFields as $where)
            {
                $stmt->bindValue(":".$where[0], $where[2]);
            }

            if(!$stmt->execute())
            {
                throw new PDOException('bad update.<br>' . print_r($stmt->errorinfo()));
            }
            $this->success = ($stmt->rowCount()) ? true : false;
 
            return $this;
    }#\set()


    /**
     * brisanje stupca iz baze podataka
     * @param  [QueryObject] $rules objekt tipa QueryObject, pomoću njega
     *                              stvaramo where klauzulu
     * @return [object] $this       vraćamo sam db objekt              
     */
        public function delete($query){

            $rules = $query->rules;
            $freeRules = $query->freeRules;
            $where = $this->_makeWhere($rules, $freeRules);

            $sql = "DELETE FROM " . $this->table . " " . $where;
            $sth = $this->prepare($sql);

           // die($sql);

            $v = 1;
            foreach($rules as $rule){

                $sth->bindParam($v, $rule[2]);
                $v++;
            }
            if(!$sth->execute())
            {
                throw new PDOException("delete nije uspio.<br>" . $sth->errorinfo());
            } else{

                $this->success = true;
                return $this;
            }
        }//delete()


        public function startTransaction(){

            $this->query("start transaction;");
        }

        public function commit(){

            $this->query('commit;');
        }

        public function rollback(){

            $this->query('rollback;');
        }

        public function prvi(){

            if(!empty($this->result)){

                return $this->result[0];
            }
            return false;
        } //prvi()

        /**
         * [_makeWhere description]
         * @param  [type] $rules [description]
         * @return [type]        [description]
         */
        private function _makeWhere($rules, $fRules){

            $where = " WHERE ";
            foreach($rules as $rule){

            //rules[0] je polje u tablici, rules[1] je pravilo po kojem vrsimo usporedbu, rules[2] je vrijednost, rules[3] je
            // je nastavak za query (AND, OR ...) 

                if($rule['1'] != 'in')
                {
                    $cond = $rule[0] . " " . $rule[1] . " ?";
                    $where .= $cond;
                }
                else{

                //moramo vezati svaku vrijednost u in klauzuli
                    $cond = $rule[0] . " in (";

                    foreach($rule[2] as $val)
                    {   
                        $cond .= ":" . $val . ", ";

                    }
                    $cond = trim($cond, ", ");
                    $cond .= ")";

                    $where .= $cond;
                }       
                if( isset($rule[3]) ) $where .= " " . $rule[3] . " ";    
            }

            if(!empty($fRules)){
                foreach($fRules as $rule)
                {
                    $where .= " " . $rule[0] . $rule[1] . $rule[2]. ", ";
                }

            }
            
            return $where;
        }

        public function _makeConditions($conds){

            $condition = "";
             foreach($conds as $cond)
            {
                $condition .= $cond[0] . " " . $cond[1] . " ";
            }

            return $condition;
        }
        public function count(){
            return sizeof($this->result);
        }
    }//DB

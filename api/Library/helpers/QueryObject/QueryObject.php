<?php namespace Tricky\helpers\QueryObject;

    
        class QueryObject{

            public $rules, $freeRules, $joins, $conditions = array(); // free rules don't require bind
        

            public function setJoin($join, $name=""){

                if($name)
                    $this->joins[$name] = $join;
                else
                $this->joins[] = $join;
            }

            public function setCondition($conds, $name=""){

                 if($name)
                    $this->conditions[$name] = $conds;
                else
                $this->conditions[] = $conds;
            }

            public function setRule($rule, $name="")
            {
                foreach($rule as &$val)
                {
                    $val = clean($val);
                }
            //dodjeljujemo ime unesenom pravilu    
                $ruleName = $name ?: ( $rule[0] . count($this->rules) );

                $this->rules[$ruleName] = $rule;
            }

            public function setFreeRule($rule, $name="")
            {
                foreach($rule as &$val)
                {
                    $val = clean($val);
                }

                //dodjeljujemo ime unesenom pravilu    
                $name = $name ?: ( $rule[0] . count($this->rules) );

                $this->freeRules[$ruleName] = $rule;
            }

            public function clearRules(){
                $this->rules = array();
            }


            public function clearConditions(){
                $this->conditions = array();
            }

            public function clearJoins(){
                $this->joins = array();
            }
        }

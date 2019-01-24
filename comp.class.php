<?php
//Testing


/*
    Daniel Romano
    This class for scan HTML file and insert to DB
*/

class ScanFile {

    private $indexFile;     //The orignal code - HTML ()

    private $nowAction = "OUT"; //Action for finds vars

    private $db;



    function __construct()
    {
        //1. Find the vars and insert to DB
        //2. 

        $this->indexFile = file_get_contents($fileLocation);
        $this->db = new Database();
    }

    private function find()
    {
        
        
        $retString = "";
        
        $stringAsArray = str_split($this->indexFile);
        $varsArray = array();
        $tempVarName = "";
        for($i = 0; $i < count($stringAsArray); $i++)
        {
           
            if($this->nowAction == "INVAR") {
                if($stringAsArray[$i] != "]") {
                    $tempVarName .= $stringAsArray[$i];
                }else{
                    $this->nowAction = "END";
                }
            }

            if($this->nowAction == "START") {
                if($stringAsArray[$i] == "[") {
                    $this->nowAction = "START2";
                }else{
                    $this->nowAction = "OUT";
                }
            } elseif($this->nowAction == "START2") {
       
                if($stringAsArray[$i] == "$") {
                    $this->nowAction = "INVAR";
                   
                }else{
                    $this->nowAction = "OUT";
                }
            }
            

          
            if($this->nowAction == "END") {
                if($stringAsArray[$i] == "]") {
                    array_push($varsArray, $tempVarName);                  
                    $tempVarName = "";
                    $this->nowAction = "OUT";
                }else{
                    //Error
                }

                
            }

            if($this->nowAction == "OUT") {
                if($stringAsArray[$i] == "[")
                {
                    $this->nowAction = "START";
                }
            }



            
 
        }

        return $varsArray;
        
    }


    //This function call to the function find and loking for new vars and insert the vars as json to DB and Create the HTML file in the server
    public function insert()
    {
        $vars = $this->find();
        $onlyNewVars = array("newObjs" => array());

        foreach($vars as $var) {
            if(!$this->specialString($var)) {
                array_push($onlyNewVars["newObjs"], array($var => ""));
            }
        }

        $onlyNewVars = json_encode($onlyNewVars);
        //Insert that strging to DB, and change the value evry version you make.



        //upload this index HTML file

        $fileLocation = "files";
        $token = "dsadsdadsad askjs djljdskljsd ljkaj sdkl";
        $fileName = md5(base64_encode(sha1(time())) . $token) .".html";
        $path = $fileLocation ."/". $fileName;
        while(file_exists($path)) {
            $fileName = md5(base64_encode(sha1(time())) . $token) .".html";
        }
        $f = fopen($path, "w");
        fwrite($f, $this->indexFile);
        fclose($f);





        //Insert to DB function here


        $data = array(
            "vars" => $onlyNewVars,
            "fileName" => $fileName,
            "name" => "No Name"
        );

        $sql = "INSERT INTO `land` (vars, filename, name) VALUES ('". $data['vars'] ."', '". $data['fileName'] ."', '". $data['name'] ."')";

        // echo $sql;
       $this->db->query($sql);









        
        
        return $onlyNewVars;
    }


    private function findAndReplace()
    {
        
    }

    private function doSpecial()
    {
        
    }


    private function specialString($string)
    {
        $special = array("form");
        if(in_array($string, $special)) {
            return true;
        }
        return false;
    }

    //return the final code
    public function code()
    {
        return $this->finalString;
    }



}



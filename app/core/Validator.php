<?php
namespace app\core;

class Validator{
    protected array $errors = [];

    public function __construct(){
        $this->errors = [];
    }

    public function validate($data, $rules){
        foreach($rules as $field => $rule){
            $rules = explode('|', $rule);
            foreach($rules as $rule){
                $this->validateRule($field, $rule, $data);
            }
        }
    }

    public function validateRule($field, $rule, $data){
        if($rule == 'required'){
            if(empty($data[$field])){
                $this->errors[$field][] = 'The ' . $field . ' field is required';
            }
        }
        if($rule == 'email'){
            if(!filter_var($data[$field], FILTER_VALIDATE_EMAIL)){
                $this->errors[$field][] = 'The ' . $field . ' field must be a valid email address';
            }
        }
        if($rule == 'min'){
            if(strlen($data[$field]) < 6){
                $this->errors[$field][] = 'The ' . $field . ' field must be at least 6 characters long';
            }
        }
    }

    public function fails(){
        return !empty($this->errors);
    }

    public function getErrors(){
        return $this->errors;
    }
}
?>
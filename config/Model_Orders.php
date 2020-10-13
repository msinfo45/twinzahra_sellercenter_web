<?

class ClassHuman
{
 private $nama;
 private $password;
 
 public function __construct($val="", $pass=""){
 $this->nama=$val;
 $this->password=$pass;
 }
 public function name(){
 return $this->nama;
 }
 public function password(){
 return $this->password;
 }
}
 
class classPlayer extends ClassHuman
{
 private $posisi;
 
 public function __construct($nama="", $pass="", $posisi=""){
 $this->posisi=$posisi;
 parent::__construct($nama, $pass);
 }
 
 public function position(){
 return $this->posisi;
 }
 
 public function get_info(){
 $result=parent::name() . " - " . $this->posisi;
 return $result;
 }
  
 public function __toString() {
 return $result=parent::name() . " - " . $this->posisi;
 }
}
 
?>
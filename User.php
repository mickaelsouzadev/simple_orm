<?php  

require 'Model.php';

class User extends Model 
{
	public $id;
	public $name;
	public $email;

	public function getId() {
		return $this->id;
	}

	public function getName() {
		return $this->name;
	}

	public function getEmail() {
		return $this->email;
	}

	public function setId($id) {
		$this->id = $id;
	}

	public function setName($name) {
		$this->name = $name;
	}

	public function setEmail($email) {
		$this->email = $email;
	}
}

$user = new User;

$user->setPropertiesByArry([
	'name'=>"Fulano de Tal",
	'email'=>"fulano@email.com"
]);

echo $user->insert->save();